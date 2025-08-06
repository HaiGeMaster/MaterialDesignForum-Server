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

class Notification extends Eloquent
{
  protected $table = 'notification';
  public $timestamps = false;
  protected $primaryKey = 'notification_id';

  

  /**
   * @typedef NotificationModel 通知
   * @property int $notification_id 通知ID
   * @property int $receiver_id 接收者ID
   * @property int $sender_id 发送者ID
   * @property string $type 消息类型
   * @property string $content_markdown 内容Markdown
   * @property string $content_rendered 内容渲染
   * @property int $user_id 用户ID
   * @property int $topic_id 话题ID
   * @property int $article_id 文章ID
   * @property int $question_id 提问ID
   * @property int $answer_id 回答ID
   * @property int $comment_id 评论ID
   * @property int $reply_id 回复ID
   * @property int $reply_to_reply_id 回复回复ID
   * @property int $create_time 创建时间
   * @property int $read_time 阅读时间
   * @property int $delete_time 删除时间
   */
  protected $fillable = [
    'notification_id',
    'receiver_id',
    'sender_id',
    'type',
    'content_markdown',
    'content_rendered',
    'user_id',
    'topic_id',
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

//旧的类型详细
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
  
    //类型注释定义
    /**
     * @typedef NotificationType 通知类型
     * @property string user_follow 自己被关注 已做完
     * @property string topic_follow 话题被关注 已做完
     * @property string topic_delete 话题被删除 已做完
     * @property string question_follow 提问被关注 已做完
     * @property string question_comment 提问被评论 已做完
     * @property string question_answer 提问被回答 已做完
     * @property string question_delete 提问被删除 已做完
     * @property string article_follow 文章被关注 已做完
     * @property string article_comment 文章被评论 已做完
     * @property string article_like 文章被点赞 已做完
     * @property string article_delete 文章被删除 已做完
     * @property string answer_comment 回答被评论 已做完
     * @property string answer_like 回答被点赞 已做完
     * @property string answer_delete 回答被删除 已做完
     * @property string comment_like 评论被点赞 已做完
     * @property string comment_reply 评论被回复 已做完
     * @property string comment_delete 评论被删除 已做完
     * @property string reply_like 回复被点赞 已做完
     * @property string reply_reply 回复被回复 已做完
     * @property string reply_delete 回复被删除 已做完
     */

    public static $types = [
      'user_follow', //自己被关注
      'topic_follow', //话题被关注
      'topic_delete', //话题被删除
      'question_follow', //提问被关注
      'question_comment', //提问被评论
      'question_answer', //提问被回答
      'question_delete', //提问被删除
      'article_follow', //文章被关注
      'article_comment', //文章被评论
      'article_like', //文章被点赞
      'article_delete', //文章被删除
      'answer_comment', //回答被评论
      'answer_like', //回答被点赞
      'answer_delete', //回答被删除
      'comment_like', //评论被点赞
      'comment_reply', //评论被回复
      'comment_delete', //评论被删除
      'reply_like', //回复被点赞
      'reply_reply', //回复被回复
      'reply_delete' //回复被删除
    ];

  /**
   * 是否是有效的消息类型
   * @param string $type 消息类型
   * @return bool
   */
  public static function IsVaildType($type): bool
  {
    return in_array($type, self::$types);
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
