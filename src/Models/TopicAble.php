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

class TopicAble extends Eloquent
{
  protected $table = 'topicable';
  public $timestamps = false;
  /**
   * @typedef TopicAbleModel 话题关联
   * @property int $topic_id 话题ID
   * @property int $topicable_id 话题关联ID
   * @property string $topicable_type 话题关联类型
   * @property int $create_time 创建时间
   */
  protected $fillable = [
    'topic_id',
    'topicable_id',
    'topicable_type',
    'create_time',
  ];
}
