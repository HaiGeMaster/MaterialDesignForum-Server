<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\User as UserModel;
use MaterialDesignForum\Models\UserGroup as UserGroupModel;
use MaterialDesignForum\Models\Report as ReportModel;
use MaterialDesignForum\Models\Topic as TopicModel;
use MaterialDesignForum\Models\Question as QuestionModel;
use MaterialDesignForum\Models\Answer as AnswerModel;
use MaterialDesignForum\Models\Article as ArticleModel;
use MaterialDesignForum\Models\Comment as CommentModel;
use MaterialDesignForum\Models\Reply as ReplyModel;

use MaterialDesignForum\Controllers\UserGroup as UserGroupController;

use MaterialDesignForum\Config\Config;

use MaterialDesignForum\Plugins\Share;

// use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as DB;


class Server
{
  public static function GetDataCount($user_token)
  {
    if (
      // !UserGroupController::IsAdmin($user_token)||
      !UserGroupController::IsAdminLogin($user_token)
    ) {
      return [
        'is_get' => false,
        'data' => null,
      ];
    }
    // $data_count = [
    //   'user_count' => UserModel::count(),
    //   'user_group_count' => UserGroupModel::count(),
    //   'report_count' => ReportModel::count(),
    //   'topic_count' => TopicModel::count(),
    //   'question_count' => QuestionModel::count(),
    //   'answer_count' => AnswerModel::count(),
    //   'article_count' => ArticleModel::count(),
    //   'comment_count' => CommentModel::count(),
    //   'reply_count' => ReplyModel::count(),
    // ];

    //仅获取未被删除或用户未被禁用的数据
    $data_count = [
      'user_count' => UserModel::where('disable_time', '=', 0)->count(),
      'user_group_count' => UserGroupModel::where('delete_time', '=', 0)->count(),
      'report_count' => ReportModel::count(),
      'topic_count' => TopicModel::where('delete_time', '=', 0)->count(),
      'question_count' => QuestionModel::where('delete_time', '=', 0)->count(),
      'answer_count' => AnswerModel::where('delete_time', '=', 0)->count(),
      'article_count' => ArticleModel::where('delete_time', '=', 0)->count(),
      'comment_count' => CommentModel::where('delete_time', '=', 0)->count(),
      'reply_count' => ReplyModel::where('delete_time', '=', 0)->count(),
    ];
    return [
      'is_get' => true,
      'data' => $data_count,
    ];
  }
  /**
   * 获取指定时间段内的统计数据
   * @param $user_token string
   * @param $time_type string last_7_days, this_month, last_month, last_30_days, this_year, last_year, last_1_year
   * @param $model_type string user, user_group, report, topic, question, answer, article, comment, reply
   * @param $startTimestamp int 开始时间戳
   * @param $endTimestamp int 结束时间戳
   * @return array|null
   */
  public static function GetDataBetweenTimestamps($user_token, $time_type, $model_type/*, $startTimestamp, $endTimestamp*/)
  {
    // items: ['最近 7 天', '本月', '上月', '最近 30 天', '今年', '去年', '最近 1 年'],
    if (
      // !UserGroupController::IsAdmin($user_token)||
      !UserGroupController::IsAdminLogin($user_token)
    ) {
      return [
        'is_get' => false,
        'data' => null,
      ];
    }
    $startTimestamp = null;
    $endTimestamp = null;
    // 获取时间段 从当前时间开始。分别有：last_7_days, this_month, last_month, last_30_days, this_year, last_year, last_1_year
    if ($time_type == 'last_7_days') {
      $startTimestamp = strtotime('-7 days');
      $endTimestamp = Share::ServerTime();
    } else if ($time_type == 'this_month') {
      $startTimestamp = strtotime(date('Y-m-01'));
      $endTimestamp = Share::ServerTime();
    } else if ($time_type == 'last_month') {
      $startTimestamp = strtotime(date('Y-m-01', strtotime('-1 month')));
      $endTimestamp = strtotime(date('Y-m-01')) - 1;
    } else if ($time_type == 'last_30_days') {
      $startTimestamp = strtotime('-30 days');
      $endTimestamp = Share::ServerTime();
    } else if ($time_type == 'this_year') {
      $startTimestamp = strtotime(date('Y-01-01'));
      $endTimestamp = Share::ServerTime();
    } else if ($time_type == 'last_year') {
      $startTimestamp = strtotime(date('Y-01-01', strtotime('-1 year')));
      $endTimestamp = strtotime(date('Y-01-01')) - 1;
    } else if ($time_type == 'last_1_year') {
      $startTimestamp = strtotime('-1 year');
      $endTimestamp = Share::ServerTime();
    }//如果time_type是其他则将视为年份文本,startTimestamp为年份的1月1日，endTimestamp为年份的12月31日23:59:59
    else if (is_numeric($time_type)) {
      $startTimestamp = strtotime($time_type . '-01-01');
      $endTimestamp = strtotime($time_type . '-12-31 23:59:59');
    }

    $model = null;

    $data = null;

    switch ($model_type) {//user、user_group、report、topic、question、answer、article、comment、reply
      case 'user':
        $model = UserModel::class;
        break;
      case 'user_group':
        $model = UserGroupModel::class;
        break;
      case 'report':
        $model = ReportModel::class;
        break;
      case 'topic':
        $model = TopicModel::class;
        break;
      case 'question':
        $model = QuestionModel::class;
        break;
      case 'answer':
        $model = AnswerModel::class;
        break;
      case 'article':
        $model = ArticleModel::class;
        break;
      case 'comment':
        $model = CommentModel::class;
        break;
      case 'reply':
        $model = ReplyModel::class;
        break;
    }


    if ($model) {

      $formattedData = [];
      // 如果$time_type包含year，最多只会有12条月份数据
      if (strpos($time_type, 'year') !== false) {

        for ($currentDate = $startTimestamp; $currentDate < $endTimestamp; $currentDate = strtotime('+1 month', $currentDate)) {
          // Calculate the start and end of the current month
          $startOfMonth = $currentDate;
          $endOfMonth = strtotime('last day of this month', $startOfMonth) + 86399; // Add 86399 seconds to get the last second of the month

          // Query for the count of entries within this month
          $count = $model::whereBetween('create_time', [$startOfMonth, $endOfMonth])->count();

          // Format and add the result to the array
          $formattedData[] = [
            'date' => date('Y-m', $startOfMonth),
            //date要求转为时间戳
            // 'date' => $startOfMonth,
            'count' => $count,
          ];
        }
      } // 如果$time_type包含this和last，最多只会有30条天数数据
      else if(strpos($time_type, 'this') !== false || strpos($time_type, 'last') !== false) {
        for ($currentDate = $startTimestamp; $currentDate < $endTimestamp; $currentDate += 86400) {
          // 计算当前天的结束时间戳（当前日期的最后一秒）
          $nextDate = $currentDate + 86399;

          // 查询当前天的数量
          $count = $model::whereBetween('create_time', [$currentDate, $nextDate])->count();

          // 格式化为期望的日期格式并添加到结果数组中
          $formattedData[] = [
            'date' => date('Y-m-d', $currentDate),
            //date要求转为时间戳
            // 'date' => $currentDate,
            'count' => $count,
          ];
        }
      }//剩余的都视为年份数字，比如2023，则取2023年1月到2023年12月的数据，共12条
      else{
        for ($currentDate = $startTimestamp; $currentDate < $endTimestamp; $currentDate = strtotime('+1 month', $currentDate)) {
          // Calculate the start and end of the current month
          $startOfMonth = $currentDate;
          $endOfMonth = strtotime('last day of this month', $startOfMonth) + 86399; // Add 86399 seconds to get the last second of the month

          // Query for the count of entries within this month
          $count = $model::whereBetween('create_time', [$startOfMonth, $endOfMonth])->count();

          // Format and add the result to the array
          $formattedData[] = [
            'date' => date('Y-m', $startOfMonth),
            //date要求转为时间戳
            // 'date' => $startOfMonth,
            'count' => $count,
          ];
        }
      }

      $data = $formattedData;
    }

    return [
      'is_get' => true,
      'data' => $data,
      'data_count' => count($data),
    ];
  }
  /**
   * 获取所有统计数据
   * @param $user_token string
   * @param $time_type string last_7_days, this_month, last_month, last_30_days, this_year, last_year, last_1_year
   * @return array|null
   */
  public static function GetDataBetweenTimestampsAll($user_token, $time_type)
  {
    if (
      // !UserGroupController::IsAdmin($user_token)||
      !UserGroupController::IsAdminLogin($user_token)
    ) {
      return [
        'is_get' => false,
        'data' => null,
      ];
    }
    $data = [
      'user' => self::GetDataBetweenTimestamps($user_token, $time_type, 'user')['data'],
      'user_group' => self::GetDataBetweenTimestamps($user_token, $time_type, 'user_group')['data'],
      'report' => self::GetDataBetweenTimestamps($user_token, $time_type, 'report')['data'],
      'topic' => self::GetDataBetweenTimestamps($user_token, $time_type, 'topic')['data'],
      'question' => self::GetDataBetweenTimestamps($user_token, $time_type, 'question')['data'],
      'answer' => self::GetDataBetweenTimestamps($user_token, $time_type, 'answer')['data'],
      'article' => self::GetDataBetweenTimestamps($user_token, $time_type, 'article')['data'],
      'comment' => self::GetDataBetweenTimestamps($user_token, $time_type, 'comment')['data'],
      'reply' => self::GetDataBetweenTimestamps($user_token, $time_type, 'reply')['data'],
    ];
    return [
      'is_get' => true,
      'data' => $data,
    ];
  }
  /**
   * 获取服务器信息
   * @param $user_token string
   * @return array|null
   */
  // public static function GetServerInfo($user_token)
  // {
  //   $data = null;
  //   try {
  //     // 获取操作系统
  //     $os = php_uname();
  //     // 获取PHP版本
  //     $php_version = phpversion();
  //     // 获取Web服务器软件
  //     $web_server = $_SERVER['SERVER_SOFTWARE'];
  //     // 获取数据库版本 (假设使用MySQL)
  //     $db_version = DB::select('SELECT VERSION() AS version');
  //     $db_version = $db_version[0]->version ?? null;
  //     // 获取上传文件限制
  //     $upload_max_filesize = ini_get('upload_max_filesize');
  //     // 获取PHP执行时长限制
  //     $max_execution_time = ini_get('max_execution_time') . 's';
  //     // 获取剩余硬盘空间
  //     $disk_free_space = disk_free_space("/"); // 获取根目录的剩余空间
  //     // 获取数据库大小（假设使用MySQL）
  //     $db_size = null;
  //     $database_name = Config::GetMySqlDatabase(); // 获取数据库名
  //     if ($database_name) {
  //       $query = DB::select(
  //         "SELECT table_schema AS db_name, 
  //                   SUM(data_length + index_length) AS db_size 
  //                   FROM information_schema.tables 
  //                   WHERE table_schema = :db_name 
  //                   GROUP BY table_schema",
  //         ['db_name' => $database_name]
  //       );
  //       if (!empty($query)) {
  //         $db_size = $query[0]->db_size;
  //       }
  //     }
  //     // 将字节转换为T、G、M、KB
  //     function formatSize($bytes)
  //     {
  //       if ($bytes >= 1099511627776) { // 1 TB = 1,099,511,627,776 bytes
  //         return number_format($bytes / 1099511627776, 2) . ' TB';
  //       } elseif ($bytes >= 1073741824) { // 1 GB = 1,073,741,824 bytes
  //         return number_format($bytes / 1073741824, 2) . ' GB';
  //       } elseif ($bytes >= 1048576) { // 1 MB = 1,048,576 bytes
  //         return number_format($bytes / 1048576, 2) . ' MB';
  //       } elseif ($bytes >= 1024) { // 1 KB = 1,024 bytes
  //         return number_format($bytes / 1024, 2) . ' KB';
  //       } else {
  //         return $bytes . ' B'; // 小于1KB时显示为字节
  //       }
  //     }
  //     // 格式化剩余硬盘空间和数据库大小
  //     $disk_free_space = formatSize($disk_free_space);
  //     $db_size = $db_size ? formatSize($db_size) : null;

