<?php

/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
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

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Models\MailCaptcha as MailCaptchaModel;

class Notification extends NotificationModel
{
    /**
     * 添加通知 此方法不对外开放 仅供内部调用
     * @param int $receiver_id 接收者ID
     * @param int $sender_id 发送者ID 一般是系统，也可以是用户
     * @param string $type 消息类型 @type [question_answer, question_comment, question_delete, article_comment, article_delete, answer_comment, answer_delete, comment_reply, comment_delete, reply_reply, reply_delete]
     * @param int $article_id 被xx的文章ID
     * @param int $question_id 被xx的提问ID
     * @param int $answer_id 被xx的回答ID
     * @param int $comment_id 被xx的评论ID
     * @param int $reply_id 被xx的回复ID
     * @return void
     */
    public static function AddNotification(
        $receiver_id = 0,
        $sender_id = '',
        $type = '',
        $article_id = 0,
        $question_id = 0,
        $answer_id = 0,
        $comment_id = 0,
        $reply_id = 0,
        $reply_to_reply_id = 0
    ) {
        $is_add = false;

        if (self::IsVaildType($type) == true) {

            $notification = new NotificationModel;
            $notification->receiver_id = $receiver_id;
            $notification->sender_id = $sender_id;
            $notification->type = $type;
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
                UserController::AddNotificationCount($receiver_id);

                //获取收件人邮箱
                $receiver = UserController::GetUser($receiver_id)['user'];
                $receiver_email = $receiver->email;

                $mail = new MailCaptchaModel();
                $mail->SendMail(
                    $receiver_email,
                    '您有新的通知',
                    '您有新的通知，请登录查看'
                );
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
    public static function GetUserNotifications(
        $user_token,
        $order,
        $page,
        $per_page = 20
    ) {
        $orders = Share::HandleArrayField($order);
        $field = $orders['field'];
        $sort = $orders['sort'];
        $user_id = TokenController::GetUserId($user_token);
        $notifications = NotificationModel::where('receiver_id', $user_id)
            ->orderBy($field, $sort)
            ->paginate($per_page, ['*'], 'page', $page);

        if ($notifications != null) {
            foreach ($notifications as $key => $notification) {
                UserController::SubNotificationCount($notification->receiver_id);
                $notification->sender_user = UserController::GetUser($notification->sender_id)['user'];
                $notification->receiver_user = UserController::GetUser($notification->receiver_id)['user'];

                $receiver_content = '';
                $sender_content = '';
                $item_link = '';
                switch ($notification->type) {
                    case 'question_answer':
                        $notification->question = QuestionController::where('question_id', $notification->question_id)->first();
                        $notification->answer = AnswerController::where('answer_id', $notification->answer_id)->first();
                        $receiver_content = $notification->question->title;
                        $sender_content = $notification->answer->content_markdown;
                        break;
                    case 'question_comment':
                        $notification->question = QuestionController::where('question_id', $notification->question_id)->first();
                        $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
                        $receiver_content = $notification->question->title;
                        $sender_content = $notification->comment->content;
                        break;
                    case 'question_delete':
                        $notification->question = QuestionController::where('question_id', $notification->question_id)->first();
                        $receiver_content = $notification->question->title;
                        break;
                    case 'article_comment':
                        $notification->article = ArticleController::where('article_id', $notification->article_id)->first();
                        $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
                        $receiver_content = $notification->article->title;
                        $sender_content = $notification->comment->content;
                        break;
                    case 'article_delete':
                        $notification->article = ArticleController::where('article_id', $notification->article_id)->first();
                        $receiver_content = $notification->article->title;
                        break;
                    case 'answer_comment':
                        $notification->answer = AnswerController::where('answer_id', $notification->answer_id)->first();
                        $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
                        $receiver_content = $notification->answer->content_markdown;
                        $sender_content = $notification->comment->content;
                        break;
                    case 'answer_delete':
                        $notification->answer = AnswerController::where('answer_id', $notification->answer_id)->first();
                        $receiver_content = $notification->answer->content_markdown;
                        break;
                    case 'comment_reply':
                        $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
                        $notification->reply = ReplyController::where('reply_id', $notification->reply_id)->first();
                        $receiver_content = $notification->comment->content;
                        $sender_content = $notification->reply->content;
                        break;
                    case 'comment_delete':
                        $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first();
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
                        }
                        break;
                    case 'reply_reply': //replyable_id
                        $notification->comment = CommentController::where('comment_id', $notification->comment_id)->first(); //被回复的item comment_id
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

                $notification->receiver_content = $receiver_content;
                $notification->sender_content = $sender_content;
                $notification->item_link = $item_link;
            }
        }

        return Share::HandleDataAndPagination($notifications);
    }
}
