<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Answer extends Eloquent
{
  protected $table = 'answer';
  public $timestamps = false;
  protected $primaryKey = 'answer_id';
  protected $fillable = [
    'answer_id', // 这个字段不需要
    'question_id',
    'user_id',
    'content_markdown',
    'content_rendered',
    'comment_count',
    'vote_count',
    'vote_up_count',
    'vote_down_count',
    'create_time',
    'update_time',
    'delete_time'
  ];
  // 搜索字段
  public static $search_field = ['content_markdown'];
  /**
   * 添加回答的评论数量
   * @param int $answer_id 回答ID
   * @param int $count 回答数量
   * @return bool
   */
  public static function AddCommentCount($answer_id, $count = 1): bool
  {
    $answer = self::find($answer_id);
    $answer->comment_count += $count;
    return $answer->save();
  }
  /**
   * 添加回答的赞成票数量
   * @param int $answer_id 回答ID
   * @param int $count 回答数量
   * @return bool
   */
  public static function AddVoteUpCount($answer_id, $count = 1): bool
  {
    $answer = self::find($answer_id);
    $answer->vote_up_count += $count;
    $answer->vote_count += $count;
    return $answer->save();
  }
  /**
   * 添加回答的反对票数量
   * @param int $answer_id 回答ID
   * @param int $count 回答数量
   * @return bool
   */
  public static function AddVoteDownCount($answer_id, $count = 1): bool
  {
    $answer = self::find($answer_id);
    $answer->vote_down_count += $count;
    $answer->vote_count -= $count;
    return $answer->save();
  }
  /**
   * 减少回答的评论数量
   * @param int $answer_id 回答ID
   * @param int $count 回答数量
   * @return bool
   */
  public static function SubCommentCount($answer_id, $count = 1): bool
  {
    $answer = self::find($answer_id);
    if($answer->comment_count <= 0){
      $answer->comment_count = 0;
      return $answer->save();
    }
    $answer->comment_count -= $count;
    return $answer->save();
  }
  /**
   * 减少回答的赞成票数量
   * @param int $answer_id 回答ID
   * @param int $count 回答数量
   * @return bool
   */
  public static function SubVoteUpCount($answer_id, $count = 1): bool
  {
    $answer = self::find($answer_id);
    $answer->vote_up_count -= $count;
    $answer->vote_count -= $count;
    return $answer->save();
  }
  /**
   * 减少回答的反对票数量
   * @param int $answer_id 回答ID
   * @param int $count 回答数量
   * @return bool
   */
  public static function SubVoteDownCount($answer_id, $count = 1): bool
  {
    $answer = self::find($answer_id);
    $answer->vote_down_count -= $count;
    $answer->vote_count += $count;
    return $answer->save();
  }
}
