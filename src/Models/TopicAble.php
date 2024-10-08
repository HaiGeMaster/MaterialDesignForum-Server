<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class TopicAble extends Eloquent
{
  protected $table = 'topicable';
  public $timestamps = false;
  protected $fillable = [
    'topic_id',
    'topicable_id',
    'topicable_type',
    'create_time',
  ];
}
