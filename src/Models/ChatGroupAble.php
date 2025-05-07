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
  protected $fillable = [
    'chat_groupable_id',
    'user_id',
    'chat_group_id',
    'create_time',
    'delete_time'
  ];
}
