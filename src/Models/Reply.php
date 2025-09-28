<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Vuetify2
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-MDUI2
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Reply extends Eloquent
{
  protected $table = 'reply';
  public $timestamps = false;
  protected $primaryKey = 'reply_id';
  /**
   * @typedef ReplyModel 回复
   * @property int $reply_id 回复ID
   * @property int $replyable_id 回复对象ID
   * @property string $replyable_type 回复对象类型
   * @property int $replyable_user_id 回复对象用户ID
   * @property int $replyable_comment_id 回复对象评论ID
   * @property int $user_id 用户ID
   * @property string $content 内容
   * @property int $reply_count 回复数量
   * @property int $vote_count 投票数量
   * @property int $vote_up_count 赞成投票数量
   * @property int $vote_down_count 反对投票数量
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $delete_time 删除时间
   */
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
    if($reply->reply_count <= 0){
      $reply->reply_count = 0;
      return $reply->save();
    }
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
