<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Vote extends Eloquent
{
  protected $table = 'vote';
  public $timestamps = false;
  protected $fillable = [
    'user_id',
    'votable_id',
    'votable_type',
    'type',
    'create_time',
  ];
}
