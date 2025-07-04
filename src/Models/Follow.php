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

class Follow extends Eloquent
{
  protected $table = 'follow';
  public $timestamps = false;
  protected $primaryKey = 'follow_id';
  /**
   * @typedef FollowModel 关注
   * @property int $follow_id 关注ID
   * @property int $user_id 用户ID
   * @property string $followable_type 关注关联类型
   * @property int $followable_id 关注关联ID
   * @property int $create_time 创建时间
   * @property int $delete_time 删除时间
   */
  protected $fillable = [
    'follow_id', // 这个字段不需要
    'user_id',
    'followable_type',
    'followable_id',
    'create_time'
  ];
}
