<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Report extends Eloquent
{
  protected $table = 'report';
  public $timestamps = false;
  protected $primaryKey = 'report_id';
  protected $fillable = [
    'report_id',
    'reportable_id',
    'reportable_type',
    'user_id',
    'reason',
    'report_handle_state',
    'create_time'
  ];
  // 搜索字段
  public static $search_field = ['reason'];
}
