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

use MaterialDesignForum\Models\Oauth as OauthModel;
use MaterialDesignForum\Controllers\Token as TokenController;

class Oauth extends OauthModel
{
  /**
   * 添加或更新第三方用户信息
   * @param string $oauthName 第三方平台标识符
   * @param string $oauthUserId 第三方平台用户ID
   * @param string $oauthUserName 第三方平台用户名
   * @param string $oauthUserEmail 第三方平台用户邮箱
   * @param string $oauthSourceResponse 第三方平台返回的用户信息
   * @param int $userId 对应用户ID
   * @return OauthModel 返回添加或更新后的Oauth模型实例
   */
  public static function AddOauthUser($oauthName, $oauthUserId, $oauthUserName,$oauthUserEmail, $oauthSourceResponse, $userId)

  {
    // 获取用户ID
    // $userId = TokenController::GetUserId($user_token);
    if (!$userId) {
      return null; // 如果无法获取用户ID，返回null
    }

    // $oauthSourceResponse = json_encode($oauthSourceResponse);
    // 检查是否已存在相同的第三方用户
    $existingOauth = self::where('oauth_name', $oauthName)
      ->where('oauth_user_id', $oauthUserId)
      ->first();

    if ($existingOauth) {
      // 如果已存在，更新数据
      $existingOauth->oauth_user_name = $oauthUserName;
      $existingOauth->oauth_user_email = $oauthUserEmail;
      $existingOauth->oauth_source_response = $oauthSourceResponse;
      $existingOauth->save();
      return $existingOauth;
    }

    // 如果不存在，创建新的记录
    return self::create([
      'oauth_name' => $oauthName,
      'oauth_user_id' => $oauthUserId,
      'oauth_user_name' => $oauthUserName,
      'oauth_user_email' => $oauthUserEmail,
      'oauth_source_response' => $oauthSourceResponse,
      'user_id' => $userId
    ]);
  }
  /**
   * 根据第三方平台标识符和用户ID获取Oauth记录
   * @param string $oauthName 第三方平台标识符
   * @param string $oauthUserId 第三方平台用户ID
   * @return OauthModel|null 返回找到的Oauth模型实例或null
   */
  public static function GetOauthUser($oauthName, $oauthUserId)
  {
    return self::where('oauth_name', $oauthName)
      ->where('oauth_user_id', $oauthUserId)
      ->first();
  }
  /**
   * 根据用户token获取Oauth记录
   * @param string $user_token 用户token
   * @return OauthModel|null 返回找到的Oauth模型实例或null
   */
  public static function GetOauths($user_token)
  {
    // 获取用户ID
    $userId = TokenController::GetUserId($user_token);
    if (!$userId) {
      return [
        'is_get' => false,
        'data' => null
      ]; // 如果无法获取用户ID，返回空数据
    }

    // // 查找对应的所有Oauth记录
    // $data = self::where('user_id', $userId)->get();
    // return [
    //   'is_get' => $data!= null,
    //   'data' => $data
    // ];

    // 查找github的Oauth记录
    $github = self::where('user_id', $userId)
      ->where('oauth_name', 'github')
      ->first();
    // 查找microsoft的Oauth记录
    $microsoft = self::where('user_id', $userId)
      ->where('oauth_name', 'microsoft')
      ->first();
    // 查找google的Oauth记录
    $google = self::where('user_id', $userId)
      ->where('oauth_name', 'google')
      ->first();

    return [
      'is_get' => true,
      'data' => [
        'github' => $github,
        'microsoft' => $microsoft,
        'google' => $google
      ]
    ];
  }
  /**
   * 根据用户token和第三方平台标识符获取Oauth记录删除
   * @param string $user_token 用户token
   * @param string $oauth_id 第三方平台标识符
   * @return bool 返回是否删除成功
   */
  public static function DeleteOauth($user_token, $oauth_id)
  {
    $is_delete = false;
    // 获取用户ID
    $userId = TokenController::GetUserId($user_token);
    if ($userId) {
      // 查找对应的Oauth记录
      $oauth = self::where('user_id', $userId)
        ->where('oauth_id', $oauth_id)
        ->first();

      if ($oauth) {
        // 删除记录
        $is_delete = $oauth->delete();
      }
    }
    // 返回删除结果
    return [
      'is_delete' => $is_delete,
      // 'message' => $is_deleted ? '删除成功' : '删除失败或记录不存在'
    ];
  }
}
