<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Notification as NotificationModel;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Article as ArticleController;
use MaterialDesignForum\Controllers\Question as QuestionController;
use MaterialDesignForum\Controllers\Answer as AnswerController;
use MaterialDesignForum\Controllers\Comment as CommentController;
use MaterialDesignForum\Controllers\Reply as ReplyController;
use MaterialDesignForum\Controllers\UserOption as UserOptionController;


use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Models\MailCaptcha as MailCaptchaModel;

class Notification extends NotificationModel
{


  //通知设置类型注释定义
  /**
   * @typedef NotificationType 通知类型
   * @property string user_follow 自己被关注
   * @property string topic_follow 话题被关注
   * @property string topic_delete 话题被删除
   * @property string article_follow 文章被关注
   * @property string article_comment 文章被评论
   * @property string article_like 文章被点赞
   * @property string article_delete 文章被删除
   * @property string question_follow 提问被关注
   * @property string question_comment 提问被评论
   * @property string question_answer 提问被回答
   * @property string question_delete 问题被删除
   * @property string answer_comment 回答被评论
   * @property string answer_like 回答被点赞
   * @property string answer_delete 回答被删除
   * @property string comment_like 评论被点赞
   * @property string comment_reply 评论被回复
   * @property string comment_delete 评论被删除
   * @property string reply_like 回复被点赞
   * @property string reply_reply 回复被回复
   * @property string reply_delete 回复被删除
   */
  /**
   * 添加互动通知 此方法不对外开放 仅供内部调用
   * @param int $receiver_id 接收者ID
   * @param int $sender_id 发送者ID 一般是系统，也可以是用户
   * @param string $type 消息类型 @type NotificationType 通知类型 必须是有效的通知类型
   * @param string $content_markdown 消息内容Markdown
   * @param string $content_rendered 消息内容HTML
   * @param int $user_id 用户ID
   * @param int $topic_id 话题ID
   * @param int $article_id 被xx的文章ID
   * @param int $question_id 被xx的提问ID
   * @param int $answer_id 被xx的回答ID
   * @param int $comment_id 被xx的评论ID
   * @param int $reply_id 被xx的回复ID
   * @return array [is_add:是否添加成功 notification:通知对象]
   */
  public static function AddInteractionNotification(
    $receiver_id = 0,
    $sender_id = '',
    $type = '',
    $content_markdown = null,
    $content_rendered = null,
    $user_id = 0,
    $topic_id = 0,
    $article_id = 0,
    $question_id = 0,
    $answer_id = 0,
    $comment_id = 0,
    $reply_id = 0,
    $reply_to_reply_id = 0
  ) {
    $is_add = false;
    $notification = null;

    if (self::IsVaildType($type) && ($sender_id != $receiver_id)) {
      $notification = new NotificationModel;
      $notification->receiver_id = $receiver_id;
      $notification->sender_id = $sender_id;
      $notification->type = $type;
      $notification->content_markdown = $content_markdown;
      $notification->content_rendered = $content_rendered;
      $notification->user_id = $user_id;
      $notification->topic_id = $topic_id;
      $notification->article_id = $article_id;
      $notification->question_id = $question_id;
      $notification->answer_id = $answer_id;
      $notification->comment_id = $comment_id;
      $notification->reply_id = $reply_id;
      $notification->reply_to_reply_id = $reply_to_reply_id;
      $notification->create_time = Share::ServerTime();
      $notification->delete_time = 0;

      $is_add = $notification->save();
      if ($is_add) {
        $notification_setting = self::GetUserOptionNotificationSetting($receiver_id, $type);
        $web_message = $notification_setting['web_message'];
        $email_message = $notification_setting['email_message'];

        //添加站内用户通知
        if ($web_message) {
          UserController::AddNotificationCount($receiver_id);
        }

        //添加站外用户邮件通知
        // 获取收件人邮箱
        if ($email_message) {

          $receiver = UserController::where('user_id', '=', $receiver_id)->first();
          if ($receiver && isset($receiver->email)) {
            $receiver_email = $receiver->email;
            $receiver_language = $receiver->language;

            $mail_title = i18n::t('Message.Client.Notifications.YouHaveNewNotifications', $receiver_language);
            $mail_content = i18n::t('Message.Client.Notifications.Type.' . $type, $receiver_language);

            //$mail_content+当前服务器域名
            $mail_content = $mail_content . ' [<a href="http://' . $_SERVER['HTTP_HOST'] . '">' . Option::Get('site_name') . '</a>]';
            // $mail_content = $mail_content . ' [' . $_SERVER['HTTP_HOST'] . ']';

            // 替换发送者名字
            $sender = UserController::where('user_id', $sender_id)->first();
            if ($sender && isset($sender->username)) {
              $sender_name = $sender->username;
              $mail_content = str_replace('{value}', $sender_name, $mail_content);
            }

            // 发送邮件
            try {
              MailCaptchaModel::SendMail($receiver_email, $mail_title, $mail_content);
            } catch (\Exception $e) {
              error_log("Email sending failed: " . $e->getMessage());
            }
          } else {
            // 处理未找到用户的情况
            error_log("Receiver not found or email is missing.");
          }
        }
      }
    }
    return [
      'is_add' => $is_add,
      'notification' => $notification
    ];
  }
  /**
   * 获取用户通知
   * @param string $user_token 用户Token
   * @param string $order 排序方式
   * @param int $page 页码
   * @param int $per_page 每页数量
   * @return array
   */
  public static function GetUserInteractionNotifications(
    $user_token,
    $order,
    $page,
    $per_page = 20
  ) {

    // return 'fuck';

    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    $user_id = TokenController::GetUserId($user_token);
    $notifications = NotificationModel::where('receiver_id', $user_id)
      ->where('delete_time', 0)
      ->orderBy($field, $sort)
      ->paginate($per_page, ['*'], 'page', $page);

    // return $notifications;

    if ($notifications != null) {
      foreach ($notifications as $key => $notification) {
        UserController::SubNotificationCount($notification->receiver_id);

        if ($notification->read_time == 0) {
          self::SetReadTime($notification->notification_id, Share::ServerTime());
        }

        $notification->sender_user = UserController::GetUser($notification->sender_id)['user'];
        $notification->receiver_user = UserController::GetUser($notification->receiver_id)['user'];

        $receiver_content = '';//附加值
        $sender_content = '';//附加值
        switch ($notification->type) {
          case 'question_answer':
            $notification->question = QuestionController::where('question_id', $notification->question_id)->first();
            $notification->answer = AnswerController::where('answer_id', $notification->answer_id)->first();
            if ($notification->question != null && $notification->answer != null) {
              $receiver_content = $notification->question->title;
              $sender_content = $notification->answer->content_markdown;
            }
            break;
          case 'question_comment':
            $notification->question = QuestionController::where('question_id', $notification->question_id)->first();
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
            if ($notification->question != null && $notification->comment != null) {
              $receiver_content = $notification->question->title;
              $sender_content = $notification->comment->content;
            }
            break;
          case 'question_delete':
            $notification->question = QuestionController::where('question_id', $notification->question_id)->first();
            if ($notification->question != null) {
              $receiver_content = $notification->question->title;
            }
            break;
          case 'article_comment':
            $notification->article = ArticleController::where('article_id', $notification->article_id)->first();
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
            if ($notification->article != null && $notification->comment != null) {
              $receiver_content = $notification->article->title;
              $sender_content = $notification->comment->content;
            }
            break;
          case 'article_delete':
            $notification->article = ArticleController::where('article_id', $notification->article_id)->first();
            if ($notification->article != null) {
              $receiver_content = $notification->article->title;
            }
            break;
          case 'answer_comment':
            $notification->answer = AnswerController::where('answer_id', $notification->answer_id)->first();
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
            if ($notification->answer != null && $notification->comment != null) {
              $receiver_content = $notification->answer->content_markdown;
              $sender_content = $notification->comment->content;
            }
            break;
          case 'answer_delete':
            $notification->answer = AnswerController::where('answer_id', $notification->answer_id)->first();
            if ($notification->answer != null) {
              $receiver_content = $notification->answer->content_markdown;
            }
            break;
          case 'comment_reply':
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
            $notification->reply = ReplyController::where('reply_id', $notification->reply_id)->first();
            if ($notification->comment != null) {
              switch ($notification->comment->commentable_type) {
                case 'article':
                  $notification->article = ArticleController::where('article_id', $notification->comment->commentable_id)->first();
                  break;
                case 'question':
                  $notification->question = QuestionController::where('question_id', $notification->comment->commentable_id)->first();
                  break;
                case 'answer':
                  $notification->answer = AnswerController::where('answer_id', $notification->comment->commentable_id)->first();
                  break;
              }
              $receiver_content = $notification->comment->content;
              // $sender_content = $notification->reply->content;
            }
            // $receiver_content = $notification->comment->content;
            if ($notification->reply != null) {
              $sender_content = $notification->reply->content;
            }
            // $sender_content = $notification->reply->content;
            break;
          case 'comment_delete':
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
            if ($notification->comment != null) {
              // switch ($notification->comment->commentable_type) {
              //     case 'article':
              //         $notification->article = ArticleController::where('article_id', $notification->comment->commentable_id)->first();
              //         break;
              //     case 'question':
              //         $notification->question = QuestionController::where('question_id', $notification->comment->commentable_id)->first();
              //         break;
              //     case 'answer':
              //         $notification->answer = AnswerController::where('answer_id', $notification->comment->commentable_id)->first();
              //         break;
              // }
              if ($notification->comment->commentable_type == 'article') {
                $notification->article = ArticleController::where('article_id', $notification->comment->commentable_id)->first();
              } else if ($notification->comment->commentable_type == 'question') {
                $notification->question = QuestionController::where('question_id', $notification->comment->commentable_id)->first();
              } else if ($notification->comment->commentable_type == 'answer') {
                $notification->answer = AnswerController::where('answer_id', $notification->comment->commentable_id)->first();
              }
              $receiver_content = $notification->comment->content;
            }
            break;
          case 'reply_reply': //replyable_id
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first(); //被回复的item comment_id
            if ($notification->comment != null) {
              // switch ($notification->comment->commentable_type) {
              //     case 'article':
              //         $notification->article = ArticleController::where('article_id', $notification->comment->commentable_id)->first();
              //         break;
              //     case 'question':
              //         $notification->question = QuestionController::where('question_id', $notification->comment->commentable_id)->first();
              //         break;
              //     case 'answer':
              //         $notification->answer = AnswerController::where('answer_id', $notification->comment->commentable_id)->first();
              //         break;
              // }
              if ($notification->comment->commentable_type == 'article') {
                $notification->article = ArticleController::where('article_id', $notification->comment->commentable_id)->first();
              } else if ($notification->comment->commentable_type == 'question') {
                $notification->question = QuestionController::where('question_id', $notification->comment->commentable_id)->first();
              } else if ($notification->comment->commentable_type == 'answer') {
                $notification->answer = AnswerController::where('answer_id', $notification->comment->commentable_id)->first();
              }
              $notification->reply = ReplyController::where('reply_id', $notification->reply_id)->first(); //接收者的reply
              $notification->replyable_reply = ReplyController::where('reply_id', $notification->reply_to_reply_id)->first(); //发送者的reply

              $receiver_content = $notification->replyable->content;
              $sender_content = $notification->replyable_reply->content;
            }
            break;
          case 'reply_delete':
            $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
            $notification->reply = ReplyController::where('reply_id', $notification->reply_id)->first();
            $receiver_content = $notification->reply->content;
            break;
        }
        //如果$receiver_content超过10个字符，截取前10个字符
        // if(strlen($receiver_content) > 10){
        //     $receiver_content = substr($receiver_content, 0, 10).'...';
        // }
        $notification->receiver_content = $receiver_content;
        $notification->sender_content = $sender_content;
        // $notification->item_link = $item_link;
      }
    }

    return Share::HandleDataAndPagination($notifications);
  }
  /**
   * 设置通知为删除状态
   * @param int $user_token 用户Token
   * @param int $notification_id 通知ID
   * @return bool
   */
  public static function DeleteNotification($user_token, $notification_id)
  {
    $is_delete = false;
    $user_id = TokenController::GetUserId($user_token);
    $notification = NotificationModel::where('notification_id', $notification_id)->first();
    if ($notification != null && $notification->receiver_id == $user_id) {
      $notification->delete_time = Share::ServerTime();
      $is_delete = $notification->save();
    }
    return [
      'is_delete' => $is_delete,
      'notification' => $notification
    ];
  }
  /**
   * 获取用户通知设置
   * @param int $user_id 用户ID
   * @return array|null 通知设置 转换为 [name=>[web_message=>bool,email_message=>bool]]
   */
  public static function GetUserOptionNotificationValue($user_id)
  {
    $data = UserOptionController::where('user_id', $user_id)->where('name', 'notifications')->first();
    if ($data != null) {
      $data = json_decode($data->value, true); //将json字符串转换为数组
      $new_data = [];
      //将$data中的name作为$new_data的key
      foreach ($data as $item) {
        $new_data[$item['name']] = [
          'web_message' => $item['web_message'] == 'true' ? true : false,
          'email_message' => $item['email_message'] == 'true' ? true : false,
        ];
      }
      return $new_data == [] ? null : $new_data;
    }
    return null;
  }
  /**
   * 获取用户通知设置
   * @param int $user_id 用户ID
   * @param string $type 通知类型
   * @return array [web_message=>bool,email_message=>bool] 用户通知设置为空的情况下默认都为true
   */
  public static function GetUserOptionNotificationSetting($user_id, $type): array
  {
    $data = self::GetUserOptionNotificationValue($user_id);
    if ($data != null) {
      if (array_key_exists($type, $data)) {
        // return $data[$type];//相当于返回了[web_message=>bool,email_message=>bool]
        return [
          'web_message' => $data[$type]['web_message'],
          'email_message' => $data[$type]['email_message'],
        ];
      }
    }
    return ['web_message' => true, 'email_message' => true]; //用户通知设置为空的情况下默认都为true
  }
}
