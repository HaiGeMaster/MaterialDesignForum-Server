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

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\TopicAble as TopicAbleModel;

use MaterialDesignForum\Plugins\Share;

class TopicAble extends TopicAbleModel
{
  /**
   * 添加话题关联
   * @param int $topic_id 话题ID
   * @param int $topicable_id 关联ID
   * @param string $topicable_type 关联类型 article,question
   * @return bool 是否添加成功
   */
  public static function AddTopicAble($topic_id, $topicable_id, $topicable_type): bool
  {
    $topicable = new self();
    $topicable->topic_id = $topic_id;
    $topicable->topicable_id = $topicable_id;
    $topicable->topicable_type = $topicable_type;
    $topicable->create_time = Share::ServerTime();
    return $topicable->save();
  }
  /**
   * 删除话题关联
   * @param int $topic_id 话题ID
   * @param int $topicable_id 关联ID
   * @param string $topicable_type 关联类型 article,question
   * @return bool 是否删除成功
   */
  public static function DeleteTopicAble($topic_id, $topicable_id, $topicable_type): bool
  {
    // $topicable = self::where('topic_id', '=', $topic_id)
    //   ->where('topicable_id', '=', $topicable_id)
    //   ->where('topicable_type', '=', $topicable_type)
    //   ->first();
    // if ($topicable != null) {
    //   return $topicable->delete();
    // } else {
    //   return false;
    // }

    //没有id列的表直接返回删除结果
    $result = self::where('topic_id', '=', $topic_id)
      ->where('topicable_id', '=', $topicable_id)
      ->where('topicable_type', '=', $topicable_type)
      ->delete();
    //如果删除成功，返回true，否则返回false
    if ($result > 0) {
      return true;
    } else {
      return false;
    }
  }
  /**
   * 获取话题关联
   * @param int $topicable_id 关联对象ID
   * @param string $topicable_type 关联类型 article,question
   * @return TopicAbleModel 返回topicable_id 对应的话题 id数组
   */
  public static function GetTopicAbles($topicable_id, $topicable_type)
  {
    //获取topicable_id=$topicable_id且topicable_type=$topicable_type的数据，只需要topic_id字段
    $topic_ids = self::where('topicable_id', '=', $topicable_id)
      ->where('topicable_type', '=', $topicable_type)
      ->pluck('topic_id');
    // //循环检查topic_id,如果话题不存在，删除该话题关联
    // foreach ($topic_ids as $topic_id) {
    //   $topic = TopicController::GetTopic($topic_id)['topic'];
    //   if($topic!=null){
    //     if ($topic->delete_time != 0) {
    //       //$topic_ids->forget($topic_id);
    //       //删除$topic_ids中的$topic_id
    //       $topic_ids = $topic_ids->forget($topic_id);
    //     }
    //   }
    // }
    return $topic_ids;
  }
  public static function DeleteTopicAbles($topic)
  {
    $topicables = self::where('topic_id', '=', $topic->topic_id)->get();
    foreach ($topicables as $topicable) {
      $topicable->delete();
    }
    //再次查找，如果没有数据，说明删除成功
    $topicables = self::where('topic_id', '=', $topic->topic_id)->get();
    if ($topicables->count() == 0) {
      return true;
    } else {
      return false;
    }
  }
}
