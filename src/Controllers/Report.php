<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Report as ReportModel;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Plugins\Share;

use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Config\Config;

class Report extends ReportModel
{
  /**
   * 添加举报
   * @param int $reportable_id 举报目标ID
   * @param string $reportable_type 举报目标类型
   * @param int $user_token 用户ID
   * @param string $reason 举报原因
   * @return array is_add:是否添加 report:举报
   */
  public static function AddReport($reportable_id, $reportable_type, $user_token, $reason)
  {
    $report = new self();
    $report->reportable_id = $reportable_id;
    $report->reportable_type = $reportable_type;
    $report->user_id = TokenController::GetUserId($user_token);
    $report->reason = $reason;
    $report->create_time = Share::ServerTime();
    $report->report_handle_state = 0;
    return [
      'is_add' => $report->save(),
      'report' => self::GetReport($report->id)
    ];
  }
  /**
   * 处理举报
   * @param int $report_id 举报ID
   * @param int $report_handle_state 举报处理状态
   * @return bool
   */
  public static function HandleReport($report_id, $report_handle_state): bool
  {
    $report = self::find($report_id);
    $report->report_handle_state = $report_handle_state;
    return $report->save();
  }
  /**
   * 获取举报
   * @param int $report_id 举报ID
   * @return mixed
   */
  public static function GetReport($report_id)
  {
    return self::find($report_id);
  }
  /**
   * 获取举报列表
   * @param string $order 排序
   * @param int $page 页数
   * @param int $user_token 用户token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @return array is_get:是否获取 data:举报列表
   */
  public static function GetReports(
    $order,
    $page,
    $user_token,
    $per_page = 20,
    $search_keywords = '',
    $search_field = []
  ) {
    if($search_field == []){
      $search_field = self::$search_field;
    }
    if (!UserGroupController::IsAdmin($user_token)) {
      return Share::HandleDataAndPagination(null);
    }
    // where('report_handle_state', '=', 0)
    $reports = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    if ($search_keywords != '') {
      // $reports = self::where($search_field, 'like', '%' . $search_keywords . '%')
      //   ->orderBy($field, $sort)
      //   ->paginate($per_page, ['*'], 'page', $page);
      $reports = self::where(function ($query) use ($search_field, $search_keywords) {
        foreach ($search_field as $key => $value) {
          $query->orWhere($value, 'like', '%' . $search_keywords . '%');
        }
      })
        ->orderBy($field, $sort)
        ->paginate($per_page, ['*'], 'page', $page);
    } else {
      $reports = self::orderBy($field, $sort)
        ->paginate($per_page, ['*'], 'page', $page);
    }
    foreach ($reports as $report) {
      $report->reportable = $report->reportable;
      $report->user = UserController::GetUserInfo($report->user_id)['user'];
    }
    return Share::HandleDataAndPagination($reports);
  }
}
