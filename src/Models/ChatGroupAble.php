<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
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
