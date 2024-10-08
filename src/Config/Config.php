<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Config;

use MaterialDesignForum\Models\Option;

class Config
{
  public static $sql_is_connect = false;
  public static function getConfig()
  {
    $config['web_version'] = '1.0.0';
    $config['web_dev'] = true; //false; //
    $config['web_theme_path'] = '././public/themes/'; //主题路径
    $config['web_locale_path'] = '././public/locale/json/'; //语言路径

    //以下是可以修改的配置↓
    $config['mysql_hostname'] = 'localhost'; //数据库地址
    $config['mysql_username'] = 'root'; //数据库用户名
    $config['mysql_password'] = 'root'; //数据库密码
    $config['mysql_database'] = 'demo'; //'root';//数据库名
    $config['mysql_prefix'] = ''; //数据库表前缀
    //以上是可以修改的配置↑
    
    if (self::$sql_is_connect) {
      //请勿在此处修改邮箱配置。因为这些是从数据库自动获取的。
      //不要在这里写你的邮箱配置。请在后台管理设置中设置。
      $config['site_name'] = Option::Get('site_name');
      $config['default_language'] = Option::Get('default_language'); //默认语言
      $config['smtp_username'] = Option::Get('smtp_username'); //邮箱用户名(默认为QQ邮箱
      $config['smtp_password'] = Option::Get('smtp_password'); //邮箱密码(默认为QQ邮箱
      $config['smtp_send_name'] = Option::Get('smtp_send_name'); //发件人名称(默认为QQ邮箱
      $config['smtp_host'] = Option::Get('smtp_host'); //邮箱服务器地址(默认为QQ邮箱
      $config['smtp_port'] = Option::Get('smtp_port'); //邮箱服务器端口(默认为QQ邮箱
      $config['smtp_secure'] = Option::Get('smtp_secure'); //邮箱服务器安全协议(默认为QQ邮箱
    }else{
      $config['site_name'] = 'MaterialDesignForum'; //网站名称
      $config['default_language'] = 'zh_CN'; //默认语言
      $config['smtp_username'] = ''; //邮箱用户名(默认为QQ邮箱
      $config['smtp_password'] = ''; //邮箱密码(默认为QQ邮箱
      $config['smtp_send_name'] = ''; //发件人名称(默认为QQ邮箱
      $config['smtp_host'] = ''; //邮箱服务器地址(默认为QQ邮箱
      $config['smtp_port'] = ''; //邮箱服务器端口(默认为QQ邮箱
      $config['smtp_secure'] = ''; //邮箱服务器安全协议(默认为QQ邮箱
    }
    
    $config['mysql_max_query'] = 20; //最大每次查询数据库的数量（条） 过大可能导致服务器缓慢查询
    $config['seo'] = true; //是否开启SEO
    $config['php_default_theme'] = 'MaterialDesignForum-Vuetify2'; //默认主题
    return $config;
  }
  public static function Dev(): bool
  {
    return self::getConfig()['web_dev'];
  }
  public static function GetWebVersion(): string
  {
    return self::getConfig()['web_version'];
  }
  public static function GetWebSiteName(): string
  {
    return self::getConfig()['site_name'];
  }
  public static function GetWebDefaultLanguage(): string
  {
    return self::getConfig()['default_language'];
    // return Option::Get('default_language');
  }
  public static function GetMySqlHostname(): string
  {
    return self::getConfig()['mysql_hostname'];
  }
  public static function GetMySqlUsername(): string
  {
    return self::getConfig()['mysql_username'];
  }
  public static function GetMySqlPassword(): string
  {
    return self::getConfig()['mysql_password'];
  }
  public static function GetMySqlDatabase(): string
  {
    return self::getConfig()['mysql_database'];
  }
  public static function GetMySqlPrefix(): string
  {
    return self::getConfig()['mysql_prefix'];
  }
  /**
   * 获取网站标题名称
   * @param string $additional_content 要附加的副标题
   * @return string 网站名称 || 附加内容+网站名称
   */
  public static function GetWebTitleName($additional_content = null): string
  {
    if ($additional_content == null) {
      return self::getConfig()['site_name'];
    } else {
      return $additional_content . ' - ' . self::getConfig()['site_name'];
    }
  }
  /**
   * 获取网站描述
   * @param string $additional_content 要附加的内容
   * @return string 网站名称+网站描述 || 附加内容+网站名称+网站描述
   */
  public static function GetWebDescription($additional_content = null): string
  {
    if ($additional_content == null) {
      return self::GetWebTitleName() . ' - ' . Option::Get('site_description');
    } else {
      return $additional_content . ' - ' . self::GetWebTitleName() . ' - ' . Option::Get('site_description');
    }
  }
  /**
   * 获取网站关键字
   * @param string $additional_content 要附加的内容
   * @return string 网站名称+网站关键字 || 附加内容+网站名称+网站关键字
   */
  public static function GetWebKeywords($additional_content = null): string
  {
    if ($additional_content == null) {
      return self::GetWebTitleName() . ',' . Option::Get('site_keywords');
    } else {
      return $additional_content . ',' . self::GetWebTitleName() . ',' . Option::Get('site_keywords');
    }
  }
  public static function GetMySQLMaxQuery(): int
  {
    return self::getConfig()['mysql_max_query'];
  }
  public static function SEO(): bool
  {
    return self::getConfig()['seo'];
  }
  public static function GetWebThemePath(): string
  {
    return self::getConfig()['web_theme_path'];
  }
  public static function GetWebLocalePath(): string
  {
    return self::getConfig()['web_locale_path'];
  }
  public static function GetDefaultTheme(): string
  {
    return Option::Get('theme')||self::getConfig()['php_default_theme'];
  }
}
