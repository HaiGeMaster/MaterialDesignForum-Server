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
