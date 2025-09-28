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

class Image extends Eloquent
{
  protected $table = 'image';
  protected $primaryKey = 'key';
  public $timestamps = false;
  /**
   * @typedef ImageModel 图片
   * @property string $key 图片ID
   * @property string $filename 文件名
   * @property int $width 宽度
   * @property int $height 高度
   * @property int $create_time 创建时间
   * @property string $item_type 项目类型
   * @property int $item_id 项目ID
   * @property int $user_id 用户ID
   */
  protected $fillable = [
    'key', // 这个字段不需要
    'filename',
    'width',
    'height',
    'create_time',
    'item_type',
    'item_id',
    'user_id'
  ];
}
