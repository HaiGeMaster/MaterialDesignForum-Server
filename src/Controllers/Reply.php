<?php

/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Reply as ReplyModel;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\Follow as FollowController;
use MaterialDesignForum\Controllers\Comment as CommentController;
use MaterialDesignForum\Controllers\Vote as VoteController;
use MaterialDesignForum\Controllers\Answer as AnswerController;
// use MaterialDesignForum\Controllers\Question as QuestionController;
use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Config\Config;

class Reply extends ReplyModel
{
  /**
   * 添加回复
   * @param int $replyable_id 回复目标的ID
   * @param string $replyable_type 回复目标类型：comment、reply、评论、回复
   * @param string $content 原始正文内容
   * @param string $user_token 用户Token
   * @return
   */
  public static function AddReply(
    $replyable_id,
    $replyable_type,
    $replyable_comment_id,
    $content,
    $user_token,
    $replyable_user_id = 0
  ) {
    $is_valid_content =
      $replyable_id != 0 &&
      $replyable_type != '' &&
      $replyable_comment_id != '' &&
      $content != '' &&
      $user_token != '' &&
      $replyable_user_id != 0;
    $is_add = false;
    $reply_id = null;
    $user_id = TokenController::GetUserId($user_token);
    if (
      $user_id != null
      && $is_valid_content
      && (
        UserGroupController::Ability($user_token, 'ability_create_reply') ||
        UserGroupController::IsAdmin($user_token)
      )
    ) {
      $reply = new self();
      $reply->replyable_id = $replyable_id;
      $reply->replyable_type = $replyable_type;
      $reply->replyable_comment_id = $replyable_comment_id;
      $reply->user_id = $user_id;
      $reply->content = $content;
      $reply->create_time = Share::ServerTime();
      $reply->update_time = Share::ServerTime();
      $reply->replyable_user_id = $replyable_user_id;
      $is_add = $reply->save();
      if ($is_add) {
        UserController::AddReplyCount($user_id);
        switch ($replyable_type) {
          case 'comment':
            CommentController::AddReplyCount($replyable_id);
            break;
          case 'reply':
            CommentController::AddReplyCount($replyable_comment_id);
            self::AddReplyCount($replyable_id);
            break;
        }
        $reply_id = $reply->reply_id;
      }
    }
    return [
      'is_add' => $is_add,
      'reply_id' => $reply_id,
      'reply' => self::GetReply($reply_id, $user_token)['reply'],
    ];
  }
  /**
   * 获取回复
   * @param int $reply_id 回复ID
   * @param string $user_token 用户Token
   * @return Reply|null
   */
  public static function GetReply($reply_id,  $user_token)
  {
    // try {
    //   $reply = self::where('reply_id', '=', $reply_id)
    //     ->where('delete_time', '=', 0)
    //     ->first();
    //   if ($reply != null) {
    //     $reply->user = UserController::GetUserInfo($reply->user_id)['user'];
    //     $reply->vote = VoteController::GetVote($reply->reply_id, 'reply', $user_token)['vote'];
    //     $reply->replyable_user = UserController::GetUserInfo($reply->replyable_user_id)['user'];
    //     // if ($reply->replyable_user_id != 0) {
    //     //   //$reply->replyable_user = UserController::GetUserInfo($reply->replyable_user_id)['user'];
    //     //   //$reply->replyable_user->is_follow = FollowController::IsFollow($user_token, 'user', $reply->replyable_user_id);
    //     //   // $reply->vote = VoteController::GetVote($reply->reply_id, 'reply', $user_token)['vote'];
    //     // }
    //   }
    //   return [
    //     'is_get' => $reply != null,
    //     'reply' => $reply==null?[]:$reply,
    //   ];
    // } catch (\Exception $e) {
    //   return [
    //     'is_get' => false,
    //     'reply' => [],
    //     'error' => $e->getMessage(),
    //   ];
    // }

    $reply = self::where('reply_id', '=', $reply_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($reply != null) {
      $reply->user = UserController::GetUserInfo($reply->user_id)['user'];
      // $reply->vote = VoteController::GetVote($reply->reply_id, 'reply', $user_token)['vote'];
      // $reply->replyable_user = UserController::GetUserInfo($reply->replyable_user_id)['user'];
      if ($reply->replyable_user_id != 0) {
        $reply->replyable_user = UserController::GetUserInfo($reply->replyable_user_id)['user'];
        //$reply->replyable_user->is_follow = FollowController::IsFollow($user_token, 'user', $reply->replyable_user_id);
        $reply->vote = VoteController::GetVote($reply->reply_id, 'reply', $user_token)['vote'];
      }
    }
    return [
      'is_get' => $reply != null,
      'reply' => $reply == null ? [] : $reply,
    ];
  }
  /**
   * 获取回复列表
   * @param int $replyable_comment_id 回复目标的父项评论ID
   * @param string $order 排序方式
   * @param int $page 页码
   * @param string $user_token 用户Token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @return Reply[]|null
   */
  public static function GetReplys(
    $replyable_comment_id,
    $order,
    $page,
    $user_token,
    $per_page = 20,
    $search_keywords = '',
    $search_field = []
  ) {
    if ($search_field == []) {
      $search_field = self::$search_field;
    }

    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);

    $field = $orders['field'];
    $sort = $orders['sort'];

    if ($search_keywords != '') {
      if ($replyable_comment_id != '') {
        $data = self::where('replyable_comment_id', '=', $replyable_comment_id)
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
      if ($replyable_comment_id != '') {
        $data = self::where('replyable_comment_id', '=', $replyable_comment_id)
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

    $GetRequestData = Share::GetRequestData();

    $is_admin = isset($GetRequestData['is_admin']);

    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {

        //以下代码有bug，暂时注释
        // if ($value->replyable_type == 'reply') {

        //   $data['data'][$key]->replyable_parent_id = self::GetReply($value->replyable_id, $user_token)['reply']->replyable_id;
        //   $data['data'][$key]->replyable_parent_type = self::GetReply($value->replyable_id, $user_token)['reply']->replyable_type;

        //   if ($data['data'][$key]->replyable_parent_type == 'reply') {

        //     $data['data'][$key]->replyable_parent_id = self::GetReply($data['data'][$key]->replyable_parent_id, $user_token)['reply']->replyable_id;
        //     $data['data'][$key]->replyable_parent_type = self::GetReply($data['data'][$key]->replyable_parent_id, $user_token)['reply']->replyable_type;

        //     if ($data['data'][$key]->replyable_parent_type == 'comment') {

        //       $data['data'][$key]->replyable_parent_id = CommentController::GetComment($data['data'][$key]->replyable_parent_id, $user_token)['comment']->commentable_id;
        //       $data['data'][$key]->replyable_parent_type = CommentController::GetComment($data['data'][$key]->replyable_parent_id, $user_token)['comment']->commentable_type;

        //       if ($data['data'][$key]->replyable_parent_type == 'answer') {

        //         $data['data'][$key]->replyable_parent_id = AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->question_id;
        //         $data['data'][$key]->replyable_parent_type = 'question'; //AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->answerable_type;
        //       }
        //     }
        //   } else if ($data['data'][$key]->replyable_parent_type == 'comment') {

        //     $data['data'][$key]->replyable_parent_id = CommentController::GetComment($data['data'][$key]->replyable_parent_id, $user_token)['comment']->commentable_id;
        //     $data['data'][$key]->replyable_parent_type = CommentController::GetComment($data['data'][$key]->replyable_parent_id, $user_token)['comment']->commentable_type;

        //     if ($data['data'][$key]->replyable_parent_type == 'answer') {

        //       $data['data'][$key]->replyable_parent_id = AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->question_id;
        //       $data['data'][$key]->replyable_parent_type = 'question'; //AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->answerable_type;
        //     }
        //   } else if ($data['data'][$key]->replyable_parent_type == 'answer') {

        //     $data['data'][$key]->replyable_parent_id = AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->question_id;
        //     $data['data'][$key]->replyable_parent_type = 'question'; //AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->answerable_type;
        //   }
        // } else if ($value->replyable_type == 'comment') {

        //   $data['data'][$key]->replyable_parent_id = CommentController::GetComment($value->replyable_id, $user_token)['comment']->commentable_id;
        //   $data['data'][$key]->replyable_parent_type = CommentController::GetComment($value->replyable_id, $user_token)['comment']->commentable_type;

        //   if ($data['data'][$key]->replyable_parent_type == 'answer') {

        //     $data['data'][$key]->replyable_parent_id = AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->question_id;
        //     $data['data'][$key]->replyable_parent_type = 'question'; //AnswerController::GetAnswer($data['data'][$key]->replyable_parent_id, $user_token)['answer']->answerable_type;
        //   }
        // }

        if(!$is_admin){

          $replyable_parent = self::GetReplyable($value->reply_id, $user_token);
  
          $data['data'][$key]->replyable_parent_id = $replyable_parent['replyable_parent_id'];
          $data['data'][$key]->replyable_parent_type = $replyable_parent['replyable_parent_type'];

        }

        $data['data'][$key]->user = UserController::GetUserInfo($value->user_id, $user_token)['user'];
        $data['data'][$key]->vote = VoteController::GetVote($value->reply_id, 'reply', $user_token)['vote']; //$value->replyable_type
        $data['data'][$key]->replyable_user = UserController::GetUserInfo($value->replyable_user_id, $user_token)['user'];
      }
    }

    return $data;
  }
  /**
   * 编辑回复
   * @param int $reply_id 回复ID
   * @param string $content 原始正文内容
   * @param string $user_token 用户Token
   * @return
   */
  public static function EditReply($reply_id,  $content,  $user_token)
  {
    $is_valid_content = $reply_id != null && $content != null && $user_token != '' && $reply_id != '' && $content != '' && $user_token != '';
    $is_edit = false;
    $user_id = TokenController::GetUserId($user_token);
    $reply = self::where('reply_id', '=', $reply_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($reply != null && $is_valid_content && $user_id != null) {
      if (
        (
          TokenController::IsUserSelf($user_token, $reply->user_id) &&
          UserGroupController::Ability($user_token, 'ability_edit_own_reply') &&
          (
            UserGroupController::Ability($user_token, 'ability_edit_reply_only_no_reply') ? ($reply->reply_count == 0 ? true : false) : true
          ) &&
          UserGroupController::BeforeTime($user_token, 'time_before_edit_reply', $reply->create_time)
        )
        ||
        UserGroupController::IsAdmin($user_token)
      ) {
        $reply->content = $content;
        $reply->update_time = Share::ServerTime();
        $is_edit = $reply->save();
      }
    }
    return [
      'is_edit' => $is_edit,
      'reply' => self::GetReply($reply_id, $user_token)['reply'],
    ];
  }
  /**
   * 删除回复
   * @param int $reply_ids 回复ID数组
   * @param string $user_token 用户Token
   * @return
   */
  public static function DeleteReplys($reply_ids,  $user_token)
  {
    $is_valid_content = $reply_ids != null && $user_token != '' && $reply_ids != '' && $user_token != '';
    $is_delete = false;
    $user_id = TokenController::GetUserId($user_token);
    $delete_ids = [];
    $replys = [];
    if (
      $user_id != null &&
      $is_valid_content
    ) {
      $replys = self::whereIn('reply_id', $reply_ids)->get();
      foreach ($replys as $key => $reply) {
        if (
          (
            TokenController::IsUserSelf($user_token, $reply->user_id) &&
            UserGroupController::Ability($user_token, 'ability_delete_own_reply') &&
            (
              UserGroupController::Ability($user_token, 'ability_delete_reply_only_no_reply') ? ($reply->reply_count == 0 ? true : false) : true
            ) &&
            UserGroupController::BeforeTime($user_token, 'time_before_delete_reply', $reply->create_time)
          )
          ||
          UserGroupController::IsAdmin($user_token)
        ) {
          $reply->delete_time = Share::ServerTime();
          UserController::SubReplyCount($reply->user_id);
          switch ($reply->replyable_type) {
            case 'comment':
              CommentController::SubReplyCount($reply->replyable_id);
              break;
            case 'reply':
              CommentController::SubReplyCount($reply->replyable_comment_id);
              self::SubReplyCount($reply->replyable_id);
              break;
          }

          //联动删除此回复的所有回复
          //删除此回复的所有回复
          $reply_replys = self::where('replyable_id', '=', $reply->reply_id)
            ->where('replyable_type', '=', 'reply')
            ->get();
          if($reply_replys!=null){
            foreach ($reply_replys as $key => $reply_reply) {
              $reply_reply->delete_time = Share::ServerTime();
              $reply_reply->save();

              UserController::SubReplyCount($reply_reply->user_id);
              self::SubReplyCount($reply_reply->replyable_id);
            }
          }

          $is_delete = $reply->save();
          array_push($delete_ids, $reply->reply_id);
        }
      }
    }
    return [
      'is_delete' => $is_delete,
      'delete_ids' => $delete_ids,
      'data' => $replys,
    ];
    // if ($is_valid_content) {
    //   $user_id = TokenController::GetUserId($user_token);
    //   if ($user_id != null) {
    //     $is_delete = self::whereIn('reply_id', $reply_ids)
    //       ->where('delete_time', '=', 0)
    //       ->update([
    //         'delete_time' => Share::ServerTime(),
    //       ]);
    //   }
    // }
    // return [
    //   'is_delete' => $is_delete,
    // ];
  }
  /**
   * 获取回复的最终目标对象是question还是article的id和type
   * @param int $reply_id 回复ID
   * @param string $user_token 用户Token
   * @return
   */
  public static function GetReplyable($reply_id,  $user_token)
  {
    $reply = self::where('reply_id', '=', $reply_id)
      ->where('delete_time', '=', 0)
      ->first();
    $replyable_data = null;
    $replyable_id = null;
    $replyable_type = null;
    try{
      if ($reply != null) {
        if ($reply->replyable_type == 'reply') {
          // $replyable_id = self::GetReply($reply->replyable_id, $user_token)['reply']->replyable_id;
          // $replyable_type = self::GetReply($reply->replyable_id, $user_token)['reply']->replyable_type;
          $replyable_data = self::where('reply_id', '=', $reply->replyable_id)
            ->first();
          $replyable_id = $replyable_data->replyable_id;
          $replyable_type = $replyable_data->replyable_type;
          if ($replyable_type == 'reply') {
            // $replyable_id = self::GetReply($replyable_id, $user_token)['reply']->replyable_id;
            // $replyable_type = self::GetReply($replyable_id, $user_token)['reply']->replyable_type;
            $replyable_data = self::where('reply_id', '=', $replyable_id)
              ->first();
            $replyable_id = $replyable_data->replyable_id;
            $replyable_type = $replyable_data->replyable_type;
            if ($replyable_type == 'comment') {
              // $replyable_id = CommentController::GetComment($replyable_id, $user_token)['comment']->commentable_id;
              // $replyable_type = CommentController::GetComment($replyable_id, $user_token)['comment']->commentable_type;
              $replyable_data = CommentController::where('comment_id', '=', $replyable_id)
                ->first();
              $replyable_id = $replyable_data->commentable_id;
              $replyable_type = $replyable_data->commentable_type;
              if ($replyable_type == 'answer') {
                // $replyable_id = AnswerController::GetAnswer($replyable_id, $user_token)['answer']->question_id;
                // $replyable_type = 'question';
                $replyable_data = AnswerController::where('answer_id', '=', $replyable_id)
                  ->first();
                $replyable_id = $replyable_data->question_id;
                $replyable_type = 'question';
              }
            }
          } else if ($replyable_type == 'comment') {
            // $replyable_id = CommentController::GetComment($replyable_id, $user_token)['comment']->commentable_id;
            // $replyable_type = CommentController::GetComment($replyable_id, $user_token)['comment']->commentable_type;
            $replyable_data = CommentController::where('comment_id', '=', $replyable_id)
              ->first();
            $replyable_id = $replyable_data->commentable_id;
            $replyable_type = $replyable_data->commentable_type;
            if ($replyable_type == 'answer') {
              // $replyable_id = AnswerController::GetAnswer($replyable_id, $user_token)['answer']->question_id;
              // $replyable_type = 'question';
              $replyable_data = AnswerController::where('answer_id', '=', $replyable_id)
                ->first();
              $replyable_id = $replyable_data->question_id;
              $replyable_type = 'question';
            }
          } else if ($replyable_type == 'answer') {
            // $replyable_id = AnswerController::GetAnswer($replyable_id, $user_token)['answer']->question_id;
            // $replyable_type = 'question';
            $replyable_data = AnswerController::where('answer_id', '=', $replyable_id)
              ->first();
            $replyable_id = $replyable_data->question_id;
            $replyable_type = 'question';
          }
        } else if ($reply->replyable_type == 'comment') {
          // $replyable_id = CommentController::GetComment($reply->replyable_id, $user_token)['comment']->commentable_id;
          // $replyable_type = CommentController::GetComment($reply->replyable_id, $user_token)['comment']->commentable_type;
          $replyable_data = CommentController::where('comment_id', '=', $reply->replyable_id)
            ->first();
          $replyable_id = $replyable_data->commentable_id;
          $replyable_type = $replyable_data->commentable_type;
          if ($replyable_type == 'answer') {
            // $replyable_id = AnswerController::GetAnswer($replyable_id, $user_token)['answer']->question_id;
            // $replyable_type = 'question';
            $replyable_data = AnswerController::where('answer_id', '=', $replyable_id)
              ->first();
            $replyable_id = $replyable_data->question_id;
            $replyable_type = 'question';
          }
        }
      }
    }catch(\Exception $e){
      return [
        'replyable_parent_id' => null,
        'replyable_parent_type' => null,
        'error' => $e->getMessage(),
      ];
    }
    return [
      'replyable_parent_id' => $replyable_id,
      'replyable_parent_type' => $replyable_type,
    ];
  }
}
