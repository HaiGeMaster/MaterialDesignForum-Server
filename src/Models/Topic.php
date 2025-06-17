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

class Topic extends Eloquent
{
  protected $table = 'topic';
  public $timestamps = false;
  protected $primaryKey = 'topic_id';
  protected $casts = [
    'cover' => 'array',
  ];
  protected $fillable = [
    'topic_id', // 这个字段不需要
    'user_id',
    'name',
    'cover',
    'description',
    'article_count',
    'question_count',
    'follower_count',
    'create_time',
    'update_time',
    'delete_time',
  ];
  // 搜索字段
  public static $search_field = ['name', 'description'];
  /**
   * 增加文章数量
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function AddArticleCount($topic_id, $count = 1): bool
  {
    $topic = self::find($topic_id);
    if ($topic) {
      $topic->article_count = $topic->article_count + $count;
      return $topic->save();
    } else {
      return false;
    }
  }
  /**
   * 减少文章数量
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function SubArticleCount($topic_id, $count = 1): bool
  {
    $topic = self::find($topic_id);
    if ($topic->article_count <= 0) {
      return true;
    }
    $topic->article_count -= $count;
    return $topic->save();
  }
  /**
   * 增加问题数量
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function AddQuestionCount($topic_id, $count = 1): bool
  {
    $topic = self::find($topic_id);
    if ($topic) {
      $topic->question_count = $topic->question_count + $count;
      return $topic->save();
    } else {
      return false;
    }
  }
  /**
   * 减少问题数量
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function SubQuestionCount($topic_id, $count = 1): bool
  {
    $topic = self::find($topic_id);
    if ($topic->question_count <= 0) {
      return true;
    }
    $topic->question_count -= $count;
    return $topic->save();
  }
  /**
   * 增加关注者数量
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function AddFollowerCount($topic_id, $count = 1): bool
  {
    $topic = self::find($topic_id);
    if ($topic) {
      if ($topic->follower_count <= 0) {
        $topic->follower_count = 0; // 如果关注者数量小于0，重置为0
      }
      $topic->follower_count = $topic->follower_count + $count;
      return $topic->save();
    } else {
      return false;
    }
  }
  /**
   * 减少关注者数量
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function SubFollowerCount($topic_id, $count = 1): bool
  {
    $topic = self::find($topic_id);
    if ($topic->follower_count <= 0) {
      return true;
    }
    $topic->follower_count -= $count;
    return $topic->save();
  }
}
