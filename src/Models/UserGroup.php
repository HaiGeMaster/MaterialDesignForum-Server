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

class UserGroup extends Eloquent
{
  protected $table = 'user_group';
  public $timestamps = false;
  protected $primaryKey = 'user_group_id';
  /**
   * @typedef UserGroupModel 用户组
   * @property int $user_group_id 用户组ID
   * @property string $user_group_name 用户组名称
   * @property string $user_group_description 用户组描述
   * @property string $user_group_icon 用户组图标
   * @property string $user_group_icon_show 用户组图标展示
   * @property string $user_group_color 用户组颜色
   * @property int $user_group_user_count 用户组用户数量
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $delete_time 删除时间
   * @property int $is_admin 是否管理员
   * @property int $ability_normal_login 普通用户登录权限
   * @property int $ability_admin_login 登录权限
   * @property int $ability_admin_manage_user_group 管理用户组权限
   * @property int $ability_admin_manage_user 管理用户权限
   * @property int $ability_admin_manage_topic 管理话题权限
   * @property int $ability_admin_manage_question 管理问题权限
   * @property int $ability_admin_manage_article 管理文章权限
   * @property int $ability_admin_manage_comment 管理评论权限
   * @property int $ability_admin_manage_answer 管理回答权限
   * @property int $ability_admin_manage_reply 管理回复权限
   * @property int $ability_admin_manage_report 管理举报权限
   * @property int $ability_admin_manage_option 管理选项权限
   * @property int $ability_create_article 创建文章权限
   * @property int $ability_create_question 创建问题权限
   * @property int $ability_create_answer 创建回答权限
   * @property int $ability_create_comment 创建评论权限
   * @property int $ability_create_reply 创建回复权限
   * @property int $ability_create_topic 创建话题权限
   * @property int $ability_edit_own_article 编辑自己的文章权限
   * @property int $ability_edit_own_question 编辑自己的问题权限
   * @property int $ability_edit_own_answer 编辑自己的回答权限
   * @property int $ability_edit_own_comment 编辑自己的评论权限
   * @property int $ability_edit_own_reply 编辑自己的回复权限
   * @property int $ability_edit_own_topic 编辑自己的话题权限
   * @property int $ability_delete_own_article 删除自己的文章权限
   * @property int $ability_delete_own_question 删除自己的问题权限
   * @property int $ability_delete_own_answer 删除自己的回答权限
   * @property int $ability_delete_own_comment 删除自己的评论权限
   * @property int $ability_delete_own_reply 删除自己的回复权限
   * @property int $ability_delete_own_topic 删除自己的话题权限
   * @property int $time_before_edit_article 编辑文章前等待时间
   * @property int $time_before_edit_question 编辑问题前等待时间
   * @property int $time_before_edit_answer 编辑回答前等待时间
   * @property int $time_before_edit_comment 编辑评论前等待时间
   * @property int $time_before_edit_reply 编辑回复前等待时间
   * @property int $time_before_edit_topic 编辑话题前等待时间
   * @property int $time_before_delete_article 删除文章前等待时间
   * @property int $time_before_delete_question 删除问题前等待时间
   * @property int $time_before_delete_answer 删除回答前等待时间
   * @property int $time_before_delete_comment 删除评论前等待时间
   * @property int $time_before_delete_reply 删除回复前等待时间
   * @property int $time_before_delete_topic 删除话题前等待时间
   * @property int $ability_edit_article_only_no_comment 编辑文章只有没有评论权限
   * @property int $ability_edit_question_only_no_answer 编辑问题只有没有回答权限
   * @property int $ability_edit_answer_only_no_comment 编辑回答只有没有评论权限
   * @property int $ability_edit_question_only_no_comment 编辑问题只有没有评论权限
   * @property int $ability_edit_comment_only_no_reply 编辑评论只有没有回复权限
   * @property int $ability_edit_topic_only_no_article_or_question 编辑话题只有没有文章或问题权限
   * @property int $ability_delete_article_only_no_comment 删除文章只有没有评论权限
   * @property int $ability_delete_question_only_no_answer 删除问题只有没有回答权限
   * @property int $ability_delete_answer_only_no_comment 删除回答只有没有评论权限
   * @property int $ability_delete_question_only_no_comment 删除问题只有没有评论权限
   * @property int $ability_delete_comment_only_no_reply 删除评论只有没有回复权限
   * @property int $ability_delete_topic_only_no_article_or_question 删除话题只有没有文章或问题权限
   * @property int $ability_edit_own_info 编辑自己的信息权限
   * @property int $ability_vote 投票权限
   */
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
    'user_group_id',
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
