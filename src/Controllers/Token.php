<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Vuetify2
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-MDUI2
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Token as TokenModel;
use MaterialDesignForum\Models\User as UserModel;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Plugins\Share;

class Token extends TokenModel
{
  // /**
  //  * 创建用户token
  //  * @param $user_id
  //  * @param $device
  //  * @return Token
  //  */
  // public static function CreateUserToken($user_id, $device = null)
  // {
  //   if ($device == null) {
  //     $device = $_SERVER['HTTP_USER_AGENT'];
  //   }
  //   //先查询token是否大于5个，如果大于5个则删除最早的一个
  //   $tokenCount = Token::where('user_id', $user_id)->count();
  //   if ($tokenCount >= 5) {
  //     $token = Token::where('user_id', $user_id)->orderBy('create_time', 'asc')->first();
  //     $token->delete();
  //   }

  //   $token = md5(uniqid());
  //   $expireTime = Share::ServerTime() + 3600 * 24 * 30;
  //   $token = new Token();
  //   $token->token = $token;
  //   $token->user_id = $user_id;
  //   $token->device = $device;
  //   $token->create_time = Share::ServerTime();
  //   $token->update_time = Share::ServerTime();
  //   $token->expire_time = $expireTime;
  //   $token->save();
  //   return $token;
  // }

  /**
   * 生成用户Token 仅允许用户在密码验证成功后调用
   * @param UserModel $user 用户模型
   * @return string token字符串
   */
  public static function SpawnUserToken($user): string
  {
    $device = $_SERVER['HTTP_USER_AGENT'];
    $token_text = md5($user->email . $user->password . $device);

    $token = self::where('user_id', '=', $user->user_id)
      ->first();

    $return_token = '';
    if ($token != null) {
      $update_token = self::where('user_id', '=', $user->user_id)
        ->update([
          'token' => $token_text,
          'device' => $device,
          'update_time' => Share::ServerTime(),
          'expire_time' => Share::ServerTime() + 86400 * 30
        ]);

      if ($update_token) {
        $return_token = $token_text;
      }
    } else {
      $token = new self();
      $token->token = $token_text;
      $token->user_id = $user->user_id;
      $token->device = $device;
      $token->create_time = Share::ServerTime();
      $token->update_time = Share::ServerTime();
      $token->expire_time = Share::ServerTime() + 86400 * 30;
      $token->save();

      $return_token = $token->token;
    }

    return $return_token;

    //先查询token是否大于5个，如果大于5个则删除最早的一个
    // $user_id = $user->user_id;
    // $tokenCount = Token::where('user_id', $user_id)->count();
    // if ($tokenCount >= 5) {
    //   $dtoken = Token::where('user_id', $user_id)->orderBy('create_time', 'asc')->first();
    //   $dtoken->delete();
    // }

    // $token_text = md5($user->email . $user->password . $device);
    // $expireTime = Share::ServerTime() + 86400 * 30;//Share::ServerTime() + 3600 * 24 * 30;
    // $token = new Token();
    // $token->token = $token_text;
    // $token->user_id = $user_id;
    // $token->device = $device;
    // $token->create_time = Share::ServerTime();
    // $token->update_time = Share::ServerTime();
    // $token->expire_time = $expireTime;
    // $token->save();
    // return $token_text;
  }
  /**
   * 通过token获取用户ID 经过验证
   * 【※警告：基于UserGroup的类不可以使用，会死循环。】
   * @param string $token token字符串
   * @return int|null 用户ID 从Token表中获取user_id 之后可做其它比较
   */
  public static function GetUserId($token)
  {
    if($token == null) return null;
    if($token == '') return null;
    $query_token = self::where('token', '=', $token)
      ->where('expire_time', '>', Share::ServerTime())
      ->first();
    if ($query_token != null) {
      $user_id = $query_token->user_id;
      $user = UserModel::where('user_id', '=', $user_id)->first();
      $device = $_SERVER['HTTP_USER_AGENT'];
      if ($user != null) {
        $user_token = md5($user->email . $user->password . $device);
        if (
          $user_token == $token && //用户token与传入token相同
          $query_token->device == $device && //查询到的token的设备与传入的设备相同
          $query_token->token == $user_token //查询到的token与用户token相同
        ) {
          if ($user->disable_time > Share::ServerTime()) { //如果禁用时间大于当前时间，说明未解除禁用
            return null;
          }
          if (UserGroupController::Ability($token, 'ability_normal_login') == false) { //如果用户组不允许前台登录
            return null;
          }
          return $user->user_id;
        } else {
          return null;
        }
      } else {
        return null;
      }
    }
    return null;
  }
  /**
   * 通过token获取用户信息 经过验证
   * @param string $token token字符串
   * @return UserModel|null 用户信息
   */
  public static function GetUser($token)
  {
    $user_id = self::GetUserId($token);
    if ($user_id) {
      $user = UserModel::where('user_id', '=', $user_id)->first();
      if ($user) {
        return $user;
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
  /**
   * 验证token是否是用户自己的 仅适用于用户
   * @param string $token token字符串
   * @param int $user_id 要与其对比验证的用户ID
   * @return bool $token->user_id == $target_user_id
   */
  public static function IsUserSelf($token, $target_user_id = null): bool
  {
    $token_user_id = self::GetUserId($token);
    if ($token_user_id == $target_user_id) {
      return true;
    } else {
      return false;
    }
  }
}
