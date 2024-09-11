<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent
{
  protected $table = 'comment';
  public $timestamps = false;
  protected $primaryKey = 'comment_id';
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
