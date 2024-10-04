<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Inbox as InboxModel;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Plugins\Share;
// protected $fillable = [
//   'inbox_id',
//   'sender_id',
//   'sender_type',
//   'receiver_id',
//   'content_markdown',
//   'content_rendered',
//   'create_time',
//   'read_time'
// ];
class Inbox extends InboxModel
{
  //角色类型：系统system，用户user_id
  //sender_type 发送类型: user_to_user、user_to_chat_group、system_to_user、system_to_user_group
  //接收类型: user_id、user_group_id、chat_group_id
  public static $sender_type = [
    'user_to_user',
    'user_to_chat_group',
    'system_to_user',
    'system_to_user_group'
  ];
  /**
   * 是否是有效的发送者类型
   * @param string $sender_type 发送者类型 user_to_user、user_to_chat_group、system_to_user、system_to_user_group
   * @return bool
   */
  public static function IsVaildSenderType($sender_type): bool
  {
    return array_search($sender_type, self::$sender_type) !== false;
  }
  /**
   * 用户获取来自其他用户的消息
   * @param string $user_token 用户Token
   * @param string $order 排序
   * @param int $page 页数
   * @param int $per_page 每页数量
   */
  public static function UserGetUserInbox($user_token, $order = '-create_time', $page = 1, $per_page = 10)
  {
    $user_id = TokenController::GetUserId($user_token);
    if($user_id == null){
      return [
        'is_get' => false,
        'data' => null
      ];
    }
    return self::GetInbox($user_id, 'user_to_user', $order, $page, $per_page);
  }
  /**
   * 用户获取来自聊天组的消息
   * @param string $user_token 用户Token
   * @param string $order 排序
   * @param int $page 页数
   * @param int $per_page 每页数量
   */
  public static function UserGetChatGroupInbox($user_token, $order = '-create_time', $page = 1, $per_page = 10)
  {
    $user_id = TokenController::GetUserId($user_token);
    if($user_id == null){
      return [
        'is_get' => false,
        'data' => null
      ];
    }
    return self::GetInbox($user_id, 'user_to_chat_group', $order, $page, $per_page);
  }
  /**
   * 用户获取来自用户组的消息
   * @param string $user_token 用户Token
   * @param string $order 排序
   * @param int $page 页数
   * @param int $per_page 每页数量
   */
  public static function UserGetUserGroupInbox($user_token, $order = '-create_time', $page = 1, $per_page = 10)
  {
    $user_id = TokenController::GetUserId($user_token);
    if($user_id == null){
      return [
        'is_get' => false,
        'data' => null
      ];
    }
    return self::GetInbox($user_id, 'system_to_user_group', $order, $page, $per_page);
  }
  /**
   * 用户获取来自系统的消息
   * @param string $user_token 用户Token
   * @param string $order 排序
   * @param int $page 页数
   * @param int $per_page 每页数量
   */
  public static function UserGetSystemInbox($user_token, $order = '-create_time', $page = 1, $per_page = 10)
  {
    $user_id = TokenController::GetUserId($user_token);
    if($user_id == null){
      return [
        'is_get' => false,
        'data' => null
      ];
    }
    return self::GetInbox($user_id, 'system_to_user', $order, $page, $per_page);
  }
  /**
   * 客户端添加消息
   * @param int $sender_token 发送者Token 仅限于用户
   * @param string $sender_type 发送者类型 user_to_user、user_to_chat_group、system_to_user、system_to_user_group
   * @param int $receiver_id 接收者ID
   * @param string $content_markdown 纯文本
   * @param string $content_rendered 渲染后的HTML
   * @return
   */
  public static function Client_AddInbox($sender_token, $sender_type, $receiver_id, $content_markdown, $content_rendered)
  {
    $sender_id = TokenController::GetUserId($sender_token);
    if ($sender_id == null) {
      return [
        'is_add' => false,
        'inbox' => null
      ];
    }
    //限制发送类型为user_to_user、user_to_chat_group
    if ($sender_type != 'user_to_user' && $sender_type != 'user_to_chat_group') {
      return [
        'is_add' => false,
        'inbox' => null
      ];
    }
    return self::Server_AddInbox($sender_id, $sender_type, $receiver_id, $content_markdown, $content_rendered);
  }
  /**
   * 服务端添加消息
   * @param int $sender_id 发送者ID system、user_group_id、user_id
   * @param string $sender_type 发送者类型 user_to_user、user_to_chat_group、system_to_user、system_to_user_group
   * @param int $receiver_id 接收者ID user_id、chat_group_id
   * @param string $content_markdown 纯文本
   * @param string $content_rendered 渲染后的HTML
   * @return
   */
  public static function Server_AddInbox($sender_id, $sender_type, $receiver_id, $content_markdown, $content_rendered)
  {
    $is_valid_content =
      $sender_id != null &&
      $sender_type != null &&
      $receiver_id != null &&
      $content_markdown != null &&
      $content_rendered != null &&
      $sender_id != '' &&
      $sender_type != '' &&
      $receiver_id != '' &&
      $content_markdown != '' &&
      $content_rendered != '';
    $is_add = false;
    $inbox = null;
    //验证发送者类型
    $is_valid_sender_type = self::IsVaildSenderType($sender_type);
    if ($is_valid_content && $is_valid_sender_type) {
      $is_valid_user = false;
      if ($sender_type == 'user_to_user') {
        //验证双方是否都是存在的用户
        $is_valid_user = UserController::find($sender_id) != null && UserController::find($receiver_id) != null;
      } else if ($sender_type == 'user_to_chat_group') {
        //验证用户和聊天组是否存在
        $is_valid_user = UserController::find($sender_id) != null;
      } else if ($sender_type == 'system_to_user') {
        //验证用户是否存在
        $is_valid_user = UserController::find($receiver_id) != null;
      }else if($sender_type == 'system_to_user_group'){
        //验证用户组是否存在
        $is_valid_user = UserGroupController::find($receiver_id) != null;
      }
      if ($is_valid_user) {
        $inbox = new InboxModel;
        $inbox->sender_id = $sender_id;
        $inbox->sender_type = $sender_type;
        $inbox->receiver_id = $receiver_id;
        $inbox->content_markdown = $content_markdown;
        $inbox->content_rendered = $content_rendered;
        $inbox->create_time = Share::ServerTime();
        $is_add = $inbox->save();

        if ($is_add) {
          UserController::AddNotificationUnread($receiver_id);
          switch ($sender_type) {
            case 'user_to_user':
              // UserController::AddInboxPrivateMessage($receiver_id);
              UserController::AddNotificationUnread($receiver_id);
              break;
            case 'user_to_chat_group':
              // UserController::AddInboxUserGroup($receiver_id);
              UserController::AddNotificationUnread($receiver_id);
              break;
            case 'system_to_user':
              // UserController::AddInboxSystem($receiver_id);
              UserController::AddNotificationUnread($receiver_id);
              break;
            case 'system_to_user_group':
              // UserController::AddInboxSystem($receiver_id);
              UserController::AddNotificationUnread($receiver_id);
              break;
          }
        }
      }
    }
    return [
      'is_add' => $is_add,
      'inbox' => $inbox
    ];
  }
  /**
   * 获取消息
   * @param int $receiver_id 接收者ID
   * @param string $sender_type 发送者类型 user_to_user、user_to_chat_group、system_to_user、system_to_user_group
   * @param int $order 排序
   * @param int $page 页数
   * @param int $per_page 每页数量
   * @return array is_get:是否获取 data:消息列表
   */
  public static function GetInbox($receiver_id, $sender_type, $order = '-create_time', $page = 1, $per_page = 10)
  {
    $is_valid_receiver_id = $receiver_id != null && $receiver_id != '';
    $is_valid_sender_type = self::IsVaildSenderType($sender_type);

    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);

    $field = $orders['field'];
    $sort = $orders['sort'];

    if ($is_valid_receiver_id && $is_valid_sender_type) {
      $data = self::where('receiver_id', '=', $receiver_id)
        ->where('sender_type', '=', $sender_type)
        ->orderBy($field, $sort)
        ->paginate($per_page, ['*'], 'page', $page);
      $data = Share::HandleDataAndPagination($data);
    }
    return [
      'is_get' => $data != null,
      'data' => $data
    ];
  }
}
