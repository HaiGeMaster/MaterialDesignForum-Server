<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Article extends Eloquent
{
  protected $table = 'article';
  public $timestamps = false;
  protected $primaryKey = 'article_id';
  protected $fillable = [
    'article_id', // 这个字段不需要
    'user_id',
    'title',
    'content_markdown',
    'content_rendered',
    'comment_count',
    'follower_count',
    'vote_count',
    'vote_up_count',
    'vote_down_count',
    'create_time',
    'update_time',
    'delete_time'
  ];
  // 搜索字段
  public static $search_field = ['title','content_markdown'];
  /**
   * 添加文章的评论数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function AddCommentCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    $article->comment_count += $count;
    return $article->save();
  }
  /**
   * 添加文章的关注者数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function AddFollowerCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    $article->follower_count += $count;
    return $article->save();
  }
  /**
   * 添加文章的赞成票数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function AddVoteUpCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    $article->vote_up_count += $count;
    $article->vote_count += $count;
    return $article->save();
  }
  /**
   * 添加文章的反对票数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function AddVoteDownCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    $article->vote_down_count += $count;
    $article->vote_count -= $count;
    return $article->save();
  }
  /**
   * 减少文章的评论数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function SubCommentCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    if($article->comment_count <= 0){
      $article->comment_count = 0;
      return $article->save();
    }
    $article->comment_count -= $count;
    return $article->save();
  }
  /**
   * 减少文章的关注者数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function SubFollowerCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    if($article->follower_count <= 0){
      $article->follower_count = 0;
      return $article->save();
    }
    $article->follower_count -= $count;
    return $article->save();
  }
  /**
   * 减少文章的赞成票数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function SubVoteUpCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    $article->vote_up_count -= $count;
    $article->vote_count -= $count;
    return $article->save();
  }
  /**
   * 减少文章的反对票数量
   * @param int $article_id 文章ID
   * @param int $count 文章数量
   * @return bool
   */
  public static function SubVoteDownCount($article_id, $count = 1): bool
  {
    $article = self::find($article_id);
    $article->vote_down_count -= $count;
    $article->vote_count += $count;
    return $article->save();
  }
}
