<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ChatGroup extends Eloquent
{
  protected $table = 'chat_group';
  public $timestamps = false;
  protected $primaryKey = 'chat_group_id';
  /**
   * @typedef ChatGroupModel 聊天组
   * @property int $chat_group_id 聊天组ID
   * @property string $chat_group_name 聊天组名称
   * @property string $chat_group_avatar 聊天组头像
   * @property int $chat_group_user_count 聊天组用户数量
   * @property string $chat_group_info 聊天组信息
   * @property int $chat_group_owner_user_id 聊天组所有者用户ID
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $delete_time 删除时间
   */
  protected $fillable = [
    'chat_group_id',
    'chat_group_name',
    'chat_group_avatar',
    'chat_group_user_count',
    'chat_group_info',
    'chat_group_owner_user_id',
    'create_time',
    'update_time',
    'delete_time'
  ];
  public static function AddChatGroupUserCount($chat_group_id, $count = 1)
  {
    $chat_group = ChatGroup::find($chat_group_id);
    $chat_group->chat_group_user_count += $count;
    $chat_group->save();
  }
  public static function SubChatGroupUserCount($chat_group_id, $count = 1)
  {
    $chat_group = ChatGroup::find($chat_group_id);
    $chat_group->chat_group_user_count -= $count;
    $chat_group->save();
  }
}
