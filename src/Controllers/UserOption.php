<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\UserOption as UserOptionModel;
use MaterialDesignForum\Controllers\Token as TokenController;
use Exception; // 引入异常类

class UserOption extends UserOptionModel
{
  /**
   * 设置用户自定义设置
   * @param string $user_token 令牌
   * @param string $name 自定义设置名称
   * @param string|array $value 自定义设置值
   * @return array [is_set=>bool, data=>mixed, error=>string|null]
   */
  public static function SetUserOption(string $user_token, string $name, string|array $value): array
  {
    if (is_array($value)) {
      $value = json_encode($value);
    }
    try {
      $user_id = TokenController::GetUserId($user_token);
      if ($user_id === null) {
        return [
          'is_set' => false,
          'data' => null,
        ];
      }

      // 查找是否已存在该设置
      $data = self::where('user_id', $user_id)->where('name', $name)->first();

      if ($data) {
        // 更新数据
        $data->value = $value;
        $data->save();

        $data->value = json_decode($data->value, true);

        return [
          'is_set' => true,
          'data' => $data,
        ];
      } else {
        // 创建新数据
        $data = self::create([
          'user_id' => $user_id,
          'name' => $name,
          'value' => $value,
        ]);
        $data->value = json_decode($data->value, true);

        return [
          'is_set' => true,
          'data' => $data,
        ];
      }
    } catch (Exception $e) {
      // 捕获所有可能的异常，如数据库错误等
      return [
        'is_set' => false,
        'data' => null,
        'error' => 'SetUserOption failed: ' . $e->getMessage(),
      ];
    }
  }
  /**
   * 获取用户自定义设置
   * @param string $user_token 令牌
   * @param string $name 自定义设置名称
   * @return array [is_set=>bool, data=>mixed, error=>string|null]
   */
  public static function GetUserOption(string $user_token, string $name): array
  {
    try {
      $user_id = TokenController::GetUserId($user_token);
      if ($user_id === null) {
        return [
          'is_get' => false,
          'data' => null,
        ];
      }

      $data = self::where('user_id', $user_id)->where('name', $name)->first();

      if ($data) {
        $data->value = json_decode($data->value, true);
        return [
          'is_get' => true,
          'data' => $data,
        ];
      } else {
        return [
          'is_get' => false,
          'data' => null,
        ];
      }
    } catch (Exception $e) {
      return [
        'is_get' => false,
        'data' => null,
        'error' => 'GetUserOption failed: ' . $e->getMessage(),
      ];
    }
  }
  /**
   * 删除用户自定义设置
   * @param string $user_token 令牌
   * @param string $name 自定义设置名称
   * @return array [is_set=>bool, data=>mixed, error=>string|null]
   */
  public static function DeleteUserOption(string $user_token, string $name): array
  {
    try {
      $user_id = TokenController::GetUserId($user_token);
      if ($user_id === null) {
        return [
          'is_delete' => false,
          'data' => null,
        ];
      }
      $data = self::where('user_id', $user_id)->where('name', $name)->first();

      if ($data) {
        $data->delete();
        return [
          'is_delete' => true,
          'data' => $data, // 可能返回已删除的模型数据，视需求可以置为 null
        ];
      } else {
        return [
          'is_delete' => false,
          'data' => null,
        ];
      }
    } catch (Exception $e) {
      return [
        'is_delete' => false,
        'data' => null,
        'error' => 'DeleteUserOption failed: ' . $e->getMessage(),
      ];
    }
  }
}
