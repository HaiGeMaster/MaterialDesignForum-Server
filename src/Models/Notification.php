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

//notification_id 通知ID	receiver_id 接收者ID	sender_id 发送者ID	
//type 消息类型：question_answer, question_comment, question_delete, article_comment, article_delete, answer_comment, answer_delete, comment_reply, comment_delete, reply_reply, reply_delete

//类型详细
//question_answer 问题被回答
//question_comment 问题被评论
//question_delete 问题被删除
//article_comment 文章被评论
//article_delete 文章被删除
//answer_comment 回答被评论
//answer_delete 回答被删除
//comment_reply 评论被回复
//comment_delete 评论被删除
//reply_reply 回复被回复
//reply_delete 回复被删除

// …	article_id 文章ID	question_id 提问ID	answer_id 回答ID	comment_id 评论ID	reply_id 回复ID	content_deleted 被删除的内容的备份	create_time 发送时间	read_time 阅读时间	delete_time 删除时间

class Notification extends Eloquent
{
  protected $table = 'notification';
  public $timestamps = false;
  protected $primaryKey = 'notification_id';
  protected $fillable = [
    'notification_id',
    'receiver_id',
    'sender_id',
    'type',
    'content_markdown',
    'content_rendered',
    'article_id',
    'question_id',
    'answer_id',
    'comment_id',
    'reply_id',
    'reply_to_reply_id',
    // 'content_deleted',
    'create_time',
    'read_time',
    'delete_time'
  ];
  /**
   * 是否是有效的消息类型
   * @param string $type 消息类型 @type [question_answer, question_comment, question_delete, article_comment, article_delete, answer_comment, answer_delete, comment_reply, comment_delete, reply_reply, reply_delete]
   * @return bool
   */
  public static function IsVaildType($type): bool
  {
    $types = [
      'question_answer',
      'question_comment',
      'question_delete',
      'article_comment',
      'article_delete',
      'answer_comment',
      'answer_delete',
      'comment_reply',
      'comment_delete',
      'reply_reply',
      'reply_delete'
    ];
    return in_array($type, $types);
  }
  /**
   * 设置消息已读
   * @param int $notification_id 消息ID
   * @param int $read_time 阅读时间
   */
  public static function SetReadTime($notification_id, $read_time)
  {
    $notification = Notification::find($notification_id);
    if ($notification) {
      $notification->read_time = $read_time;
      $notification->save();
      return true;
    }
    return false;
  }
}
