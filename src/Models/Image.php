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

class Image extends Eloquent
{
  protected $table = 'image';
  protected $primaryKey = 'key';
  public $timestamps = false;
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
