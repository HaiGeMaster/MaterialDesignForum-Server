<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
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
