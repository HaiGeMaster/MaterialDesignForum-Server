<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserGroup extends Eloquent
{
  protected $table = 'user_group';
  public $timestamps = false;
  protected $primaryKey = 'user_group_id';
  protected $fillable = [
    'user_group_id', // 这个字段不需要
    'user_group_name',
    'user_group_description',
    'user_group_icon',
    'user_group_icon_show',
    'user_group_color',
    'user_group_user_count',
    'create_time',
    'update_time',
    'delete_time',
    'is_admin',
    'ability_normal_login',
    'ability_admin_login',
    'ability_admin_manage_user_group',
    'ability_admin_manage_user',
    'ability_admin_manage_topic',
    'ability_admin_manage_question',
    'ability_admin_manage_article',
    'ability_admin_manage_comment',
    'ability_admin_manage_answer',
    'ability_admin_manage_reply',
    'ability_admin_manage_report',
    'ability_admin_manage_option',
    'ability_create_article',
    'ability_create_question',
    'ability_create_answer',
    'ability_create_comment',
    'ability_create_reply',
    'ability_create_topic',
    'ability_edit_own_article',
    'ability_edit_own_question',
    'ability_edit_own_answer',
    'ability_edit_own_comment',
    'ability_edit_own_reply',
    'ability_edit_own_topic',
    'ability_delete_own_article',
    'ability_delete_own_question',
    'ability_delete_own_answer',
    'ability_delete_own_comment',
    'ability_delete_own_reply',
    'ability_delete_own_topic',
    'time_before_edit_article',
    'time_before_edit_question',
    'time_before_edit_answer',
    'time_before_edit_comment',
    'time_before_edit_reply',
    'time_before_edit_topic',
    'time_before_delete_article',
    'time_before_delete_question',
    'time_before_delete_answer',
    'time_before_delete_comment',
    'time_before_delete_reply',
    'time_before_delete_topic',
    'ability_edit_article_only_no_comment',
    'ability_edit_question_only_no_answer',
    'ability_edit_answer_only_no_comment',
    'ability_edit_question_only_no_comment',
    'ability_edit_comment_only_no_reply',
    'ability_edit_topic_only_no_article_or_question',
    'ability_delete_article_only_no_comment',
    'ability_delete_question_only_no_answer',
    'ability_delete_answer_only_no_comment',
    'ability_delete_question_only_no_comment',
    'ability_delete_comment_only_no_reply',
    'ability_delete_topic_only_no_article_or_question',
    'ability_edit_own_info',
    'ability_vote',
  ];
  protected $casts = [
    'user_group_icon_show' => 'boolean',
    'is_admin' => 'boolean',
    'ability_normal_login' => 'boolean',
    'ability_admin_login' => 'boolean',
    'ability_admin_manage_user_group' => 'boolean',
    'ability_admin_manage_user' => 'boolean',
    'ability_admin_manage_topic' => 'boolean',
    'ability_admin_manage_question' => 'boolean',
    'ability_admin_manage_article' => 'boolean',
    'ability_admin_manage_comment' => 'boolean',
    'ability_admin_manage_answer' => 'boolean',
    'ability_admin_manage_reply' => 'boolean',
    'ability_admin_manage_report' => 'boolean',
    'ability_admin_manage_option' => 'boolean',
    'ability_create_article' => 'boolean',
    'ability_create_question' => 'boolean',
    'ability_create_answer' => 'boolean',
    'ability_create_comment' => 'boolean',
    'ability_create_reply' => 'boolean',
    'ability_create_topic' => 'boolean',
    'ability_edit_own_article' => 'boolean',
    'ability_edit_own_question' => 'boolean',
    'ability_edit_own_answer' => 'boolean',
    'ability_edit_own_comment' => 'boolean',
    'ability_edit_own_reply' => 'boolean',
    'ability_edit_own_topic' => 'boolean',
    'ability_delete_own_article' => 'boolean',
    'ability_delete_own_question' => 'boolean',
    'ability_delete_own_answer' => 'boolean',
    'ability_delete_own_comment' => 'boolean',
    'ability_delete_own_reply' => 'boolean',
    'ability_delete_own_topic' => 'boolean',
    'time_before_edit_article' => 'integer',
    'time_before_edit_question' => 'integer',
    'time_before_edit_answer' => 'integer',
    'time_before_edit_comment' => 'integer',
    'time_before_edit_reply' => 'integer',
    'time_before_edit_topic' => 'integer',
    'time_before_delete_article' => 'integer',
    'time_before_delete_question' => 'integer',
    'time_before_delete_answer' => 'integer',
    'time_before_delete_comment' => 'integer',
    'time_before_delete_reply' => 'integer',
    'time_before_delete_topic' => 'integer',
    'ability_edit_article_only_no_comment' => 'boolean',//1
    'ability_edit_question_only_no_answer' => 'boolean',
    'ability_edit_answer_only_no_comment' => 'boolean',
    'ability_edit_question_only_no_comment' => 'boolean',
    'ability_edit_comment_only_no_reply' => 'boolean',
    'ability_edit_reply_only_no_reply' => 'boolean',
    'ability_edit_topic_only_no_article_or_question' => 'boolean',
    'ability_delete_article_only_no_comment' => 'boolean',
    'ability_delete_question_only_no_answer' => 'boolean',
    'ability_delete_answer_only_no_comment' => 'boolean',
    'ability_delete_question_only_no_comment' => 'boolean',
    'ability_delete_comment_only_no_reply' => 'boolean',
    'ability_delete_reply_only_no_reply' => 'boolean',
    'ability_delete_topic_only_no_article_or_question' => 'boolean',
    'ability_edit_own_info' => 'boolean',
    'ability_vote' => 'boolean',//16
  ];
  // 搜索字段
  public static $search_field = [
    'user_group_name',
    'user_group_description'
  ];
  /**
   * 增加用户组人数
   * @param int $user_group_id 用户组ID
   * @return bool 是否成功
   */
  public static function AddUserGroupUserCount($user_group_id, $count = 1): bool
  {
    $user_group = self::find($user_group_id);
    if ($user_group) {
      $user_group->user_group_user_count = $user_group->user_group_user_count + $count;
      return $user_group->save();
    } else {
      return false;
    }
  }
  /**
   * 减少用户组人数
   * @param int $user_group_id 用户组ID
   * @return bool 是否成功
   */
  public static function SubUserGroupUserCount($user_group_id, $count = 1): bool
  {
    $user_group = self::find($user_group_id);
    if ($user_group) {
      $user_group->user_group_user_count = $user_group->user_group_user_count - $count;
      return $user_group->save();
    } else {
      return false;
    }
  }
}
