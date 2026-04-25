<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/05/20-15:53:29
 */

namespace MaterialDesignForum\Views;

use MaterialDesignForum\Plugins\Share;

class Admin
{
  public static $lang; //语言 从外部设置
  public static function Index()
  {
    return self::ReturnView();
  }
  private static function ReturnView()
  {
    return Share::HandleAdminPage(
      self::$lang
    );
  }
}
