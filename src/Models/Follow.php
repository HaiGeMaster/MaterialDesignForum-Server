<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
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
