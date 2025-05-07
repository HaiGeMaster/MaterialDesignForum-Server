<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Views;

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Models\Option;

class Server
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
