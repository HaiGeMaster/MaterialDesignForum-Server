<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Vuetify2
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-MDUI2
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Config;

use MaterialDesignForum\Plugins\i18n;

use MaterialDesignForum\Models\Option;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Illuminate\Database\Capsule\Manager as Capsule;


class Install
{
  public static $path = '././src/Config/Install.json';
  public static $installIndexFilePath = '././public/themes/MaterialDesignForum-Vuetify2/install.html';
  public static $defaultConfigFiePath = '././src/Config/Config.default.php.txt';
  public static $configFiePath = '././src/Config/Config.php';
  public static $defaultSQLFilePath = '././demo_table.sql';

  /**
   * @return bool 是否已经安装
   */
  public static function AsInstall()
  {
    $json = null;
    try {
      $conn = Option::Get('site_name');
      if (!$conn) {
        self::SaveInstallJSON(false, 1);
        return false;
        exit();
      } else {
        // self::SaveInstallJSON(false, 2);
        //如果路由含没有localhost则返回true
        // if (!preg_match('/localhost/', $_SERVER['HTTP_HOST'])) {
          self::SaveInstallJSON(true, 3);
          return true;
        // }
      }
      //默认情况下返回false
      $json = file_get_contents(self::$path);
    } catch (\Exception $e) {
      $json = false;
    }
    if ($json === false) {
      return true;
    }
    //如果请求路由文本中含有[a-z]{2}_[A-Z]{2}}/install则返回true
    // if (preg_match('/[a-z]{2}_[A-Z]{2}}\/install/', $_SERVER['REQUEST_URI'])) {
    //   return true;
    // }
    $json = json_decode($json, true);
    return $json['install'];
  }
  public static function GetInstallInfoJson()
  {
    $json = [
      'install' => true,
      'step' => 0
    ];
    try {
      $json = file_get_contents(self::$path);
      $json = json_decode($json, true);
    } catch (\Exception $e) {
    }
    return $json;
  }
  public static function InstallView($lang = '')
  {
    if ($lang == '') {
      $lang = i18n::i18n()->locale;
    }
    $upcoming = i18n::t('Message.App.UpComing', $lang);
    $index_html = file_get_contents(self::$installIndexFilePath);
    $index_html = str_replace('{lang}', $lang, $index_html);
    $index_html = str_replace('{title}', i18n::t('Message.App.Install'), $index_html);
    $index_html = str_replace('{description}', i18n::t('Message.App.Install'), $index_html);
    $index_html = str_replace('{keywords}', i18n::t('Message.App.Install'), $index_html);
    $index_html = str_replace('{upcoming}', $upcoming, $index_html);
    // $index_html = str_replace('{content}', '', $index_html);
    $index_html = str_replace('{script}', '', $index_html);
    return $index_html;
  }
  public static function SaveInstallJSON($install = false, $step = 0): bool
  {
    //如果文件不存在则创建
    if (!file_exists(self::$path)) {
      $set = file_put_contents(self::$path, '');
      if (!$set) {
        return false;
      }
    }
    $json = file_get_contents(self::$path);
    $json = json_decode($json, true);
    $json['install'] = $install;
    $json['step'] = $step;
    $json = json_encode($json);
    $set = file_put_contents(self::$path, $json);
    return $set;
  }
  /**
   * 安装1：设置数据库
   * @param string $mysqlHostname 数据库地址
   * @param string $mysqlUsername 数据库用户名
   * @param string $mysqlPassword 数据库密码
   * @param string $mysqlDatabase 数据库名称
   * @param string $mysqlPrefix 数据库前缀
   * @return array [sql=>bool]
   */
  public static function SetConfigPHP($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase, $mysqlPrefix = '')
  {
    if (self::AsInstall()) {
      return [
        'is_install' => true,
        'data' => self::GetInstallInfoJson()
      ];
    }

    $is_install = false;

    $capsule = new Capsule();
    $capsule->addConnection([
      'driver' => 'mysql',
      'host' => $mysqlHostname,
      'database' => $mysqlDatabase,
      'username' => $mysqlUsername,
      'password' => $mysqlPassword,
      'prefix' => $mysqlPrefix,
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $site_name = Option::Get('site_name');
    if ($site_name) {
      // print_r($site_name);
      // exit;

      $config = file_get_contents(self::$defaultConfigFiePath);
      // $config = str_replace('// namespace', 'namespace', $config);
      $config = str_replace('{mysql_hostname}', $mysqlHostname, $config);
      $config = str_replace('{mysql_username}', $mysqlUsername, $config);
      $config = str_replace('{mysql_password}', $mysqlPassword, $config);
      $config = str_replace('{mysql_database}', $mysqlDatabase, $config);
      //$config = str_replace('{mysqlPrefix}', $mysqlPrefix, $config);
      $set = file_put_contents(self::$configFiePath, $config);
      if ($set) {
        $is_install = self::SaveInstallJSON(false, 2);
      }
    }

    return [
      'is_install' => $is_install
    ];
  }
  /**
   * 安装2-0:测试系统邮件
   * @param string $to 邮箱地址
   * @param string $smtp_username 邮箱用户名
   * @param string $smtp_password 邮箱密码
   * @param string $smtp_send_name 发件人名称
   * @param string $smtp_host 邮箱服务器地址
   * @param string $smtp_port 邮箱服务器端口
   * @param string $smtp_secure 邮箱服务器安全协议
   * @return array [mail=>bool]
   */
  public static function TestMail($to = '', $smtp_username = '', $smtp_password = '', $smtp_send_name = 'name', $smtp_host = '', $smtp_port = 465, $smtp_secure = 'ssl')
  {
    $data = false;
    if (self::AsInstall()) {
      return [
        'install' => true,
        'data' => self::GetInstallInfoJson()
      ];
    }
    try {
      $data = false;
      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->SMTPAuth = true;
      $mail->Host = $smtp_host;
      $mail->SMTPSecure = $smtp_secure;
      $mail->Port = $smtp_port;
      $mail->CharSet = 'UTF-8';
      $mail->Username = $smtp_username;
      $mail->Password = $smtp_password;

      $mail->setFrom($smtp_username, $smtp_send_name);
      if (is_array($to)) {
        foreach ($to as $v) {
          $mail->addAddress($v);
        }
      } else {
        $mail->addAddress($to);
      }
      $mail->Subject = 'MaterialDesignForum Test Mail';
      $mail->Body = 'MaterialDesignForum Test Mail';
      $mail->isHTML(true);
      if ($mail->send()) {
        $data = true;
      } else {
        $data = false;
      }
    } catch (Exception $e) {
      $data = false;
    }
    return [
      'mail' => $data
    ];
  }
  /**
   * 安装2-1：设置系统邮件
   */
  public static function SetSqlMail($smtp_username, $smtp_password, $smtp_send_name, $smtp_host, $smtp_port, $smtp_secure)
  {
    $data = false;
    $data = Option::Set('smtp_username', $smtp_username) &&
      Option::Set('smtp_password', $smtp_password) &&
      Option::Set('smtp_send_name', $smtp_send_name) &&
      Option::Set('smtp_host', $smtp_host) &&
      Option::Set('smtp_port', $smtp_port) &&
      Option::Set('smtp_secure', $smtp_secure);
    if ($data) {
      self::SaveInstallJSON(false, 3);
    }
    return [
      'mail' => $data
    ];
  }
  /**
   * 安装3：设置网站信息
   */
  public static function SetWebInfo($site_name, $default_language)
  {
    $data = false;
    $data = Option::Set('site_name', $site_name) &&
      Option::Set('default_language', $default_language);
    if ($data) {
      self::SaveInstallJSON(false, 4);
    }
    return [
      'web' => $data
    ];
  }
  /**
   * 安装4：启动站点，完成安装
   */
  public static function SetWebInstallChange()
  {
    return [
      'web' => self::SaveInstallJSON(true, 0)
    ];
  }
}
