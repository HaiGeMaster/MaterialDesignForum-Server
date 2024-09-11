<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
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
