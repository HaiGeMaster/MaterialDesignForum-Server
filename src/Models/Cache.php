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

class Cache extends Eloquent
{
  protected $table = 'cache';
  public $timestamps = false;
  /**
   * @typedef CacheModel 缓存
   * @property string $name 缓存名称
   * @property string $value 缓存值
   * @property int $create_time 创建时间
   * @property int $life_time 缓存时间
   */
  protected $fillable = [
    'name',
    'value',
    'create_time',
    'life_time'
  ];
}
