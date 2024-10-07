<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Answer as AnswerModel;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\Question as QuestionController;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Vote as VoteController;
use MaterialDesignForum\Controllers\Comment as CommentController;
use MaterialDesignForum\Controllers\Reply as ReplyController;
use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Controllers\Notification as NotificationController;
use MaterialDesignForum\Models\Notification;

// use MaterialDesignForum\Config\Config;

class Answer extends AnswerModel
{
  /**
   * 添加回答
   * @param int $question_id 问题ID
   * @param string $content_markdown 纯文本
   * @param string $content_rendered 渲染后的HTML
   * @param string $user_token 用户Token
   * @return
   */
  public static function AddAnswer($question_id, $content_markdown, $content_rendered, $user_token)
  {
    $is_valid_content =
      $question_id != null &&
      $content_markdown != null &&
      $content_rendered != null &&
      $user_token != '' &&
      $question_id != '' &&
      $content_markdown != '' &&
      $content_rendered != '' &&
      $user_token != '';
    $user_id = TokenController::GetUserId($user_token);
    $is_add = false;
    if (
      $user_id != null
      && $is_valid_content
      && (
        UserGroupController::Ability($user_token, 'ability_create_answer') ||
        UserGroupController::IsAdmin($user_token)
      )
    ) {
      $content_markdown = preg_replace('/\s+/', '', $content_markdown);//去除回车和空格

      $answer = new AnswerModel;
      $answer->question_id = $question_id;
      $answer->user_id = $user_id;
      $answer->content_markdown = $content_markdown;
      $answer->content_rendered = $content_rendered;
      $answer->comment_count = 0;
      $answer->create_time = Share::ServerTime();
      $answer->update_time = Share::ServerTime();
      $is_add = $answer->save();
      if ($is_add) {
        // UserController::AddAnswerCount($user_id)['user'];
        UserController::AddAnswerCount($user_id);//['user'];
        QuestionController::AddAnswerCount($question_id);
        //根据问题ID获取问题
        $question = QuestionController::GetQuestion($question_id, $user_token)['question'];
        NotificationController::AddNotification(
          $question->user_id,
          $user_id,
          'question_answer',
          0,
          $question->question_id,
          $answer->answer_id,
          0,
          0
        );
      }
    }
    return [
      'is_add' => $is_add,
      'answer' => self::GetAnswer($answer->answer_id, $user_token)['answer'],
    ];
  }
  /**
   * 获取回答
   * @param int $answer_id 回答ID
   * @param string $user_token 用户Token
   * @return
   */
  public static function GetAnswer($answer_id, $user_token)
  {
    $answer = AnswerModel::where('answer_id', $answer_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($answer != null) {
      $answer->user = UserController::GetUserInfo($answer->user_id)['user'];
      $answer->vote = VoteController::GetVote($answer->answer_id, 'answer', $user_token)['vote'];
    }
    return [
      'is_get' => $answer != null,
      'answer' => $answer,
    ];
  }
  /**
   * 获取回答列表
   * @param int $question_id 问题ID
   * @param string $order 排序
   * @param int $page 页码
   * @param string $user_token 用户Token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @return array data:回答列表 pagination:分页信息
   */
  public static function GetAnswers(
    $question_id,
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
      if ($question_id != '') {
        $data = self::where('question_id', '=', $question_id)
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
      if ($question_id != '') {
        $data = self::where('question_id', '=', $question_id)
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
        $data['data'][$key]->user = UserController::GetUserInfo($value->user_id, $user_token)['user'];
        $data['data'][$key]->vote = VoteController::GetVote($value->answer_id, 'answer', $user_token)['vote'];
      }
    }

    return $data;
  }
  /**
   * 编辑回答
   * @param int $answer_id 回答ID
   * @param string $content_markdown 纯文本
   * @param string $content_rendered 渲染后的HTML
   * @param string $user_token 用户Token
   * @return
   */
  public static function EditAnswer($answer_id, $content_markdown, $content_rendered, $user_token)
  {
    $is_valid_content =
      $answer_id != null &&
      $content_markdown != null &&
      $content_rendered != null &&
      $user_token != '' &&
      $answer_id != '' &&
      $content_markdown != '' &&
      $content_rendered != '' &&
      $user_token != '';
    $is_edit = false;
    $user_id = TokenController::GetUserId($user_token);
    $answer = AnswerModel::where('answer_id', $answer_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($answer != null && $is_valid_content && $user_id != null) {
      if (
        (
          TokenController::IsUserSelf($user_token, $answer->user_id) &&
          UserGroupController::Ability($user_token, 'ability_edit_own_answer') &&
          (
            UserGroupController::Ability($user_token, 'ability_edit_answer_only_no_comment') ? ($answer->comment_count == 0 ? true : false) : true
          ) &&
          UserGroupController::BeforeTime($user_token, 'time_before_edit_answer', $answer->create_time)
        )
        ||
        UserGroupController::IsAdmin($user_token)
      ) {
        $content_markdown = preg_replace('/\s+/', '', $content_markdown);//去除回车和空格
        
        $answer->content_markdown = $content_markdown;
        $answer->content_rendered = $content_rendered;
        $answer->update_time = Share::ServerTime();
        $is_edit = $answer->save();
      }
    }
    return [
      'is_edit' => $is_edit,
      'answer' => self::GetAnswer($answer_id, $user_token)['answer'],
    ];
  }
  /**
   * 删除回答
   * @param int $answer_id 回答ID
   * @param string $user_token 用户Token
   * @return
   */
  public static function DeleteAnswers($answer_ids, $user_token)
  {
    $is_valid_content =
      $answer_ids != null &&
      $user_token != '' &&
      $answer_ids != [] &&
      $user_token != '';
    $is_delete = false;
    $user_id = TokenController::GetUserId($user_token);
    $delete_ids = [];
    $answers = [];
    if (
      $user_id != null &&
      $is_valid_content
    ) {
      $answers = self::whereIn('answer_id', $answer_ids)->get();
      foreach ($answers as $key => $answer) {
        if (
          (
            TokenController::IsUserSelf($user_token, $answer->user_id) &&
            UserGroupController::Ability($user_token, 'ability_delete_own_answer') &&
            (
              UserGroupController::Ability($user_token, 'ability_delete_answer_only_no_comment') ? ($answer->reply_count == 0 ? true : false) : true
            ) &&
            UserGroupController::BeforeTime($user_token, 'time_before_delete_answer', $answer->create_time)
          )
          ||
          UserGroupController::IsAdmin($user_token)
        ) {
          $answer->delete_time = Share::ServerTime();
          UserController::SubAnswerCount($answer->user_id);
          QuestionController::SubAnswerCount($answer->question_id);
          NotificationController::AddNotification(
            $answer->user_id,
            $user_id,
            'answer_delete',
            0,
            $answer->question_id,
            $answer->answer_id,
            0,
            0
          );

          //联动删除此回答下的评论和回复
          //删除此回答下的评论
          $comments = CommentController::where('commentable_id', '=', $answer->answer_id)
            ->where('commentable_type', '=', 'answer')
            ->get();
          if($comments != null){
            foreach ($comments as $key => $comment) {
              //删除此评论下的回复
              $replys = ReplyController::where('replyable_comment_id', '=', $comment->comment_id)
                ->get();
              if($replys != null){
                foreach($replys as $key => $reply){
                  $reply->delete_time = Share::ServerTime();
                  $reply->save();
    
                  //从用户回复数中减去
                  UserController::SubReplyCount($reply->user_id);
                }
              }
  
              $comment->delete_time = Share::ServerTime();
              $comment->save();
  
              //从用户评论数中减去
              UserController::SubCommentCount($comment->user_id);
            }
          }

          $is_delete = $answer->save();
          array_push($delete_ids, $answer->answer_id);
        }
      }
    }
    return [
      'is_delete' => $is_delete,
      'delete_ids' => $delete_ids,
      'data' => $answers,
    ];
    // if (
    //   $user_id != null
    //   && $is_valid_content
    //   && (
    //     UserGroupController::Ability($user_token, 'ability_delete_own_answer') ||
    //     UserGroupController::IsAdmin($user_token)
    //   )
    // ) {
    //   $is_delete = self::whereIn('answer_id', $answer_ids)
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
