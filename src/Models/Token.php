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

class Token extends Eloquent
{
  protected $table = 'token';
  public $timestamps = false;
  /**
   * @typedef TokenModel 令牌
   * @property string $token 令牌
   * @property int $user_id 用户ID
   * @property string $device 设备
   * @property int $create_time 创建时间
   * @property int $update_time 更新时间
   * @property int $expire_time 过期时间
   */
  protected $fillable = [
    'token',
    'user_id',
    'device',
    'create_time',
    'update_time',
    'expire_time'
  ];
}
