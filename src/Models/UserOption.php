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
