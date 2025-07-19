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

class Comment extends Eloquent
{
  protected $table = 'comment';
  public $timestamps = false;
  protected $primaryKey = 'comment_id';
  /**
   * @typedef CommentModel 评论
   * @property int $comment_id 评论ID
   * @property int $commentable_id 评论关联ID
   * @property string $commentable_type 评论关联类型
   * @property int $user_id 用户ID
   * @property string $content 评论内容
   * @property int $reply_count 评论回复数量
   * @property int $vote_count 评论投票数量
   * @property int $vote_up_count 评论赞成票数量
   * @property int $vote_down_count 评论反对票数量
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $delete_time 删除时间
   */
  protected $fillable = [
    'comment_id', // 这个字段不需要
    'commentable_id',
    'commentable_type', // article、question、answer、文章、提问、回答
    'user_id',
    'content',
    'reply_count',
    'vote_count',
    'vote_up_count',
    'vote_down_count',
    'create_time',
    'update_time',
    'delete_time'
  ];
  // 搜索字段
  public static $search_field = ['content'];
  /**
   * 添加评论的回复数量
   * @param int $comment_id 评论ID
   * @param int $count 评论数量
   * @return bool
   */
  public static function AddReplyCount($comment_id, $count = 1): bool
  {
    $comment = self::find($comment_id);
    $comment->reply_count += $count;
    return $comment->save();
  }
  /**
   * 添加评论的赞成票数量
   * @param int $comment_id 评论ID
   * @param int $count 评论数量
   * @return bool
   */
  public static function AddVoteUpCount($comment_id, $count = 1): bool
  {
    $comment = self::find($comment_id);
    $comment->vote_up_count += $count;
    $comment->vote_count += $count;
    return $comment->save();
  }
  /**
   * 添加评论的反对票数量
   * @param int $comment_id 评论ID
   * @param int $count 评论数量
   * @return bool
   */
  public static function AddVoteDownCount($comment_id, $count = 1): bool
  {
    $comment = self::find($comment_id);
    $comment->vote_down_count += $count;
    $comment->vote_count -= $count;
    return $comment->save();
  }
  /**
   * 减少评论的回复数量
   * @param int $comment_id 评论ID
   * @param int $count 评论数量
   * @return bool
   */
  public static function SubReplyCount($comment_id, $count = 1): bool
  {
    $comment = self::find($comment_id);
    if($comment->reply_count <= 0){
      $comment->reply_count = 0;
      return $comment->save();
    }
    $comment->reply_count -= $count;

    return $comment->save();
  }
  /**
   * 减少评论的赞成票数量
   * @param int $comment_id 评论ID
   * @param int $count 评论数量
   * @return bool
   */
  public static function SubVoteUpCount($comment_id, $count = 1): bool
  {
    $comment = self::find($comment_id);
    $comment->vote_up_count -= $count;
    $comment->vote_count -= $count;
    return $comment->save();
  }
  /**
   * 减少评论的反对票数量
   * @param int $comment_id 评论ID
   * @param int $count 评论数量
   * @return bool
   */
  public static function SubVoteDownCount($comment_id, $count = 1): bool
  {
    $comment = self::find($comment_id);
    $comment->vote_down_count -= $count;
    $comment->vote_count += $count;
    return $comment->save();
  }
}
