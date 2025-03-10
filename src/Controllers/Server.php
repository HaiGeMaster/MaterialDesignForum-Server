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

use MaterialDesignForum\Plugins\Share;

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
      'user_count' => UserModel::where('disable_time','=', 0)->count(),
      'user_group_count' => UserGroupModel::where('delete_time','=', 0)->count(),
      'report_count' => ReportModel::count(),
      'topic_count' => TopicModel::where('delete_time','=', 0)->count(),
      'question_count' => QuestionModel::where('delete_time','=', 0)->count(),
      'answer_count' => AnswerModel::where('delete_time','=', 0)->count(),
      'article_count' => ArticleModel::where('delete_time','=', 0)->count(),
      'comment_count' => CommentModel::where('delete_time','=', 0)->count(),
      'reply_count' => ReplyModel::where('delete_time','=', 0)->count(),
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
}
