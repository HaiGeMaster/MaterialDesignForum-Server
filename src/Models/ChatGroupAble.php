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

class ChatGroupAble extends Eloquent
{
  protected $table = 'chat_groupable';
  public $timestamps = false;
  protected $primaryKey = 'chat_groupable_id';
  /**
   * @typedef ChatGroupAbleModel 聊天组关联
   * @property int $chat_groupable_id 聊天组关联ID
   * @property int $user_id 用户ID
   * @property int $chat_group_id 聊天组ID
   * @property int $create_time 创建时间
   * @property int $delete_time 删除时间
   */
  protected $fillable = [
    'chat_groupable_id',
    'user_id',
    'chat_group_id',
    'create_time',
    'delete_time'
  ];
}
