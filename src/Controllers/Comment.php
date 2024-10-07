<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Comment as CommentModel;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\Question as QuestionController;
use MaterialDesignForum\Controllers\Answer as AnswerController;
use MaterialDesignForum\Controllers\Article as ArticleController;
use MaterialDesignForum\Controllers\Reply as ReplyController;
use MaterialDesignForum\Controllers\Vote as VoteController;
use MaterialDesignForum\Controllers\Notification as NotificationController;

// use MaterialDesignForum\Controllers\Inbox as InboxController;

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Config\Config;
use MaterialDesignForum\Models\Notification;

class Comment extends CommentModel
{
  /**
   * 添加评论
   * @param int $commentable_id 评论目标的ID
   * @param string $commentable_type 评论目标类型：article、question、answer、文章、提问、回答
   * @param string $content 原始正文内容
   * @param string $user_token 用户Token
   * @return
   */
  public static function AddComment(
    $commentable_id,
    $commentable_type,
    $content,
    $user_token
  ) {
    $is_valid_content =
      $commentable_id != null &&
      $commentable_type != null &&
      $content != null &&
      $user_token != '' &&
      $commentable_id != '' &&
      $commentable_type != '' &&
      $content != '' &&
      $user_token != '';
    $is_add = false;
    $comment = null;
    $user_id = TokenController::GetUserId($user_token);
    if (
      $user_id != null
      && $is_valid_content
      && (
        UserGroupController::Ability($user_token, 'ability_create_comment') ||
        UserGroupController::IsAdmin($user_token)
      )
    ) {
      $comment = new CommentModel;
      $comment->commentable_id = $commentable_id;
      $comment->commentable_type = $commentable_type;
      $comment->user_id = $user_id;
      $comment->content = $content;
      $comment->create_time = Share::ServerTime();
      $comment->update_time = Share::ServerTime();
      $is_add = $comment->save();
      if ($is_add) {
        UserController::AddCommentCount($user_id);
        // $target_user_id = 0;
        // $target_user_message = '';
        // $comment_user_name = UserController::GetUserInfo($user_id, $user_token)['user']->user_name;
        switch ($commentable_type) {
          case 'article':
            ArticleController::AddCommentCount($commentable_id);
            //获取文章作者用户ID
            $article = ArticleController::GetArticle($commentable_id, $user_token)['article'];
            NotificationController::AddNotification(
              $article->user_id,
              $user_id,
              'article_comment',
              $article->article_id,
              0,
              0,
              $comment->comment_id,
              0
            );
            // $article = ArticleController::GetArticle($commentable_id, $user_token)['article'];
            // $target_user_id = $article->user_id;
            // $target_user_message = '您的文章有新评论:%user_name: %content';
            break;
          case 'question':
            QuestionController::AddCommentCount($commentable_id);
            //根据问题ID获取问题
            $question = QuestionController::GetQuestion($commentable_id, $user_token)['question'];
            NotificationController::AddNotification(
              $question->user_id,
              $user_id,
              'question_comment',
              0,
              $question->question_id,
              0,
              $comment->comment_id,
              0
            );
            //获取问题作者用户ID
            // $question = QuestionController::GetQuestion($commentable_id, $user_token)['question'];
            // $target_user_id = $question->user_id;
            // $target_user_message = '您的问题有新评论:%user_name: %content';
            break;
          case 'answer':
            AnswerController::AddCommentCount($commentable_id);
            //根据回答ID获取回答
            $answer = AnswerController::GetAnswer($commentable_id, $user_token)['answer'];
            NotificationController::AddNotification(
              $answer->user_id,
              $user_id,
              'answer_comment',
              0,
              0,
              $answer->answer_id,
              $comment->comment_id,
              0
            );
            //获取回答作者用户ID
            // $answer = AnswerController::GetAnswer($commentable_id, $user_token)['answer'];
            // $target_user_id = $answer->user_id;
            // $target_user_message = '您的回答有新评论:%user_name: %content';
            break;
        }

        // $target_user_message = str_replace('%user_name', $comment_user_name, $target_user_message);
        // $target_user_message = str_replace('%content', $content, $target_user_message);

        // InboxController::Server_AddInbox(
        //   'system',
        //   'system_to_user',
        //   $user_id,
        //   '您的评论已发布',
        //   '您的评论已发布'
        // );
        // InboxController::Server_AddInbox(
        //   'system',
        //   'system_to_user',
        //   $target_user_id,
        //   $target_user_message,
        //   $target_user_message
        // );
      }
    }
    return [
      'is_add' => $is_add,
      'comment' => self::GetComment($comment->comment_id, $user_token)['comment'],
    ];
  }
  /**
   * 获取评论
   * @param int $comment_id 评论ID
   * @param string $user_token 用户Token
   * @return CommentModel|null
   */
  public static function GetComment($comment_id, $user_token)
  {
    $comment = self::where('comment_id', '=', $comment_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($comment != null) {
      $comment->user = UserController::GetUserInfo($comment->user_id, $user_token)['user'];
      $comment->vote = VoteController::GetVote($comment->comment_id, 'comment', $user_token)['vote'];
    }
    return [
      'is_get' => $comment != null,
      'comment' => $comment,
    ];
  }
  /**
   * 获取评论列表
   * @param int $commentable_id 评论目标的ID
   * @param string $commentable_type 评论目标类型：article、question、answer、文章、提问、回答
   * @param string $order 排序方式
   * @param int $page 页码
   * @param string $user_token 用户Token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @return array
   */
  public static function GetComments(
    $commentable_id,
    $commentable_type,
    $order,
    $page,
    $user_token,
    $per_page = 20,
    $search_keywords = '',
    $search_field = []
  ) {
    if($search_field == []){
      $search_field = self::$search_field;
    }

    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);

    $field = $orders['field'];
    $sort = $orders['sort'];

    if ($search_keywords != '') {
      if (($commentable_id != 0 && $commentable_id != '') && $commentable_type != '') {
        $data = self::where('commentable_id', '=', $commentable_id)
          ->where('commentable_type', '=', $commentable_type)
          ->where('delete_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('delete_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      }
    } else {
      if (($commentable_id != 0 && $commentable_id != '') && $commentable_type != '') {
        $data = self::where('commentable_id', '=', $commentable_id)
          ->where('commentable_type', '=', $commentable_type)
          ->where('delete_time', '=', 0)
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('delete_time', '=', 0)
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      }
    }

    $data = Share::HandleDataAndPagination($data);

    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]->commentable_parent_id = $data['data'][$key]->commentable_id;
        $data['data'][$key]->commentable_parent_type = $data['data'][$key]->commentable_type;

        if ($data['data'][$key]->commentable_parent_type == 'answer') {

          //$data['data'][$key]->commentable_parent_id = AnswerController::GetAnswer($data['data'][$key]->commentable_parent_id, $user_token)['answer']->answerable_id;
          //$data['data'][$key]->commentable_parent_type = AnswerController::GetAnswer($data['data'][$key]->commentable_parent_id, $user_token)['answer']->answerable_type;

          // $data['data'][$key]->commentable_parent_id = AnswerController::GetAnswer($data['data'][$key]->commentable_parent_id, $user_token)['answer']->question_id;
          $data['data'][$key]->commentable_parent_id = AnswerController::where('answer_id', '=', $data['data'][$key]->commentable_parent_id)->first()->question_id;
          $data['data'][$key]->commentable_parent_type = 'question'; //AnswerController::GetAnswer($data['data'][$key]->commentable_parent_id, $user_token)['answer']->answerable_type;
        }
        $data['data'][$key]->user = UserController::GetUserInfo($value->user_id, $user_token)['user'];
        $data['data'][$key]->vote = VoteController::GetVote($value->comment_id, 'comment', $user_token)['vote'];
      }
    }

    //$data['$per_page'] = $per_page;

    return $data;
  }
  /**
   * 编辑评论
   * @param int $comment_id 评论ID
   * @param string $content 原始正文内容
   * @param string $user_token 用户Token
   * @return
   */
  public static function EditComment(
    $comment_id,
    $content,
    $user_token
  ) {
    $is_valid_content =
      $comment_id != null &&
      $content != null &&
      $user_token != '' &&
      $comment_id != '' &&
      $content != '' &&
      $user_token != '';
    $is_edit = false;
    // $comment = null;
    // $user_id = TokenController::GetUserId($user_token);
    // if (
    //   $user_id != null
    //   && $is_valid_content
    //   && (
    //     UserGroupController::Ability($user_token, 'ability_edit_own_comment') ||
    //     UserGroupController::IsAdmin($user_token)
    //   )
    // ) {
    //   $comment = self::where('comment_id', '=', $comment_id)
    //     ->where('delete_time', '=', 0)
    //     ->first();
    //   if ($comment != null) {
    //     $comment->content = $content;
    //     $comment->update_time = Share::ServerTime();
    //     $is_edit = $comment->save();
    //   }
    // }
    $user_id = TokenController::GetUserId($user_token);
    $comment = self::where('comment_id', '=', $comment_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($comment != null && $is_valid_content && $user_id != null) {
      if (
        (
          TokenController::IsUserSelf($user_token, $comment->user_id) &&
          UserGroupController::Ability($user_token, 'ability_edit_own_comment') &&
          (
            UserGroupController::Ability($user_token, 'ability_edit_comment_only_no_reply') ? ($comment->reply_count == 0 ? true : false) : true
          ) &&
          UserGroupController::BeforeTime($user_token, 'time_before_edit_comment', $comment->create_time)
        )
        ||
        UserGroupController::IsAdmin($user_token)
      ) {
        $comment->content = $content;
        $comment->update_time = Share::ServerTime();
        $is_edit = $comment->save();
      }
    }
    return [
      'is_edit' => $is_edit,
      'comment' => self::GetComment($comment_id, $user_token)['comment'],
    ];
  }
  /**
   * 删除评论
   * @param int $comment_ids 评论ID数组
   * @param string $user_token 用户Token
   * @return
   */
  public static function DeleteComments(
    $comment_ids,
    $user_token
  ) {
    $is_valid_content =
      $comment_ids != null &&
      $user_token != '' &&
      $comment_ids != '' &&
      $user_token != '';
    $is_delete = false;
    $user_id = TokenController::GetUserId($user_token);
    $delete_ids = [];
    $comments = [];
    if (
      $user_id != null &&
      $is_valid_content
    ) {
      $comments = self::whereIn('comment_id', $comment_ids)->get();
      foreach ($comments as $key => $comment) {
        if (
          (
            TokenController::IsUserSelf($user_token, $comment->user_id) &&
            UserGroupController::Ability($user_token, 'ability_delete_own_comment') &&
            (
              UserGroupController::Ability($user_token, 'ability_delete_comment_only_no_reply') ? ($comment->reply_count == 0 ? true : false) : true
            ) &&
            UserGroupController::BeforeTime($user_token, 'time_before_delete_comment', $comment->create_time)
          )
          ||
          UserGroupController::IsAdmin($user_token)
        ) {
          $comment->delete_time = Share::ServerTime();
          UserController::SubCommentCount($comment->user_id);
          NotificationController::AddNotification(
            $comment->user_id,
            $user_id,
            'comment_delete',
            0,
            0,
            0,
            $comment->comment_id,
            0
          );

          switch($comment->commentable_type){
            case 'article':
              ArticleController::SubCommentCount($comment->commentable_id);
              break;
            case 'question':
              QuestionController::SubCommentCount($comment->commentable_id);
              break;
            case 'answer':
              AnswerController::SubCommentCount($comment->commentable_id);
              break;
          }

          //联动删除此评论下的所有回复
          //删除此评论下的所有回复 ->where('delete_time', '=', 0)//看情况决定是否加上这个条件
          $replys = ReplyController::where('replyable_comment_id', '=', $comment->comment_id)
            ->get();
          if($replys != null){
            foreach ($replys as $key => $reply) {
              $reply->delete_time = Share::ServerTime();
              $reply->save();
  
              UserController::SubReplyCount($reply->user_id);
            }
          }

          $is_delete = $comment->save();
          array_push($delete_ids, $comment->comment_id);
        }
      }
    }
    return [
      'is_delete' => $is_delete,
      'delete_ids' => $delete_ids,
      'data' => $comments,
    ];
    // if (
    //   $user_id != null
    //   && $is_valid_content
    //   && (
    //     UserGroupController::Ability($user_token, 'ability_delete_own_comment') ||
    //     UserGroupController::IsAdmin($user_token)
    //   )
    // ) {
    //   $is_delete = self::whereIn('comment_id', $comment_ids)
    //     ->where('delete_time', '=', 0)
    //     ->update([
    //       'delete_time' => Share::ServerTime(),
    //     ]);
    // }
    // return [
    //   'is_delete' => $is_delete,
    // ];
  }
}
