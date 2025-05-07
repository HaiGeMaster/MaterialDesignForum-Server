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

class User extends Eloquent
{
  protected $table = 'user';
  public $timestamps = false;
  protected $primaryKey = 'user_id';
  protected $casts = [
    'avatar' => 'array', //这个字段的值将会被自动转换为 PHP 数组：
    'cover' => 'array',
  ];
  protected $fillable = [
    'user_id', // 用户ID// 这个字段不需要
    'user_group_id', // 用户组ID
    'username', // 用户名
    'email', // 邮箱
    'avatar', // 头像
    'cover', // 封面
    'password', // 密码
    'create_ip', // 创建IP
    'create_location', // 创建位置
    'last_login_time', // 最后登录时间
    'last_login_ip', // 最后登录IP
    'last_login_location', // 最后登录位置
    'follower_count', // 粉丝数
    'followee_count', // 关注数
    'following_topic_count', // 关注的话题数
    'following_article_count', // 关注的文章数
    'following_question_count', // 关注的问题数
    'topic_count', // 话题数
    'article_count', // 文章数
    'question_count', // 问题数
    'answer_count', // 回答数
    'comment_count', // 评论数
    'reply_count', // 回复数
    'notification_unread', // 未读通知数
    // 'inbox_system', // 系统消息数
    // 'inbox_user_group', // 用户组消息数
    // 'inbox_private_message', // 私信数
    'headline', // 个人简介
    'bio', // 个人介绍
    'blog', // 博客链接
    'company', // 公司
    'location', // 地址
    'language', // 语言
    'create_time', // 创建时间
    'update_time', // 更新时间
    'disable_time' // 禁用时间
  ];
  // 搜索字段
  public static $search_field = [
    'username', 'headline',
    'bio'
  ];
  /**
   * 添加用户的 关注我的人数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddFollowerCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->follower_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我关注的人数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddFolloweeCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->followee_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我关注的话题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddFollowingTopicCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->following_topic_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我关注的文章数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddFollowingArticleCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->following_article_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我关注的问题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddFollowingQuestionCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->following_question_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的发表的话题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddTopicCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->topic_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的发表的文章数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddArticleCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->article_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的发表的问题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddQuestionCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->question_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的发表的回答数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddAnswerCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->answer_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的发表的评论数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddCommentCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->comment_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的发表的回复数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddReplyCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->reply_count += $count;
    return $user->save();
  }
  /**
   * 添加用户的 我的未读通知数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function AddNotificationCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    $user->notification_unread += $count;
    return $user->save();
  }
  // /**
  //  * 添加用户的 我的未读系统消息数
  //  * @param int $user_id 用户ID
  //  * @param int $count 数量
  //  * @return bool
  //  */
  // public static function AddInboxSystem($user_id, $count = 1): bool
  // {
  //   $user = self::find($user_id);
  //   $user->inbox_system += $count;
  //   return $user->save();
  // }
  // /**
  //  * 添加用户的 我的未读用户组消息数
  //  * @param int $user_id 用户ID
  //  * @param int $count 数量
  //  * @return bool
  //  */
  // public static function AddInboxUserGroup($user_id, $count = 1): bool
  // {
  //   $user = self::find($user_id);
  //   $user->inbox_user_group += $count;
  //   return $user->save();
  // }
  // /**
  //  * 添加用户的 我的私信数
  //  * @param int $user_id 用户ID
  //  * @param int $count 数量
  //  * @return bool
  //  */
  // public static function AddInboxPrivateMessage($user_id, $count = 1): bool
  // {
  //   $user = self::find($user_id);
  //   $user->inbox_private_message += $count;
  //   return $user->save();
  // }
  /**
   * 减少用户的 关注我的人数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubFollowerCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->follower_count <= 0){
      $user->follower_count = 0;
      return $user->save();
    }
    $user->follower_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我关注的人数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubFolloweeCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->followee_count <= 0){
      $user->followee_count = 0;
      return $user->save();
    }
    $user->followee_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我关注的话题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubFollowingTopicCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->following_topic_count <= 0){
      $user->following_topic_count = 0;
      return $user->save();
    }
    $user->following_topic_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我关注的文章数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubFollowingArticleCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->following_article_count <= 0){
      $user->following_article_count = 0;
      return $user->save();
    }
    $user->following_article_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我关注的问题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubFollowingQuestionCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->following_question_count <= 0){
      $user->following_question_count = 0;
      return $user->save();
    }
    $user->following_question_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的发表的话题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubTopicCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->topic_count <= 0){
      $user->topic_count = 0;
      return $user->save();
    }
    $user->topic_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的发表的文章数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubArticleCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->article_count <= 0){
      $user->article_count = 0;
      return $user->save();
    }
    $user->article_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的发表的问题数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubQuestionCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->question_count <= 0){
      $user->question_count = 0;
      return $user->save();
    }
    $user->question_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的发表的回答数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubAnswerCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->answer_count <= 0){
      $user->answer_count = 0;
      return $user->save();
    }
    $user->answer_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的发表的评论数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubCommentCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->comment_count <= 0){
      $user->comment_count = 0;
      return $user->save();
    }
    $user->comment_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的发表的回复数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubReplyCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->reply_count <= 0){
      $user->reply_count = 0;
      return $user->save();
    }
    $user->reply_count -= $count;
    return $user->save();
  }
  /**
   * 减少用户的 我的未读通知数
   * @param int $user_id 用户ID
   * @param int $count 数量
   * @return bool
   */
  public static function SubNotificationCount($user_id, $count = 1): bool
  {
    $user = self::find($user_id);
    if ($user->notification_unread <= 0){
      $user->notification_unread = 0;
      return $user->save();
    }
    $user->notification_unread -= $count;
    return $user->save();
  }
  // /**
  //  * 减少用户的 我的系统消息数
  //  * @param int $user_id 用户ID
  //  * @param int $count 数量
  //  * @return bool
  //  */
  // public static function SubInboxSystem($user_id, $count = 1): bool
  // {
  //   $user = self::find($user_id);
  //   if ($user->inbox_system <= 0){
  //     $user->inbox_system = 0;
  //     return $user->save();
  //   }
  //   $user->inbox_system -= $count;
  //   return $user->save();
  // }
  // /**
  //  * 减少用户的 我的用户组消息数
  //  * @param int $user_id 用户ID
  //  * @param int $count 数量
  //  * @return bool
  //  */
  // public static function SubInboxUserGroup($user_id, $count = 1): bool
  // {
  //   $user = self::find($user_id);
  //   if ($user->inbox_user_group <= 0){
  //     $user->inbox_user_group = 0;
  //     return $user->save();
  //   $user->inbox_user_group -= $count;
  //   return $user->save();
  // }
  // /**
  //  * 减少用户的 我的私信数
  //  * @param int $user_id 用户ID
  //  * @param int $count 数量
  //  * @return bool
  //  */
  // public static function SubInboxPrivateMessage($user_id, $count = 1): bool
  // {
  //   $user = self::find($user_id);
  //   if ($user->inbox_private_message <= 0){
  //     $user->inbox_private_message = 0;
  //     return $user->save();
  //   }
  //   $user->inbox_private_message -= $count;
  //   return $user->save();
  // }
  public static function HandlePassword($password)
  {
    //return password_hash($password, PASSWORD_DEFAULT);
    return md5($password);
  }
  public static function PasswordHash($password, $hash)
  {
    return password_verify($password, $hash);
  }
  public static function AvatarStringToArray($avatar)
  {
    return json_decode($avatar, true);
  }
}
