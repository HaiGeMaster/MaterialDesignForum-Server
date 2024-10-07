<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Reply extends Eloquent
{
  protected $table = 'reply';
  public $timestamps = false;
  protected $primaryKey = 'reply_id';
  protected $fillable = [
    'reply_id', // 这个字段不需要
    'replyable_id',
    'replyable_type',
    'replyable_user_id',
    'replyable_comment_id',
    'user_id', //准备废弃
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
   * 添加回复的回复数量
   * @param int $reply_id 回复ID
   * @param int $count 回复数量
   * @return bool
   */
  public static function AddReplyCount($reply_id, $count = 1): bool
  {
    $reply = self::find($reply_id);
    $reply->reply_count += $count;
    return $reply->save();
  }
  /**
   * 添加回复的赞成票数量
   * @param int $reply_id 回复ID
   * @param int $count 回复数量
   * @return bool
   */
  public static function AddVoteUpCount($reply_id, $count = 1): bool
  {
    $reply = self::find($reply_id);
    $reply->vote_up_count += $count;
    $reply->vote_count += $count;
    return $reply->save();
  }
  /**
   * 添加回复的反对票数量
   * @param int $reply_id 回复ID
   * @param int $count 回复数量
   * @return bool
   */
  public static function AddVoteDownCount($reply_id, $count = 1): bool
  {
    $reply = self::find($reply_id);
    $reply->vote_down_count += $count;
    $reply->vote_count -= $count;
    return $reply->save();
  }
  /**
   * 减少回复的回复数量
   * @param int $reply_id 回复ID
   * @param int $count 回复数量
   * @return bool
   */
  public static function SubReplyCount($reply_id, $count = 1): bool
  {
    $reply = self::find($reply_id);
    $reply->reply_count -= $count;
    return $reply->save();
  }
  /**
   * 减少回复的赞成票数量
   * @param int $reply_id 回复ID
   * @param int $count 回复数量
   * @return bool
   */
  public static function SubVoteUpCount($reply_id, $count = 1): bool
  {
    $reply = self::find($reply_id);
    $reply->vote_up_count -= $count;
    $reply->vote_count -= $count;
    return $reply->save();
  }
  /**
   * 减少回复的反对票数量
   * @param int $reply_id 回复ID
   * @param int $count 回复数量
   * @return bool
   */
  public static function SubVoteDownCount($reply_id, $count = 1): bool
  {
    $reply = self::find($reply_id);
    $reply->vote_down_count -= $count;
    $reply->vote_count += $count;
    return $reply->save();
  }
}
