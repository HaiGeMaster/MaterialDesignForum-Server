<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/05/20-15:53:29
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserOption extends Eloquent
{
  protected $table = 'user_option';
  public $timestamps = false;
  protected $primaryKey = 'user_option_id';
  protected $fillable = [
    'user_option_id', // 这个字段不需要
    'user_id',
    'name',
    'value',
  ];
}
