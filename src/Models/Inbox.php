<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Inbox extends Eloquent
{
  protected $table = 'inbox';
  public $timestamps = false;
  protected $primaryKey = 'inbox_id';
  protected $fillable = [
    'inbox_id',
    'sender_id',
    'sender_type',
    'receiver_id',
    'content_markdown',
    'content_rendered',
    'create_time',
    'read_time',
    'delete_time'
  ];
}
