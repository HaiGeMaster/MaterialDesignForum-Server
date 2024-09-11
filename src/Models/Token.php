<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Token extends Eloquent
{
  protected $table = 'token';
  public $timestamps = false;
  protected $fillable = [
    'token',
    'user_id',
    'device',
    'create_time',
    'update_time',
    'expire_time'
  ];
}
