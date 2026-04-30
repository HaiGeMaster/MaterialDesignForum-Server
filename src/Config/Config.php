<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/05/20-15:53:29
 */

namespace MaterialDesignForum\Config;

use Dotenv\Dotenv;

use MaterialDesignForum\Models\Option;
use MaterialDesignForum\Plugins\EnvEditor;

class Config
{

  public static ?Dotenv $dotenv = null;
  // public static $sql_is_connect = false;
  public static function getConfig()
  {
    //首先检查是否存在.env文件
    if (!file_exists('././.env')) {
      // throw new \Exception('未找到.env文件');
      //如果没有，则读取.env.example文件
      if (file_exists('././.env.example')) {
        copy('././.env.example', '././.env');
      }
    }

    // 指定 .env 文件所在目录（通常是项目根目录）
    self::$dotenv = Dotenv::createImmutable('././');
    self::$dotenv->load();

    $config['web_version'] = '1.0.0';
    $config['web_dev'] = true; //false; //
    $config['web_theme_path'] = '././public/themes/'; //主题路径
    $config['web_locale_path'] = '././public/locale/json/'; //语言路径

    $config['mysql_hostname'] = $_ENV['DB_HOST']; //数据库地址
    $config['mysql_username'] = $_ENV['DB_USERNAME']; //数据库用户名
    $config['mysql_password'] = $_ENV['DB_PASSWORD']; //数据库密码
    $config['mysql_database'] = $_ENV['DB_DATABASE']; //'root';//数据库名
    $config['mysql_port'] = $_ENV['DB_PORT']; //数据库端口
    $config['mysql_prefix'] = $_ENV['DB_PREFIX']; //数据库表前缀

    $config['site_name'] = $_ENV['APP_NAME']; //网站名称
    $config['default_language'] = $_ENV['APP_LOCALE']; //默认语言
    $config['smtp_username'] = $_ENV['MAIL_USERNAME']; //邮箱用户名(默认为QQ邮箱
    $config['smtp_password'] = $_ENV['MAIL_PASSWORD']; //邮箱密码(默认为QQ邮箱密码
    $config['smtp_send_name'] = $_ENV['MAIL_SENDER_NAME']; //发件人名称(
    $config['smtp_host'] = $_ENV['MAIL_HOST']; //邮箱服务器地址(
    $config['smtp_port'] = $_ENV['MAIL_PORT']; //邮箱服务器端口(
    $config['smtp_secure'] = $_ENV['MAIL_SCHEME']; //邮箱服务器安全协议(
    $config['smtp_mailer'] = $_ENV['MAIL_MAILER']; //邮箱服务器发送器(

    $config['mysql_max_query'] = $_ENV['APP_MAX_QUERY']; //最大每次查询数据库的数量（条） 过大可能导致服务器缓慢查询
    $config['seo'] = $_ENV['APP_SEO']; //是否开启SEO
    $config['php_default_theme'] = $_ENV['APP_DEFAULT_THEME']; //默认主题
    return $config;
  }
  /**
   * 设置环境变量
   * @param string $key 环境变量键
   * @param string $value 环境变量值
   * @return bool 是否设置成功
   */
  public static function SetEnv(string $key, string $value): bool
  {
    // 读取
    $parser = new EnvEditor('././.env');
    // 修改
    // $parser->set($key, $value);

    //去除value中的空格
    // $value = trim($value);

    //如果包含空格则添加引号
    if (strpos($value, ' ') !== false) {
      $value = '"' . $value . '"';
    }

    $parser->set($key, $value);
    // 写入
    return $parser->save();
  }
  public static function Dev(): bool
  {
    return self::getConfig()['web_dev'] && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
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
  public static function GetMySqlPort(): int
  {
    return self::getConfig()['mysql_port'];
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
      return self::getConfig()['site_name'] ?? '';
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
      return self::GetWebTitleName() . ' - ' . Option::Get('site_description') ?? '';
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
      return self::GetWebTitleName() . ',' . Option::Get('site_keywords') ?? '';
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
    return Option::Get('theme') ?? self::getConfig()['php_default_theme'];
  }
}
