<?php

/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

use MaterialDesignForum\Config\Config;

class MailCaptcha
{
  public $code;
  public $md5code;
  public $session_name;
  /**
   * @param string $SessionName 会话名称
   * @return void
   */
  public function __construct($SessionName = 'MailCaptchaCode')
  {
    $this->session_name = $SessionName;
    if (!isset($_SESSION) || $_SESSION[$this->session_name] == '') {
      session_start();
    } else if (isset($_SESSION[$this->session_name])) {
      $this->code = $_SESSION[$this->session_name];
      $this->md5code = md5($this->code);
    }
  }
  /**
   * @param void 创建验证码
   */
  public function CreateMailCode(): array
  {
    $this->code = '';
    $randcodestr = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890';
    for ($i = 0; $i < 5; $i++) {
      $this->code .= $randcodestr[rand(0, strlen($randcodestr) - 1)];
    }
    $this->md5code = md5($this->code);
    //$_SESSION[$this->session_name] = $this->code;
    $data = array(
      'code' => $this->code,
      'md5code' => $this->md5code,
    );
    return $data; //$_SESSION[$this->session_name];//
  }
  /**
   * @return string 获取验证码
   */
  public function GetCode(): string
  {
    $this->code = $_SESSION[$this->session_name];
    return $this->code;
  }
  /**
   * @param bool 验证验证码
   */
  public function CheckCode($code): bool
  {
    if ($code == $_SESSION[$this->session_name]) {
      $_SESSION[$this->session_name] = '';
      session_destroy();
      return true;
    } else {
      return false;
    }
  }
  // /**
  //  * @param bool 验证md5验证码
  //  */
  // public function CheckMd5Code($md5code): bool
  // {
  //   if ($md5code == $this->md5code) {
  //     $_SESSION[$this->session_name] = '';
  //     session_destroy();
  //     return true;
  //   } else {
  //     return false;
  //   }
  // }
  // /**
  //  * @param string $codes 验证码验证对比。可以是验证码或者md5验证码
  //  */
  // public function Check($codes): bool
  // {
  //   if($this->CheckCode($codes) || $this->CheckMd5Code($codes)){
  //     return true;
  //   }else{
  //     return false;
  //   }
  // }
  /**
   * @param string||array $to 收件人
   * @param string $subject 主题
   * @param string $content 内容
   * @return bool 发送邮件
   */
  public static function SendMail($to = "", $subject = '', $content = '<h1>Hello World Test</h1>')
  {
    //include_once __DIR__ . '/../../plugins/share.php';
    $config = Config::getConfig();
    $smtp_username = $config['smtp_username'];
    $smtp_password = $config['smtp_password'];
    $smtp_send_name = $config['smtp_send_name'];
    $smtp_host = $config['smtp_host'];
    $smtp_port = $config['smtp_port'];
    $smtp_secure = $config['smtp_secure'];

    // 记录发送状态
    $sendResults = [];

    // 创建新的 PHPMailer 实例
    $mail = new PHPMailer(true);

    // 配置 SMTP
    try {
      $mail->isSMTP();
      $mail->SMTPAuth = true;
      $mail->Host = $smtp_host;
      $mail->Username = $smtp_username;
      $mail->Password = $smtp_password;
      $mail->SMTPSecure = $smtp_secure; // ssl
      $mail->Port = $smtp_port;
      $mail->CharSet = 'UTF-8';
      $mail->setFrom($smtp_username, $smtp_send_name);
      $mail->addReplyTo($smtp_username, 'Information');

      // 检查收件人是否为数组
      if (is_array($to)) {
        foreach ($to as $v) {
          // 为每个收件人发送一封邮件
          $mail->addAddress($v);

          // 设置邮件主题和内容
          $mail->isHTML(true);
          $mail->Subject = $subject;
          if (is_array($content)) {
            $allcontent = implode('<br>', $content);
            $mail->Body = $allcontent;
          } else {
            $mail->Body = $content;
          }

          // 发送邮件
          if ($mail->send()) {
            $sendResults[$v] = true; // 记录成功
          } else {
            $sendResults[$v] = false; // 记录失败
          }

          // 清除收件人以便下次发送
          $mail->clearAddresses();
        }
      } else {
        // 如果是单个收件人
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        if (is_array($content)) {
          $allcontent = implode('<br>', $content);
          $mail->Body = $allcontent;
        } else {
          $mail->Body = $content;
        }

        // 发送邮件
        if ($mail->send()) {
          $sendResults[$to] = true; // 记录成功
        } else {
          $sendResults[$to] = false; // 记录失败
        }
      }

      return $sendResults; // 返回发送结果
    } catch (Exception $e) {
      // 处理异常
      return false;
    }
  }
  // public function SendMail($to = "", $subject = '', $content = '<h1>Hello World Test</h1>')
  // {
  //   //include_once __DIR__ . '/../../plugins/share.php';
  //   $config = Config::getConfig();
  //   $smtp_username = $config['smtp_username'];
  //   $smtp_password = $config['smtp_password'];
  //   $smtp_send_name = $config['smtp_send_name'];
  //   $smtp_host = $config['smtp_host'];
  //   $smtp_port = $config['smtp_port'];
  //   $smtp_secure = $config['smtp_secure'];
  //   $mail = new PHPMailer(true);
  //   try {
  //     $mail->isSMTP();
  //     $mail->SMTPAuth = true;
  //     $mail->Host = $smtp_host;
  //     $mail->Username = $smtp_username;
  //     $mail->Password = $smtp_password;
  //     $mail->SMTPSecure = $smtp_secure;//ssl
  //     $mail->Port = $smtp_port;
  //     $mail->CharSet = 'UTF-8';
  //     $mail->setFrom($smtp_username, $smtp_send_name);
  //     $mail->addReplyTo($smtp_username, ['Information']);
  //     if (is_array($to)) {
  //       foreach ($to as $v) {
  //         $mail->addAddress($v);
  //       }
  //     } else {
  //       $mail->addAddress($to);
  //     }
  //     $mail->isHTML(true);
  //     $mail->Subject = $subject;
  //     if (is_array($content)) {
  //       $allcontent = implode('<br>', $content);
  //       $mail->Body = $allcontent;
  //     } else {
  //       $mail->Body = $content;
  //     }
  //     $mail->send();
  //     return true;
  //   } catch (Exception $e) {
  //     //return $e;
  //     return false;
  //   }
  // }
}
