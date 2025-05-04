<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
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
      !UserGroupController::Ability($user_token, 'ability_admin_login')
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
      !UserGroupController::Ability($user_token, 'ability_admin_login')
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
    }

    $model = null;

    $data = null;

    switch ($model_type) {
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

      // for ($currentDate = $startTimestamp; $currentDate <= $endTimestamp; $currentDate += 86400) {
      //   $nextDate = $currentDate + 86400;
      for ($currentDate = $startTimestamp; $currentDate < $endTimestamp; $currentDate += 86400) {
        $nextDate = $currentDate + 86399; // 结束时间戳调整为当前日期的最后一秒

        $count = $model::whereBetween('create_time', [$currentDate, $nextDate])->count();

        $formattedData[] = [
          'date' => date('Y-m-d', $currentDate),
          'count' => $count,
          //'timestampArray' => [$currentDate, $nextDate],
        ];
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
      !UserGroupController::Ability($user_token, 'ability_admin_login')
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
  public static function GetServerInfo($user_token)
  {
    // 获取操作系统
    $os = php_uname();

    // 获取PHP版本
    $php_version = phpversion();

    // 获取Web服务器软件
    $web_server = $_SERVER['SERVER_SOFTWARE'];

    // 获取数据库版本 (假设使用MySQL)
    $db_version = DB::select('SELECT VERSION() AS version');
    $db_version = $db_version[0]->version ?? null;

    // 获取上传文件限制
    $upload_max_filesize = ini_get('upload_max_filesize');

    // 获取PHP执行时长限制
    $max_execution_time = ini_get('max_execution_time').'s';

    // 获取剩余硬盘空间
    $disk_free_space = disk_free_space("/"); // 获取根目录的剩余空间

    // 获取数据库大小（假设使用MySQL）
    $db_size = null;
    $database_name = Config::GetMySqlDatabase(); // 获取数据库名
    if ($database_name) {
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

    // 格式化剩余硬盘空间和数据库大小
    $disk_free_space = formatSize($disk_free_space);
    $db_size = $db_size ? formatSize($db_size) : null;

    // 返回服务器信息
    // return [
    //     'os' => $os,
    //     'php_version' => $php_version,
    //     'web_server' => $web_server,
    //     'db_version' => $db_version,
    //     'upload_max_filesize' => $upload_max_filesize,
    //     'max_execution_time' => $max_execution_time,
    //     'disk_free_space' => $disk_free_space,
    //     'db_size' => $db_size
    // ];
    return [
      'is_get' => true,
      'data' => [
        // 'MDF_VERSION' => '1.0.0',
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
