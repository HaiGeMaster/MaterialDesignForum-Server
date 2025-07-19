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

class Question extends Eloquent
{
  protected $table = 'question';
  public $timestamps = false;
  protected $primaryKey = 'question_id';
  /**
   * @typedef QuestionModel 问题
   * @property int $question_id 问题ID
   * @property int $user_id 用户ID
   * @property string $title 标题
   * @property string $content_markdown 内容Markdown
   * @property string $content_rendered 内容渲染
   * @property int $comment_count 评论数
   * @property int $answer_count 回答数
   * @property int $follower_count 关注数
   * @property int $vote_count 投票数
   * @property int $vote_up_count 赞成投票数
   * @property int $vote_down_count 反对投票数
   * @property int $last_answer_time 最后回答时间
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $delete_time 删除时间
   */
  protected $fillable = [
    'question_id', // 这个字段不需要
    'user_id',
    'title',
    'content_markdown', //纯文本
    'content_rendered', //渲染后的HTML
    'comment_count',
    'answer_count',
    'follower_count',
    'vote_count', //投票数 vote_up_count-vote_down_count
    'vote_up_count',
    'vote_down_count',
    'last_answer_time',
    'create_time',
    'update_time',
    'delete_time'
  ];
  // 搜索字段
  public static $search_field = ['title','content_markdown'];
  /**
   * 添加评论数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddCommentCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->comment_count += $count;
    return $question->save();
  }
  /**
   * 添加回答数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddAnswerCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->answer_count += $count;
    return $question->save();
  }
  /**
   * 添加关注数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddFollowerCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->follower_count += $count;
    return $question->save();
  }
  /**
   * 添加赞成投票数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddVoteUpCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->vote_up_count += $count;
    $question->vote_count += $count;
    return $question->save();
  }
  /**
   * 添加反对投票数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddVoteDownCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->vote_down_count += $count;
    $question->vote_count -= $count;
    return $question->save();
  }
  /**
   * 减少评论数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubCommentCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    if($question->comment_count <= 0){
      $question->comment_count = 0;
      return $question->save();
    }
    $question->comment_count -= $count;
    return $question->save();
  }
  /**
   * 减少回答数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubAnswerCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    if($question->answer_count <= 0){
      $question->answer_count = 0;
      return $question->save();
    }
    $question->answer_count -= $count;
    return $question->save();
  }
  /**
   * 减少关注数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubFollowerCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    if($question->follower_count <= 0){
      $question->follower_count = 0;
      return $question->save();
    }
    $question->follower_count -= $count;
    return $question->save();
  }
  /**
   * 减少赞成投票数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubVoteUpCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->vote_up_count -= $count;
    $question->vote_count -= $count;
    return $question->save();
  }
  /**
   * 减少反对投票数
   * @param int $question_id 问题ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubVoteDownCount($question_id, $count = 1): bool
  {
    $question = self::find($question_id);
    $question->vote_down_count -= $count;
    $question->vote_count += $count;
    return $question->save();
  }
}
