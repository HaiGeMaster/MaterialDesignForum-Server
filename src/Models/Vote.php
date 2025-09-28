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

class Vote extends Eloquent
{
  protected $table = 'vote';
  public $timestamps = false;
  /**
   * @typedef VoteModel 投票
   * @property int $vote_id 投票ID
   * @property int $user_id 用户ID
   * @property int $votable_id 投票对象ID
   * @property string $votable_type 投票对象类型
   * @property int $type 投票类型
   * @property int $create_time 创建时间
   */
  protected $fillable = [
    'user_id',
    'votable_id',
    'votable_type',
    'type',
    'create_time',
  ];
}
