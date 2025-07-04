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

class Inbox extends Eloquent
{
  protected $table = 'inbox';
  public $timestamps = false;
  protected $primaryKey = 'inbox_id';
  /**
   * @typedef InboxModel 私信
   * @property int $inbox_id 私信ID
   * @property int $sender_id 发送者ID
   * @property string $sender_type 发送者类型
   * @property int $receiver_id 接收者ID
   * @property string $content_markdown 私信内容Markdown
   * @property string $content_rendered 私信内容渲染
   * @property int $create_time 创建时间
   * @property int $read_time 读取时间
   * @property int $delete_time 删除时间
   */
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
