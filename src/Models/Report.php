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

class Report extends Eloquent
{
  protected $table = 'report';
  public $timestamps = false;
  protected $primaryKey = 'report_id';
  /**
   * @typedef ReportModel 举报
   * @property int $report_id 举报ID
   * @property int $reportable_id 举报对象ID
   * @property string $reportable_type 举报对象类型
   * @property int $user_id 用户ID
   * @property string $reason 举报理由
   * @property int $report_handle_state 举报处理状态
   * @property int $create_time 创建时间
   */
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