  //     $data = [
  //       // 'MDF_VERSION' => '1.0.0',
  //       'OS' => $os,
  //       'PHP_VERSION' => $php_version,
  //       'WEB_SERVER' => $web_server,
  //       'DB_VERSION' => $db_version,
  //       'UPLOAD_MAX_FILESIZE' => $upload_max_filesize,
  //       'MAX_EXECUTION_TIME' => $max_execution_time,
  //       'DISK_FREE_SPACE' => $disk_free_space,
  //       'DB_SIZE' => $db_size
  //     ];
  //     // 返回服务器信息
  //     return [
  //       'is_get' => true,
  //       'data' => $data
  //     ];
  //   } catch (\Exception $e) {
  //     return [
  //       'is_get' => true,
  //       'data' => $data,
  //     ];
  //   }
  // }
  public static function GetServerInfo($user_token)
  {

    if (
      // !UserGroupController::IsAdmin($user_token)||
      !UserGroupController::IsAdminLogin($user_token)
    ) {
      return [
        'is_get' => false,
        'data' => null,
      ];
    }

    // 获取操作系统
    $os = php_uname();

    // 获取PHP版本
    $php_version = phpversion();

    // 获取Web服务器软件
    $web_server = $_SERVER['SERVER_SOFTWARE'];

    // 获取数据库版本 (假设使用MySQL)
    try {
      $db_version = DB::select('SELECT VERSION() AS version');
      $db_version = !empty($db_version) ? $db_version[0]->version : null;
    } catch (\Exception $e) {
      $db_version = null;
    }

    // 获取上传文件限制
    $upload_max_filesize = ini_get('upload_max_filesize');

    // 获取PHP执行时长限制
    $max_execution_time = ini_get('max_execution_time') . 's';

    //关闭所有错误报告
    error_reporting(0);

    // 获取剩余硬盘空间
    $disk_free_space = disk_free_space("/"); // 获取根目录的剩余空间
    if ($disk_free_space === false) {
      $disk_free_space = null; // 如果获取不到，设置为null
    }

    // 恢复错误报告
    error_reporting(E_ALL);

    // 获取数据库大小（假设使用MySQL）
    $db_size = null;
    $database_name = Config::GetMySqlDatabase(); // 获取数据库名
    if ($database_name) {
      try {
        $query = DB::select(
          "SELECT table_schema AS db_name, 
                        SUM(data_length + index_length) AS db_size 
                 FROM information_schema.tables 
                 WHERE table_schema = :db_name 
                 GROUP BY table_schema",
          ['db_name' => $database_name]
        );
        if (!empty($query)) {
          $db_size = $query[0]->db_size;
        }
      } catch (\Exception $e) {
        $db_size = null;
      }
    }

    // 将字节转换为T、G、M、KB
    function formatSize($bytes)
    {
      if ($bytes >= 1099511627776) { // 1 TB = 1,099,511,627,776 bytes
        return number_format($bytes / 1099511627776, 2) . ' TB';
      } elseif ($bytes >= 1073741824) { // 1 GB = 1,073,741,824 bytes
        return number_format($bytes / 1073741824, 2) . ' GB';
      } elseif ($bytes >= 1048576) { // 1 MB = 1,048,576 bytes
        return number_format($bytes / 1048576, 2) . ' MB';
      } elseif ($bytes >= 1024) { // 1 KB = 1,024 bytes
        return number_format($bytes / 1024, 2) . ' KB';
      } else {
        return $bytes . ' B'; // 小于1KB时显示为字节
      }
    }

    // 格式化剩余硬盘空间和数据库大小，磁盘格式为:已用空间/总磁盘空间
    $disk_free_space = $disk_free_space !== null ? formatSize($disk_free_space) . '/' . formatSize(disk_total_space("/")) : '/error';
    $db_size = ($db_size !== null ? formatSize($db_size) : '/error');

    // 返回服务器信息
    return [
      'is_get' => true,
      'data' => [
        'OS' => $os,
        'PHP_VERSION' => $php_version,
        'WEB_SERVER' => $web_server,
        'DB_VERSION' => $db_version,
        'UPLOAD_MAX_FILESIZE' => $upload_max_filesize,
        'MAX_EXECUTION_TIME' => $max_execution_time,
        'DISK_FREE_SPACE' => $disk_free_space,
        'DB_SIZE' => $db_size
      ]
    ];
  }
}
