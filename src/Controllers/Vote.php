<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Models\Vote as VoteModel;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\Reply as ReplyController;
use MaterialDesignForum\Controllers\Answer as AnswerController;
use MaterialDesignForum\Controllers\Article as ArticleController;
use MaterialDesignForum\Controllers\Comment as CommentController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\Notification as NotificationController;


class Vote extends VoteModel
{
  /**
   * 投票
   * @param string $user_token 用户Token
   * @param int $votable_id 投票对象ID
   * @param string $votable_type 投票对象类型 question、answer、article、comment、reply
   * @param int $type 投票类型 up down
   * @return
   */
  public static function Vote($user_token, $votable_id, $votable_type, $type)
  {
    $is_valid_content =
      $user_token != '' &&
      $votable_id != null &&
      $votable_type != null &&
      $type != null &&
      $user_token != '' &&
      $votable_id != '' &&
      $votable_type != '' &&
      $type != '';
    $user_id = TokenController::GetUserId($user_token);
    $is_add_vote = false;
    $is_sub_vote = false;
    $vote = null;
    if (
      $user_id != null
      && $is_valid_content
      && (
        UserGroupController::Ability($user_token, 'ability_vote') ||
        UserGroupController::IsAdmin($user_token)
      )
    ) {
      $vote = VoteModel::where('user_id', '=', $user_id)
        ->where('votable_id', '=', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', $type)
        ->first();
      if ($vote != null) {
        $is_sub_vote = VoteModel::where('user_id', '=', $user_id)
          ->where('votable_id', '=', $votable_id)
          ->where('votable_type', '=', $votable_type)
          ->where('type', '=', $type)
          ->delete() != 0;
        if ($is_sub_vote) {
          if ($type == 'up') {
            switch ($votable_type) {
              case 'answer':
                AnswerController::SubVoteUpCount($votable_id);
                NotificationController::AddInteractionNotification(
                  AnswerController::GetAnswerOwnerId($votable_id),
                  $user_id,
                  'answer_like',
                  null,
                  null,
                  0,
                  0,
                  0,
                  $votable_id,
                );
                break;
              case 'article':
                ArticleController::SubVoteUpCount($votable_id);
                NotificationController::AddInteractionNotification(
                  ArticleController::GetArticleOwnerId($votable_id),
                  $user_id,
                  'article_like',
                  null,
                  null,
                  0,
                  0,
                  $votable_id,
                );

                break;
              case 'comment':
                CommentController::SubVoteUpCount($votable_id);
                NotificationController::AddInteractionNotification(
                  CommentController::GetCommentOwnerId($votable_id),
                  $user_id,
                  'comment_like',
                  null,
                  null,
                  0,
                  0,
                  0,
                  0,
                  0,
                  $votable_id,
                );
                break;
              case 'reply':
                ReplyController::SubVoteUpCount($votable_id);
                NotificationController::AddInteractionNotification(
                  ReplyController::GetReplyOwnerId($votable_id),
                  $user_id,
                  'reply_like',
                  null,
                  null,
                  0,
                  0,
                  0,
                  0,
                  0,
                  0,
                  $votable_id,
                );
                break;
            }
          } else if ($type == 'down') {
            switch ($votable_type) {
              case 'answer':
                AnswerController::SubVoteDownCount($votable_id);
                break;
              case 'article':
                ArticleController::SubVoteDownCount($votable_id);
                break;
              case 'comment':
                CommentController::SubVoteDownCount($votable_id);
                break;
              case 'reply':
                ReplyController::SubVoteDownCount($votable_id);
                break;
            }
          }
        }
      } else {
        $vote = new VoteModel;
        $vote->user_id = $user_id;
        $vote->votable_id = $votable_id;
        $vote->votable_type = $votable_type;
        $vote->type = $type;
        $vote->create_time = Share::ServerTime();
        $is_add_vote = $vote->save();
        if ($is_add_vote) {
          if ($type == 'up') {
            switch ($votable_type) {
              case 'answer':
                AnswerController::AddVoteUpCount($votable_id);
                break;
              case 'article':
                ArticleController::AddVoteUpCount($votable_id);
                break;
              case 'comment':
                CommentController::AddVoteUpCount($votable_id);
                break;
              case 'reply':
                ReplyController::AddVoteUpCount($votable_id);
                break;
            }
          } else if ($type == 'down') {
            switch ($votable_type) {
              case 'answer':
                AnswerController::AddVoteDownCount($votable_id);
                break;
              case 'article':
                ArticleController::AddVoteDownCount($votable_id);
                break;
              case 'comment':
                CommentController::AddVoteDownCount($votable_id);
                break;
              case 'reply':
                ReplyController::AddVoteDownCount($votable_id);
                break;
            }
          }
        }
      }
    }
    return [
      'is_add_vote' => $is_add_vote,
      'is_sub_vote' => $is_sub_vote,
      'vote' => self::GetVote($votable_id, $votable_type, $user_token)['vote'],
    ];
  }
  /**
   * 获取投票
   * @param int $votable_id 投票对象ID
   * @param string $votable_type 投票对象类型 question、answer、article、comment、reply
   * @param string $user_token 用户Token
   * @return array is_get:是否获取 vote:投票信息[]
   */
  public static function GetVote($votable_id, $votable_type, $user_token)
  {
    $is_valid_content =
      $votable_id != null &&
      $user_token != '' &&
      $votable_id != '' &&
      $user_token != '';
    // $user_id = TokenController::GetUserId($user_token);

    $up_count = 0;
    $down_count = 0;
    $up_value = false;
    $down_value = false;
    $vote = null;
    // if ($user_id != null && $is_valid_content) {
    //   $vote_up = VoteModel::where('user_id', '=', $user_id)
    //     ->where('votable_id', '=', $votable_id)
    //     ->where('votable_type', '=', $votable_type)
    //     ->where('type', '=', 'up')
    //     ->first();
    //   $vote_down = VoteModel::where('user_id', '=', $user_id)
    //     ->where('votable_id', '=', $votable_id)
    //     ->where('votable_type', '=', $votable_type)
    //     ->where('type', '=', 'down')
    //     ->first();
    //   $up_count = VoteModel::where('votable_id', '=', $votable_id)
    //     ->where('votable_type', '=', $votable_type)
    //     ->where('type', '=', 'up')
    //     ->count();
    //   $down_count = VoteModel::where('votable_id', $votable_id)
    //     ->where('votable_type', '=', $votable_type)
    //     ->where('type', '=', 'down')
    //     ->count();
    //   $up_value = $vote_up != null && $vote_up->type == 'up';
    //   $down_value = $vote_down != null && $vote_down->type == 'down';
    // }
    if ($is_valid_content) {
      $user_id = TokenController::GetUserId($user_token);
      $vote_up = VoteModel::where('user_id', '=', $user_id)
        ->where('votable_id', '=', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', 'up')
        ->first();
      $vote_down = VoteModel::where('user_id', '=', $user_id)
        ->where('votable_id', '=', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', 'down')
        ->first();
      $up_count = VoteModel::where('votable_id', '=', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', 'up')
        ->count();
      $down_count = VoteModel::where('votable_id', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', 'down')
        ->count();
      $up_value = $vote_up != null && $vote_up->type == 'up';
      $down_value = $vote_down != null && $vote_down->type == 'down';
    }else{
      $up_count = VoteModel::where('votable_id', '=', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', 'up')
        ->count();
      $down_count = VoteModel::where('votable_id', $votable_id)
        ->where('votable_type', '=', $votable_type)
        ->where('type', '=', 'down')
        ->count();
    }
    return [
      'is_get' => $vote != null,
      'vote' => [
        'votable_id' => $votable_id,
        'votable_type' => $votable_type,
        'up' => [
          'count' => $up_count,
          'value' => $up_value
        ],
        'down' => [
          'count' => $down_count,
          'value' => $down_value
        ],
      ]
    ];
  }
}
