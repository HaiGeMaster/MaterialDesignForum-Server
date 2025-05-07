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
  protected $fillable = [
    'follow_id', // 这个字段不需要
    'user_id',
    'followable_type',
    'followable_id',
    'create_time'
  ];
}
