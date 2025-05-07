<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Config;

use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Models\Option;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Capsule\Manager as Capsule;

// class SensitiveParameterValue
// {
//     private $value;

//     public function __construct($value)
//     {
//         $this->value = $value;
//     }

//     public function getValue()
//     {
//         return $this->value;
//     }
// }

// class SensitiveParameterValue {
//   private $value;

//   public function __construct($value) {
//       $this->value = $value;
//   }

//   public function getValue() {
//       return $this->value;
//   }
// }


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
      $conn = @mysqli_connect(Config::GetMySqlHostname(), Config::GetMySqlUsername(), Config::GetMySqlPassword());
      if (!$conn) {
        self::SaveInstallJSON(false, 1);
        return false;
      } else {
        // self::SaveInstallJSON(false, 2);

        //如果路由含没有localhost则返回true
        if (!preg_match('/localhost/', $_SERVER['HTTP_HOST'])) {
          mysqli_close($conn);
          
          
          return true;
        }
        // mysqli_close($conn);
        // return true;
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
//   public static function SetConfigPHP($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase, $mysqlPrefix = '')
// {
//     if (self::AsInstall()) {
//         return [
//             'install' => true,
//             'data' => self::GetInstallInfoJson()
//         ];
//     }

//     $data = false;
//     $errorLog = [];
    
//     try {
//         // 1. 测试数据库连接
//         $conn = @mysqli_connect(
//             $mysqlHostname, 
//             $mysqlUsername, 
//             $mysqlPassword,
//             $mysqlDatabase
//         );
        
//         if (!$conn) {
//             throw new \Exception("mysqli_connect_error: " . mysqli_connect_error());
//         }

//         // 2. 读取并处理SQL文件
//         // $sqlFilePath = self::$defaultSQLFilePath;
//         // if (!file_exists($sqlFilePath)) {
//         //     throw new \Exception("SQL Not Found: " . $sqlFilePath);
//         // }

//         // $sqlContent = file_get_contents($sqlFilePath);
//         // if ($mysqlPrefix && $mysqlPrefix !== '') {
//         //     // // 使用正则表达式安全替换表名前缀
//         //     // $pattern = '/`([^`]+)`/'; 
//         //     // $replacement = '`' . $mysqlPrefix . '`\\1';
//         //     // $sqlContent = preg_replace_callback($pattern, function($matches) use ($mysqlPrefix) {
//         //     //     return str_replace($matches[0], $replacement, $matches[0]);
//         //     // }, $sqlContent);

//         //     //使用普通替换表名前缀
//         //     $sqlContent = str_replace('CREATE TABLE `', 'CREATE TABLE `' . $mysqlPrefix, $sqlContent);
//         // }

//         // 3. 执行SQL导入（使用事务处理）
//         // $conn->begin_transaction();
//         // $batchSize = 1000; // 每批提交1000条
//         // $statements = explode(';', $sqlContent);
//         // $successCount = 0;
//         // $errorCount = 0;

//         // foreach ($statements as $stmt) {
//         //     $stmt = trim($stmt);
            
//         //     // 跳过空语句和注释
//         //     if (empty($stmt) || strpos($stmt, '//') === 0 || strpos($stmt, '--') === 0) {
//         //         continue;
//         //     }

//         //     if (!$conn->query($stmt)) {
//         //         $errorLog[] = [
//         //             'line' => $successCount + 1,
//         //             'error' => $conn->error
//         //         ];
//         //         $errorCount++;
                
//         //         if ($errorCount > 5) { // 最多允许5次错误后回滚
//         //             throw new \Exception("error 5");
//         //         }
//         //     } else {
//         //         $successCount++;
//         //     }

//         //     // 分批提交
//         //     if ($successCount % $batchSize === 0) {
//         //         $conn->commit();
//         //         $conn->begin_transaction(); // 开启新事务
//         //     }
//         // }

//         // // 提交剩余语句
//         // $conn->commit();

//         // 4. 保存配置文件
//         $config = file_get_contents(self::$defaultConfigFiePath);
//         $config = str_replace('{mysql_hostname}', $mysqlHostname, $config);
//         $config = str_replace('{mysql_username}', $mysqlUsername, $config);
//         $config = str_replace('{mysql_password}', $mysqlPassword, $config);
//         $config = str_replace('{mysql_database}', $mysqlDatabase, $config);
//         $config = str_replace('{mysqlPrefix}', $mysqlPrefix, $config);
        
//         if (file_put_contents(self::$defaultConfigFiePath, $config) === false) {
//             throw new \Exception("save config error");
//         }

//         // 5. 记录安装信息
//         $data = self::SaveInstallJSON(false, 2);
        
//     } catch (\Exception $e) {
//         // 发生错误时回滚事务
//         if ($conn) {
//             $conn->rollback();
//         }
        
//         // 记录详细错误信息
//         $errorLog[] = [
//             'message' => $e->getMessage(),
//             'stack' => $e->getTrace(),
//             'error' => $conn->error
//         ];
//     } finally {
//         // 关闭数据库连接
//         if ($conn) {
//             mysqli_close($conn);
//         }
//     }

//     // 构建返回结果
//     return [
//         // 'success' => $data !== false,
//         // 'errors' => $errorLog,
//         // 'imported_rows' => $successCount,
//         // 'errored_rows' => $errorCount,
//         'sql' => $data
//     ];
// }
  public static function SetConfigPHP($mysqlHostname, $mysqlUsername, $mysqlPassword, $mysqlDatabase, $mysqlPrefix = '')
  {
    if (self::AsInstall()) {
      return [
        'is_install' => true,
        'data' => self::GetInstallInfoJson()
      ];
    }

    $data = false;
    // try {
    //   //先测试是否能连接数据库
    //   $conn = mysqli_connect($mysqlHostname, $mysqlUsername, $mysqlPassword);
    //   if (!$conn) {
    //     $data = false;
    //   } else {
        $config = file_get_contents(self::$defaultConfigFiePath);
        // $config = str_replace('// namespace', 'namespace', $config);
        $config = str_replace('{mysql_hostname}', $mysqlHostname, $config);
        $config = str_replace('{mysql_username}', $mysqlUsername, $config);
        $config = str_replace('{mysql_password}', $mysqlPassword, $config);
        $config = str_replace('{mysql_database}', $mysqlDatabase, $config);
        //$config = str_replace('{mysqlPrefix}', $mysqlPrefix, $config);
        $set = file_put_contents(self::$configFiePath, $config);
        if ($set) {

          $sql = file_get_contents(self::$defaultSQLFilePath);
          $sql_is_install = DB::unprepared($sql);
          if ($sql_is_install) {
            $data = self::SaveInstallJSON(false, 2);
          } else {
            $data = false;
          }
        } else {
          $data = false;
        }
      // }
    // } catch (\Exception $e) {
    //   $data = false;
    //   echo $e->getMessage();
    // }
    return [
      'is_install' => $data
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
