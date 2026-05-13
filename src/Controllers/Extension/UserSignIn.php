<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/05/20-15:53:29
 */

namespace MaterialDesignForum\Controllers\Extension;

use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\UserOption as UserOptionController;

use Exception; // 引入异常类

/**
 * 用户签到类
 * 签到产生数据：{签到时间，签到次数，连续签到天数（根据签到时间而定），签到点数}
 * 使用UserOptionController::SetUserOption和使用UserOptionController::GetUserOption来设置与获取签到数据
 */
class UserSignIn
{
  /** 签到配置名称 */
  const SIGN_IN_OPTION_NAME = 'user_sign_in';

  /**
   * 添加用户签到
   * @param string $user_token 用户令牌
   * @return array
   */
  public static function AddUserSignIn(string $user_token): array
  {
    try {
      $user = TokenController::GetUserId($user_token);
      if (!$user) {
        return [
          'is_set' => false,
          'error' => '用户未登录或令牌无效',
        ];
      }

      // 获取当前签到数据
      $signInData = self::GetUserSignInData($user_token)['data'];

      // 检查今日是否已签到
      if (self::IsSignedInToday($signInData)) {
        return [
          'is_set' => false,
          'error' => '今日已签到',
          'data' => $signInData,
        ];
      }

      // 计算连续签到天数
      $consecutiveDays = self::CalculateConsecutiveDays($signInData);

      // 计算签到点数（连续签到天数越多，奖励越多）
      $points = self::CalculatePoints($consecutiveDays);

      //天数和点数最多999999999
      $consecutiveDays = min($consecutiveDays, 999999999);
      $points = min($points, 999999999);

      // 更新签到数据
      $newSignInData = [
        'last_sign_in_time' => date('Y-m-d H:i:s'),
        'last_sign_in_date' => date('Y-m-d'),
        'total_count' => ($signInData['total_count'] ?? 0) + 1,
        'consecutive_days' => $consecutiveDays,
        'total_points' => ($signInData['total_points'] ?? 0) + $points,
      ];

      $result = UserOptionController::SetUserOption($user_token, self::SIGN_IN_OPTION_NAME, $newSignInData);

      if ($result['is_set']) {
        return [
          'is_set' => true,
          'data' => $newSignInData,
          // 'points' => $points,
          // 'message' => '签到成功',
        ];
      }

      return [
        'is_set' => false,
        'error' => $result['error'] ?? '签到失败',
      ];
    } catch (Exception $e) {
      return [
        'is_set' => false,
        'error' => '签到异常: ' . $e->getMessage(),
      ];
    }
  }

  /**
   * 获取用户签到数据
   * @param string $user_token 用户令牌
   * @return array
   */
  public static function GetUserSignInData(string $user_token): array
  {
    $result = UserOptionController::GetUserOption($user_token, self::SIGN_IN_OPTION_NAME);

    if ($result['is_get'] && isset($result['data'])) {
      return [
        'is_get' => true,
        'data' => [
          'last_sign_in_time' => $result['data']['value']['last_sign_in_time'] ?? null,
          'last_sign_in_date' => $result['data']['value']['last_sign_in_date'] ?? null,
          'total_count' => $result['data']['value']['total_count'] ?? 0,
          'consecutive_days' => $result['data']['value']['consecutive_days'] ?? 0,
          'total_points' => $result['data']['value']['total_points'] ?? 0,
        ],
      ];
    }

    return [
      'is_get' => false,
      'data' => [
        'last_sign_in_time' => null, //上次签到时间
        'last_sign_in_date' => null, //上次签到日期
        'total_count' => 0, //签到次数
        'consecutive_days' => 0, //连续签到天数
        'total_points' => 0, //签到点数
      ],
    ];
  }

  /**
   * 检查今日是否已签到
   * @param array $signInData 签到数据
   * @return bool
   */
  private static function IsSignedInToday(array $signInData): bool
  {
    $lastDate = $signInData['last_sign_in_date'] ?? null;
    $today = date('Y-m-d');
    return $lastDate === $today;
  }

  /**
   * 计算连续签到天数
   * @param array $signInData 签到数据
   * @return int
   */
  private static function CalculateConsecutiveDays(array $signInData): int
  {
    $lastDate = $signInData['last_sign_in_date'] ?? null;

    if ($lastDate === null) {
      return 1; // 首次签到
    }

    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if ($lastDate === $today) {
      return $signInData['consecutive_days'] ?? 1;
    }

    if ($lastDate === $yesterday) {
      return ($signInData['consecutive_days'] ?? 0) + 1;
    }

    // 中间断签，重新从1开始
    return 1;
  }

  /**
   * 计算签到点数
   * @param int $consecutiveDays 连续签到天数
   * @return int
   */
  private static function CalculatePoints(int $consecutiveDays): int
  {
    // // 基础签到点数
    // $basePoints = 1;

    // // 连续签到加成：每连续7天额外+1点
    // $bonusPoints = floor($consecutiveDays / 7);

    // // 本月全勤签到奖励
    // $monthPoints = 0;
    // if ($consecutiveDays >= date('t')) {
    //   $monthPoints = 100; // 例如，100点奖励
    // }

    // return $basePoints + $bonusPoints + $monthPoints;

    // 基础签到点数
    $basePoints = 5;

    // 连续签到加成：每连续签到1天+1点（最高+20点）
    $bonusPoints = min($consecutiveDays - 1, 20);

    // 连续签到里程碑奖励
    $milestonePoints = 0;
    $milestones = [7 => 10, 14 => 20, 30 => 50, 100 => 200];
    foreach ($milestones as $days => $points) {
      if ($consecutiveDays >= $days) {
        $milestonePoints = $points;
      }
    }

    return $basePoints + $bonusPoints + $milestonePoints;
  }

  /**
   * 获取今日签到状态
   * @param string $user_token 用户令牌
   * @return array
   */
  public static function GetTodaySignInStatus(string $user_token): array
  {
    $signInData = self::GetUserSignInData($user_token);

    return [
      'is_signed_in_today' => self::IsSignedInToday($signInData),
      'consecutive_days' => $signInData['consecutive_days'] ?? 0,
      'total_count' => $signInData['total_count'] ?? 0,
      'total_points' => $signInData['total_points'] ?? 0,
    ];
  }
}
