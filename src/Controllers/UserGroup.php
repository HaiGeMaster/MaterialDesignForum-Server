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

use MaterialDesignForum\Models\User as UserModel;
use MaterialDesignForum\Models\UserGroup as UserGroupModel;

use MaterialDesignForum\Controllers\Token as TokenController;

use MaterialDesignForum\Plugins\Share;
// use MaterialDesignForum\Config\Config;

class UserGroup extends UserGroupModel
{
  /**
   * 验证token是否是管理员，这才是真正的管理员，可以增删改查
   * @param string $token token字符串
   * @return bool $user_group->is_admin
   */
  public static function IsAdmin($token): bool
  {
    if ($token == null || $token == '') {
      return false;
    }
    return self::Ability($token, 'is_admin');
  }
  /**
   * 验证token是否是能后台管理员登录，这不是真正的管理员，只是能登录后台查看用户组指定的权限数据
   * @param string $token token字符串
   * @return bool $user_group->ability_admin_login
   */
  public static function IsAdminLogin($token): bool
  {
    if ($token == null || $token == '') {
      return false;
    }
    return self::Ability($token, 'ability_admin_login');
  }
  /**
   * 获取用户token所在的用户组
   * @param string $token token字符串
   * @return UserGroup|null 用户组
   */
  public static function GetUserLocationGroup($token)
  {
    $user_group_id = TokenController::GetUser($token)->user_group_id;
    if ($user_group_id != null) {
      $user_group = self::where('user_group_id', '=', $user_group_id)->first();
      if ($user_group != null) {
        return $user_group;
      }
    } else {
      return null;
    }
    return null;
  }
  /**
   * 验证用户的权限是否满足 only_no项如果true则只有没有xx项目才能xx
   * @param string $token token字符串
   * @param string $name 权限名称
   * @param string $ability = [
   * @param string 'ability_normal_login',//用户前台登录
   * @param string 'ability_admin_login',//管理员登录
   * @param string 'ability_admin_manage_user_group',//管理员管理用户组
   * @param string 'ability_admin_manage_user',//管理员管理用户
   * @param string 'ability_admin_manage_topic',//管理员管理话题
   * @param string 'ability_admin_manage_question',//管理员管理问题
   * @param string 'ability_admin_manage_article',//管理员管理文章
   * @param string 'ability_admin_manage_comment',//管理员管理评论
   * @param string 'ability_admin_manage_answer',//管理员管理回答
   * @param string 'ability_admin_manage_reply',//管理员管理回复
   * @param string 'ability_admin_manage_report',//管理员管理举报
   * @param string 'ability_admin_manage_option',//管理员管理设置
   * @param string 'ability_create_article',//创建文章
   * @param string 'ability_create_question',//创建问题
   * @param string 'ability_create_answer',//创建回答
   * @param string 'ability_create_comment',//创建评论
   * @param string 'ability_create_reply',//创建回复
   * @param string 'ability_create_topic',//创建话题
   * @param string 'ability_edit_own_article',//编辑自己的文章
   * @param string 'ability_edit_own_question',//编辑自己的问题
   * @param string 'ability_edit_own_answer',//编辑自己的回答
   * @param string 'ability_edit_own_comment',//编辑自己的评论
   * @param string 'ability_edit_own_reply',//编辑自己的回复
   * @param string 'ability_edit_own_topic',//编辑自己的话题
   * @param string 'ability_delete_own_article',//删除自己的文章
   * @param string 'ability_delete_own_question',//删除自己的问题
   * @param string 'ability_delete_own_answer',//删除自己的回答
   * @param string 'ability_delete_own_comment',//删除自己的评论
   * @param string 'ability_delete_own_reply',//删除自己的回复
   * @param string 'ability_delete_own_topic',//删除自己的话题
   * @param string 'ability_edit_article_only_no_comment',//仅限文章没有评论的情况下才能编辑
   * @param string 'ability_edit_question_only_no_answer',//仅限问题没有回答的情况下才能编辑
   * @param string 'ability_edit_answer_only_no_comment',//仅限回答没有评论的情况下才能编辑
   * @param string 'ability_edit_question_only_no_comment',//仅限问题没有评论的情况下才能编辑
   * @param string 'ability_edit_comment_only_no_reply',//仅限评论没有回复的情况下才能编辑
   * @param string 'ability_edit_topic_only_no_article_or_question',//仅限话题没有文章或问题的情况下才能编辑
   * @param string 'ability_delete_article_only_no_comment',//仅限文章没有评论的情况下才能删除
   * @param string 'ability_delete_question_only_no_answer',//仅限问题没有回答的情况下才能删除
   * @param string 'ability_delete_answer_only_no_comment',//仅限回答没有评论的情况下才能删除
   * @param string 'ability_delete_question_only_no_comment',//仅限问题没有评论的情况下才能删除
   * @param string 'ability_delete_comment_only_no_reply',//仅限评论没有回复的情况下才能删除
   * @param string 'ability_delete_topic_only_no_article_or_question',//仅限话题没有文章或问题的情况下才能删除
   * @param string 'ability_edit_own_info'//编辑自己的个人信息
   * @param string 'ability_vote'//投票
   * @param string '];
   * @return bool 是否满足
   */
  public static function Ability($token, $name): bool
  {
    $ability = [
      'is_admin',
      'ability_normal_login',
      'ability_admin_login',
      'ability_admin_manage_user_group',
      'ability_admin_manage_user',
      'ability_admin_manage_topic',
      'ability_admin_manage_question',
      'ability_admin_manage_article',
      'ability_admin_manage_comment',
      'ability_admin_manage_answer',
      'ability_admin_manage_reply',
      'ability_admin_manage_report',
      'ability_admin_manage_option',
      'ability_create_article',
      'ability_create_question',
      'ability_create_answer',
      'ability_create_comment',
      'ability_create_reply',
      'ability_create_topic',
      'ability_edit_own_article',
      'ability_edit_own_question',
      'ability_edit_own_answer',
      'ability_edit_own_comment',
      'ability_edit_own_reply',
      'ability_edit_own_topic',
      'ability_delete_own_article',
      'ability_delete_own_question',
      'ability_delete_own_answer',
      'ability_delete_own_comment',
      'ability_delete_own_reply',
      'ability_delete_own_topic',
      'ability_edit_article_only_no_comment',
      'ability_edit_question_only_no_answer',
      'ability_edit_answer_only_no_comment',
      'ability_edit_question_only_no_comment',
      'ability_edit_comment_only_no_reply',
      'ability_edit_reply_only_no_reply',
      'ability_edit_topic_only_no_article_or_question',
      'ability_delete_article_only_no_comment',
      'ability_delete_question_only_no_answer',
      'ability_delete_answer_only_no_comment',
      'ability_delete_question_only_no_comment',
      'ability_delete_comment_only_no_reply',
      'ability_delete_reply_only_no_reply',
      'ability_delete_topic_only_no_article_or_question',
      'ability_edit_own_info',
      'ability_vote',
    ];
    if (!in_array($name, $ability)) {
      return false;
    }
    $user_id = null;

    if ($token == null || $token == '') {
      return false;
    }

    // die('token: ' . $token);//找到了token变成了1

    try {
      $user = TokenController::where('token', '=', $token)->first();
      // $user_id = TokenController::GetUserId($token);//会死循环
      if ($user != null) {
        $user_id = $user->user_id;
        $user_group_id = UserModel::where('user_id', '=', $user_id)->first()->user_group_id;
        $user_group = self::where('user_group_id', '=', $user_group_id)->first();
        if ($user_group != null) {
          if ($user_group->$name == true) {
            //如果
            return true;
          }
        }
      }
    } catch (\Exception $e) {
      //如果查询失败，返回false
      echo $user_id;
      echo $e->getMessage();
      return false;
    }
    return false;
  }
  /**
   * 获取用户的时间类权限 返回true则可以下一步操作
   * @param string $token token字符串
   * @param string $name 权限名称
   * @param string $ObjectCreationTime 对象创建时间
   * @param string $ability = [
   * @param string 'time_before_edit_article',//在多长时间前可编辑自己的文章（单位：分钟，0无限期）
   * @param string 'time_before_edit_question',//在多长时间前可编辑自己的问题（单位：分钟，0无限期）
   * @param string 'time_before_edit_answer',//在多长时间前可编辑自己的回答（单位：分钟，0无限期）
   * @param string 'time_before_edit_comment',//在多长时间前可编辑自己的评论（单位：分钟，0无限期）
   * @param string 'time_before_edit_reply',//在多长时间前可编辑自己的回复（单位：分钟，0无限期）
   * @param string 'time_before_edit_topic',//在多长时间前可编辑自己的话题（单位：分钟，0无限期）
   * @param string 'time_before_delete_article',//在多长时间前可删除自己的文章（单位：分钟，0无限期）
   * @param string 'time_before_delete_question',//在多长时间前可删除自己的问题（单位：分钟，0无限期）
   * @param string 'time_before_delete_answer',//在多长时间前可删除自己的回答（单位：分钟，0无限期）
   * @param string 'time_before_delete_comment',//在多长时间前可删除自己的评论（单位：分钟，0无限期）
   * @param string 'time_before_delete_reply',//在多长时间前可删除自己的回复（单位：分钟，0无限期）
   * @param string 'time_before_delete_topic',//在多长时间前可删除自己的话题（单位：分钟，0无限期）
   * @param string '];
   * @return int 权限值
   */
  public static function BeforeTime($token, $name, $ObjectCreationTimeStamp = null): bool
  {
    $time_before = [
      'time_before_edit_article',
      'time_before_edit_question',
      'time_before_edit_answer',
      'time_before_edit_comment',
      'time_before_edit_reply',
      'time_before_edit_topic',
      'time_before_delete_article',
      'time_before_delete_question',
      'time_before_delete_answer',
      'time_before_delete_comment',
      'time_before_delete_reply',
      'time_before_delete_topic'
    ];
    //如果$name不在$time_before中，返回false
    if (!in_array($name, $time_before)) {
      return false;
    }
    $user_id = TokenController::where('token', '=', $token)->first()->user_id;
    if ($user_id != null) {
      $user_group_id = UserModel::where('user_id', '=', $user_id)->first()->user_group_id;
      $user_group = self::where('user_group_id', '=', $user_group_id)->first();
      if ($user_group != null) {
        if ($user_group->$name == 0) {
          return true;
        } else {
          if ($ObjectCreationTimeStamp != null && $ObjectCreationTimeStamp != '') {
            $time = $user_group->$name; //$time等于多少分钟
            $time = $time * 60; //$time等于$time * 60秒 得到 共 秒
            $time1 = Share::ServerTime() - $ObjectCreationTimeStamp; //$time1 等于当前时间 - $ObjectCreationTimeStamp 得到 共 秒
            if ($time1 < $time) { //如果$time1小于$time
              return true;
            }
          }
        }
      }
    }
    return false;
  }
  /**
   * 获取用户组列表
   * @param int $user_group_id 用户组id
   * @param string $user_token 用户token
   * @return array
   */
  public static function GetUserGroup($user_group_id, $user_token)
  {
    $is_admin = self::IsAdmin($user_token);
    $data = null;
    if ($is_admin) {
      $data = self::where('user_group_id', '=', $user_group_id)->first();
    }
    return [
      'is_get' => $data != null,
      'user_group' => $data
    ];
  }
  /**
   * 获取用户组列表
   * @param string $order 排序方式
   * @param int $page 页码
   * @param string $user_token 用户Token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @return array
   */
  public static function GetUserGroups(
    $order,
    $page,
    $user_token,
    $per_page = 20,
    $search_keywords = '',
    $search_field = []
  ) {
    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    if ($search_field == []) {
      $search_field = self::$search_field;
    }
    $data = Share::HandleDataAndPagination(null);
    $is_admin = self::IsAdmin($user_token);
    $is_admin_login = self::IsAdminLogin($user_token); //允许可登录后台的用户查看
    if ($is_admin || $is_admin_login) {
      if ($search_keywords != '') {
        // $data = self::where($search_field, 'like', '%' . $search_keywords . '%')
        //   ->where('delete_time', '=', 0)
        //   ->orderBy($field, $sort)
        //   ->paginate($per_page, ['*'], 'page', $page);
        $data = self::where('delete_time', '=', 0)
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::orderBy($field, $sort)
          ->where('delete_time', '=', 0)
          ->paginate($per_page, ['*'], 'page', $page);
      }
      $data = Share::HandleDataAndPagination($data);
    }
    return $data;
  }
  /**
   * 获取用户组简介信息
   * @param int $user_group_id 用户组ID
   * @return UserGroup|null
   */
  public static function GetUserGroupInfo($user_group_id)
  {
    $data = self::where('user_group_id', '=', $user_group_id)->first();
    if ($data != null) {
      return [
        'user_group_id' => $data->user_group_id,
        'user_group_name' => $data->user_group_name,
        'user_group_description' => $data->user_group_description,
        'user_group_icon' => $data->user_group_icon,
        'user_group_icon_show' => $data->user_group_icon_show,
        'user_group_color' => $data->user_group_color,
      ];
    }
    return null;
  }
  /**
   * 添加用户组
   * @param array $user_group_data 用户组数据
   * @param string $user_token 用户Token
   * @return array
   */
  public static function AddUserGroup($user_group_data, $user_token)
  {
    $is_valid_content = $user_group_data != null && $user_token != '' && $user_group_data != '' && $user_token != '';
    $is_add = false;
    if ($is_valid_content) {
      if (self::Ability($user_token, 'ability_admin_manage_user_group') && self::IsAdmin($user_token)) {
        $user_group = new self;
        //使用遍历的方式
        foreach ($user_group_data as $key => $value) {
          //如果$user_group->$key的值是整数且$value的值是布尔值，那么$value的值就是0或1，所以要转换成整数
          // if (is_int($user_group->$key) && (is_bool($value) || ($value == 'true') || ($value == 'false'))) {
          //   $user_group->$key = is_bool($value) ? intval($value) : ($value == 'true' ? 1 : 0);
          // } else {
          //   $user_group->$key = $value;
          // }
          $user_group->$key = $value == 'true' ? 1 : ($value == 'false' ? 0 : $value);
        }
        $user_group->create_time = Share::ServerTime();
        $user_group->update_time = Share::ServerTime();
        $user_group->delete_time = 0;
        $is_add = $user_group->save();
      }
    }
    return [
      'is_add' => $is_add,
      // 'user_group' => self::GetUserGroupInfo($user_group->user_group_id),
      'user_group' => $user_group,
    ];
  }
  /**
   * 编辑用户组
   * @param int $user_group_id 用户组ID
   * @param array $user_group_data 用户组数据
   * @param string $user_token 用户Token
   * @return array
   */
  public static function EditUserGroup($user_group_id, $user_group_data, $user_token)
  {
    $is_valid_content = $user_group_id != null && $user_group_data != null && $user_token != '' && $user_group_id != '' && $user_group_data != '' && $user_token != '';
    $is_edit = false;
    if ($is_valid_content) {
      $user_group = self::where('user_group_id', '=', $user_group_id)
        ->where('delete_time', '=', 0)
        ->first();
      if ($user_group != null) {
        //管理用户组能力 self::Ability($user_token, 'ability_admin_manage_user_group')
        //使用遍历的方式//仅允许真正的管理员修改用户组
        if (self::Ability($user_token, 'ability_admin_manage_user_group') && self::IsAdmin($user_token)) {
          foreach ($user_group_data as $key => $value) {
            //如果$user_group->$key的值是整数且$value的值是布尔值，那么$value的值就是0或1，所以要转换成整数
            // if (is_int($user_group->$key) && (is_bool($value) || ($value == 'true') || ($value == 'false'))) {
            //   $user_group->$key = is_bool($value) ? intval($value) : ($value == 'true' ? 1 : 0);
            // } else {
            //   $user_group->$key = $value;
            // }
            $user_group->$key = $value == 'true' ? 1 : ($value == 'false' ? 0 : $value);
          }
          $user_group->update_time = Share::ServerTime();
          $is_edit = $user_group->save();
        }
      }
    }
    return [
      'is_edit' => $is_edit,
      // 'user_group' => self::GetUserGroupInfo($user_group_id),
      'user_group' => $user_group,
    ];
  }
  /**
   * 删除用户组
   * @param int $user_group_ids 用户组ID数组
   * @param string $user_token 用户Token
   * @return array
   */
  public static function DeleteUserGroups($user_group_ids, $user_token)
  {
    $is_valid_content = $user_group_ids != null && $user_token != '' && $user_group_ids != '' && $user_token != '';
    $is_delete = false;
    $user_groups = [];
    if ($is_valid_content) {
      if (self::Ability($user_token, 'ability_admin_manage_user_group') && self::IsAdmin($user_token)) {
        $is_delete = self::whereIn('user_group_id', $user_group_ids)
          ->where('delete_time', '=', 0)
          ->update([
            'delete_time' => Share::ServerTime(),
          ]);

        $user_groups = self::whereIn('user_group_id', $user_group_ids)
          ->where('delete_time', '=', 0)
          ->get();

        // foreach ($user_groups as $user_group) {
        //   $user_group->delete_time = Share::ServerTime();
        //   $user_group->save();
        // }
      }
    }
    return [
      'is_delete' => $is_delete,
      'delete_ids' => $user_group_ids,
      'data' => $user_groups,
    ];
  }
  /**
   * 将用户从一个用户组移动到另一个用户组，同时变更新旧用户组人数
   * @param int $user_group_id 用户组ID
   * @param int $user_id 用户ID
   * @return bool
   */
  public static function MoveUserGroup($user_group_id, $user_id): bool
  {
    $user_group = self::where('user_group_id', '=', $user_group_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($user_group != null) {
      $user = UserModel::where('user_id', '=', $user_id)
        ->where('disable_time', '=', 0)
        ->first();
      if ($user != null) {
        //首先保存旧的用户组ID
        $old_user_group_id = $user->user_group_id;
        //然后将用户组ID修改为新的用户组ID
        $user->user_group_id = $user_group_id;
        //如果修改成功
        if ($user->save()) {
          //然后将旧的用户组ID的用户组人数减1，最后将新的用户组ID的用户组人数加1
          // return self::SubUserGroupUserCount($old_user_group_id) &&
          //   self::AddUserGroupUserCount($user_group_id);
          // self::SubUserGroupUserCount($old_user_group_id);
          // self::AddUserGroupUserCount($user_group_id);
          self::UpdateUserGroupUserCount($old_user_group_id);
          self::UpdateUserGroupUserCount($user_group_id);
          return true;
        }
      }
    }
    return false;
  }
  // /**
  //  * 将多个用户从一个用户组移动到另一个用户组，同时变更新旧用户组人数
  //  * @param int $user_group_id 用户组ID
  //  * @param array $user_ids 用户ID数组
  //  * @return [bool,array]
  //  */
  // public static function MoveUserGroups($user_group_id, $user_ids): bool
  // {
  //   $user_group = self::where('user_group_id', '=', $user_group_id)
  //     // ->where('delete_time', '=', 0)
  //     ->first();
  //   if ($user_group != null) {
  //     $users = UserModel::whereIn('user_id', $user_ids)
  //       // ->where('disable_time', '=', 0)
  //       ->get();
  //     if ($users != null) {
  //       // //首先保存旧的用户组ID数组，可以用来撤销？
  //       // $old_user_group_id = [];
  //       // foreach ($users as $user) {
  //       //   // array_push($old_user_group_id, $user->user_group_id);
  //       //   //首先保存旧的用户组ID
  //       //   $old_user_group_id = $user->user_group_id;
  //       //   //然后将用户组ID修改为新的用户组ID
  //       //   $user->user_group_id = $user_group_id;
  //       //   //如果修改成功
  //       //   if ($user->save()) {
  //       //     //然后将旧的用户组ID的用户组人数减1，最后将新的用户组ID的用户组人数加1
  //       //     self::SubUserGroupUserCount($old_user_group_id);
  //       //     self::AddUserGroupUserCount($user_group_id);
  //       //     // self::UpdateUserGroupUserCount($old_user_group_id);
  //       //     // self::UpdateUserGroupUserCount($user_group_id);
  //       //   }
  //       // }

  //       $users_count = $users->count();
  //       $old_user_group_ids = $users->pluck('user_group_id')->toArray();
  //       $old_user_group_ids = array_unique($old_user_group_ids);
  //       $users->update([
  //         'user_group_id' => $user_group_id,
  //       ]);
  //       foreach($old_user_group_ids as $old_user_group_id)
  //       {
  //         if($old_user_group_id != $user_group_id)
  //         {
  //           self::SubUserGroupUserCount($old_user_group_id,$users_count);
  //         }
  //       }
  //       self::AddUserGroupUserCount($user_group_id,$users_count);

  //       return true;
  //     }
  //   }
  //   // return true;
  //   return false;
  // }
  /**
   * 将多个用户从一个用户组移动到另一个用户组，同时变更新旧用户组人数
   * @param int $user_group_id 目标用户组ID
   * @param array $user_ids 用户ID数组
   * @return bool 是否操作成功
   */
  public static function MoveUserGroups(int $user_group_id, array $user_ids): bool
  {
    // 查找目标用户组是否存在且未删除
    $user_group = self::where('user_group_id', $user_group_id)->first();
    if (!$user_group) {
      return false; // 目标用户组不存在
    }

    // 查找所有需要移动的用户
    $users = UserModel::whereIn('user_id', $user_ids)->get();

    if ($users->isEmpty()) {
      return false; // 没有找到要移动的用户
    }

    // 取出这些用户当前的 user_group_id（可能各不相同）
    $old_user_group_ids = $users->pluck('user_group_id')->filter()->unique()->values()->toArray();

    $users_count = $users->count();

    // 🔥 关键修改：使用查询构造器批量更新这些用户的 user_group_id
    UserModel::whereIn('user_id', $user_ids)
      ->update(['user_group_id' => $user_group_id]);

    // 遍历旧用户组，如果不同则减少对应用户组的人数
    foreach ($old_user_group_ids as $old_user_group_id) {
      if ($old_user_group_id != $user_group_id) {
        self::SubUserGroupUserCount($old_user_group_id, $users_count);
      }
    }

    // 增加新用户组的人数
    self::AddUserGroupUserCount($user_group_id, $users_count);

    return true;
  }
  /**
   * 将旧用户组里的所有用户移动到另一个用户组
   * @param int $old_user_group_id 旧用户组ID
   * @param int $user_group_id 新用户组ID
   * @return bool
   */
  public static function MoveAllUserGroupUsers($old_user_group_id, $user_group_id): bool
  {
    $is_move = false;
    // $is_move = UserModel::where('user_group_id', '=', $old_user_group_id)
    //   ->where('disable_time', '=', 0)
    //   ->update([
    //     'user_group_id' => $user_group_id,
    //   ]);
    $users = UserModel::where('user_group_id', '=', $old_user_group_id);
    // ->where('disable_time', '=', 0)
    // ->update([
    //   'user_group_id' => $user_group_id,
    // ]);
    if ($users) {
      $old_users_count = $users->count();
      $users->update([
        'user_group_id' => $user_group_id,
      ]);
      //然后将旧的用户组ID的用户组人数减1，最后将新的用户组ID的用户组人数加1
      self::SubUserGroupUserCount($old_user_group_id, $old_users_count);
      self::AddUserGroupUserCount($user_group_id, $old_users_count);
      // self::UpdateUserGroupUserCount($old_user_group_id);
      // self::UpdateUserGroupUserCount($user_group_id);
      return true;
    }
    return false;
  }
  /**
   * 更新正确的用户组人数-消耗性能！！！
   * @param int $user_group_id 用户组ID
   * @return bool
   */
  public static function UpdateUserGroupUserCount($user_group_id): bool
  {
    $user_count = UserModel::where('user_group_id', '=', $user_group_id)
      // ->where('disable_time', '=', 0)
      ->count();
    if ($user_count != null) {
      $is_update = self::where('user_group_id', '=', $user_group_id)
        // ->where('delete_time', '=', 0)
        ->first();
      if ($is_update) {
        if($is_update->user_group_user_count - $user_count != 0){
          $is_update->user_group_user_count = $user_count;
        }else if($is_update->user_group_user_count - $user_count < 0){
          $is_update->user_group_user_count = 0;
        }
        return $is_update->save();
        // return true;
      }
    }
    return false;
  }
}
