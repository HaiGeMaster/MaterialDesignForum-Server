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

class Answer extends Eloquent
{
  protected $table = 'answer';
  public $timestamps = false;
  protected $primaryKey = 'answer_id';
  /**
   * @typedef AnswerModel 回答
   * @property int $answer_id 回答ID
   * @property int $question_id 问题ID
   * @property int $user_id 用户ID
   * @property string $content_markdown 回答内容Markdown
   * @property string $content_rendered 回答内容渲染
   * @property int $comment_count 评论数量
   * @property int $vote_count 投票数量
   * @property int $vote_up_count 赞成票数量
   * @property int $vote_down_count 反对票数量
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $delete_time 删除时间
   */
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
