<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
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
