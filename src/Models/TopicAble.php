<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
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
