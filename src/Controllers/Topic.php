<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Topic as TopicModel;
use MaterialDesignForum\Controllers\Image as ImageController;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\Follow as FollowController;
use MaterialDesignForum\Controllers\TopicAble as TopicAbleController;
use MaterialDesignForum\Plugins\Share;
//use MaterialDesignForum\Config\Config;

class Topic extends TopicModel
{
  /**
   * 获取关联的话题
   */
  public static function GetAblesTopic($topicable_id, $topicable_type)
  {
    $topic_ids = TopicAbleController::GetTopicAbles($topicable_id, $topicable_type);
    if ($topic_ids != null) {
      // $topics = self::whereIn('topic_id', $topic_ids)
      //   ->where('delete_time', '=', 0)//只获取删除时间为0的
      //   ->get();
      //获取id列表到topic_ids
      $topic_ids = self::whereIn('topic_id', $topic_ids)
        ->where('delete_time', '=', 0)//只获取删除时间为0的
        ->pluck('topic_id')
        ->toArray();
      $topics = [];
      foreach ($topic_ids as $key => $value) {
        $topics[$key] = self::GetTopic($value)['topic'];
      }
      return $topics;
    }
  }
  /**
   * 增加话题
   * @param int $user_token 用户token
   * @param int $topic_id 话题ID
   * @return bool 是否成功
   */
  public static function AddTopic($name, $description, $cover, $user_token)
  {
    $is_valid_content =
      $name != null &&
      $description != null &&
      $cover != null &&
      $user_token != '' &&
      $name != '' &&
      $description != '' &&
      $cover != '' &&
      $user_token != '';
    $is_add = false;
    $topic_id = null;
    if (
      $is_valid_content && (
        UserGroupController::Ability($user_token, 'ability_create_topic') ||
        UserGroupController::IsAdmin($user_token)
      )
    ) {
      $topic = new self();
      $topic->user_id = TokenController::GetUserId($user_token);
      $topic->name = $name;
      $topic->cover = ImageController::SaveUploadImage('topic_cover', $cover, Share::ServerTime());
      $topic->description = $description;
      $topic->article_count = 0;
      $topic->question_count = 0;
      $topic->follower_count = 0;
      $topic->create_time = Share::ServerTime();
      $topic->update_time = Share::ServerTime();
      $is_add = $topic->save();
      if ($is_add) {
        $topic_id = $topic->topic_id;
        UserController::AddTopicCount($topic->user_id);
      }
    }
    return [
      'is_add' => $is_add,
      'topic' => self::GetTopic($topic_id, $user_token)['topic'],
      // 'topic_id' => $topic_id
    ];
  }
  /**
   * 获取话题
   * @param int $topic_id 话题ID
   * @param int $user_token 用户token
   * @return array is_get:是否获取 topic:话题 json
   */
  public static function GetTopic($topic_id, $user_token = '')
  {
    $topic = self::where('topic_id', '=', $topic_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($topic) {
      // $topic['is_follow'] = false;
      // $topic['user'] = User::GetUserInfo($topic['user_id'], $user_token)['user'];
      $topic->is_follow = false;
      $topic->user = User::GetUserInfo($topic['user_id'], $user_token)['user'];
      if ($user_token != '') {
        //$topic['is_follow'] = FollowController::IsFollow($user_token, 'topic', $topic['topic_id'], true);
        $topic->is_follow = FollowController::IsFollow($user_token, 'topic', $topic->topic_id, true);
      }
      return [
        'is_get' => true,
        'topic' => $topic,
      ];
    } else {
      return [
        'is_get' => false,
        'topic' => null,
      ];
    }
  }
  /**
   * 获取话题列表
   * @param string $order 排序
   * @param int $page 页数
   * @param bool $following 是否获取关注的话题
   * @param int $user_token 用户token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @return array is_get:是否获取 data:话题列表
   */
  public static function GetTopics(
    $order,
    $page,
    $following = false,
    $user_token = '',
    $per_page = 20,
    $search_keywords = '',
    $search_field = []
  ) {
    if($search_field == []){
      $search_field = self::$search_field;
    }

    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    if ($following == 'false' || $following == false) {
      if ($search_keywords != '') {
        $data = self::where('delete_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('delete_time', '=', 0)->orderBy($field, $sort)->paginate($per_page, ['*'], 'page', $page);
      }
      $data = Share::HandleDataAndPagination($data);
    } else if ($following == 'true' || $following == true && $user_token != '') {
      $user_id = TokenController::GetUserId($user_token);
      $following_id_object = FollowController::where('user_id', '=', $user_id)->where('followable_type', '=', 'topic')->paginate($per_page, ['*'], 'page', $page);
      $following_id_array = [];
      foreach ($following_id_object->items() as $key => $value) {
        array_push($following_id_array, $value->followable_id);
      }
      if ($search_keywords != '') {
        $data = self::where('delete_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->whereIn(
            'topic_id',
            $following_id_array
          )->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page)
          ->items();
      } else {
        $data = self::where('delete_time', '=', 0)->whereIn(
          'topic_id',
          $following_id_array
        )->orderBy($field, $sort)->paginate($per_page, ['*'], 'page', $page)->items();
      }
      $data = Share::HandleMergeDataAndPagination($data, $following_id_object);
    }
    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]->is_follow = FollowController::IsFollow($user_token, 'topic', $value->topic_id, true);
        $data['data'][$key]->user = User::GetUserInfo($value->user_id, $user_token)['user'];
      }
    }
    return $data;
  }
  /**
   * 编辑话题
   * @param int $topic_id 话题ID
   * @param string $name 话题名称
   * @param string $description 话题描述
   * @param string $cover 话题封面
   * @param int $user_token 用户token
   * @return array is_edit:是否编辑
   */
  public static function EditTopic($topic_id, $name, $description, $cover, $user_token)
  {
    $is_valid_content =
      $topic_id != null &&
      $name != null &&
      $description != null &&
      //$cover != null &&
      $user_token != '' &&
      $topic_id != '' &&
      $name != '' &&
      $description != '' &&
      //$cover != '' &&
      $user_token != '';
    $is_edit = false;
    $user_id = TokenController::GetUserId($user_token);
    $topic = self::where('topic_id', '=', $topic_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($topic != null && $is_valid_content && $user_id != null) {
      if (
        (
          TokenController::IsUserSelf($user_token, $topic->user_id) &&
          UserGroupController::Ability($user_token, 'ability_edit_own_topic') &&
          (
            UserGroupController::Ability($user_token, 'ability_edit_topic_only_no_article_or_question') ? ($topic->article_count == 0 && $topic->question_count == 0 ? true : false) : true
          ) &&
          UserGroupController::BeforeTime($user_token, 'time_before_edit_topic', $topic->create_time)
        )
        ||
        UserGroupController::IsAdmin($user_token)
      ) {
        $topic->name = $name;
        if ($cover != '') {
          //先将$topic->cover删除
          if (ImageController::DeleteUploadImage($topic->cover)) {
            $topic->cover = ImageController::SaveUploadImage('topic_cover', $cover, Share::ServerTime());
          }
        }
        $topic->description = $description;
        $topic->update_time = Share::ServerTime();
        $is_edit = $topic->save();
      }
    }
    return [
      'is_edit' => $is_edit,
      'topic' => self::GetTopic($topic_id, $user_token)['topic']
    ];
  }
  /**
   * 删除话题
   * @param array $topic_ids 话题ID数组
   * @param int $user_token 用户token
   * @return array is_delete:是否删除
   */
  public static function DeleteTopics($topic_ids, $user_token)
  {
    //return self::whereIn('topic_id', $topic_ids)->get();//$topic_ids;
    $is_valid_content =
      $topic_ids != null &&
      $user_token != '' &&
      $topic_ids != '' &&
      $user_token != '';
    $is_delete = false;
    $user_id = TokenController::GetUserId($user_token);
    $delete_ids = [];
    $topics=[];
    if (
      $user_id != null &&
      $is_valid_content
    ) {
      $topics = self::whereIn('topic_id', $topic_ids)->get();
      foreach ($topics as $key => $topic) {
        if (
          (
            TokenController::IsUserSelf($user_token, $topic->user_id) &&
            UserGroupController::Ability($user_token, 'ability_delete_own_question') &&
            (
              UserGroupController::Ability($user_token, 'ability_delete_topic_only_no_article_or_question') ? ($topic->article_count == 0 && $topic->question_count == 0 ? true : false) : true
            ) &&
            UserGroupController::BeforeTime($user_token, 'time_before_delete_topic', $topic->create_time)
          )
          ||
          UserGroupController::IsAdmin($user_token)
        ) {
          //删除话题关系
          //TopicAbleController::DeleteTopicAbles($value);
          //减少用户话题数
          $topic->delete_time = Share::ServerTime();
          UserController::SubTopicCount($topic->user_id);
          $is_delete = $topic->save();
          array_push($delete_ids, $topic->topic_id);
        }
      }
    }
    return [
      'is_delete' => $is_delete,
      'delete_ids' => $delete_ids,
      'data' => $topics
    ];
  }
}
