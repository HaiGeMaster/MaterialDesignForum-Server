<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Views;

use MaterialDesignForum\Plugins\Share;
// use MaterialDesignForum\Plugins\i18n;
// use MaterialDesignForum\Models\Option;

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
