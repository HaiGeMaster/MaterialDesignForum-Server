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

class Cache extends Eloquent
{
  protected $table = 'cache';
  public $timestamps = false;
  protected $fillable = [
    'name',
    'value',
    'create_time',
    'life_time'
  ];
}
