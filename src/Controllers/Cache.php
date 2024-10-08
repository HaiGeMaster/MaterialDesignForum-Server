<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Cache as CacheModel;
use MaterialDesignForum\Plugins\Share;

class Cache extends CacheModel
{

  /**
   * 是否有效的验证码
   * @param string $name 验证码名称
   * @return bool
   */
  public static function IsVaildCaptcha($name): bool
  {
    $captcha = Cache::where('name', '=', $name)->where('life_time', '>', Share::ServerTime())->first();
    if ($captcha) {
      return true;
    } else {
      return false;
    }
  }
  /**
   * 删除验证码
   * @param string $name 验证码名称
   * @return void
   */
  public static function DeleteCaptcha($name)
  {
    self::where('name', '=', $name)->delete();
    self::where('life_time', '<', Share::ServerTime())->delete();
  }
}
