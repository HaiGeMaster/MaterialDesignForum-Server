<?php
include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/src/Core/Database.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

use MaterialDesignForum\Routes\Api;
use MaterialDesignForum\Routes\Page;
use MaterialDesignForum\Config\Install;
// use Dotenv\Dotenv as Dotenv;

// //输出从请求标头中获取的内容
// echo json_encode(getallheaders());
// return;

try {

  // $dotenv = Dotenv::createImmutable('././');
  // $dotenv->load();
  //检查是否为安装模式
  if (Install::AsInstall()) {
    if (Api::IsApi()) {
      header('Content-Type: application/json');
      echo Api::HandleAPI();
    } else {
      echo Page::HandleRoute();
    }
  } else {
    if (Api::IsApi()) {
      header('Content-Type: application/json');
      echo Api::HandleInstallAPI();
    } else {
      echo Page::HandleInstallRoute();
    }
  }
} catch (\Exception $e) {

  //直接进入安装页面
  // if (Api::IsApi()) {
  //   header('Content-Type: application/json');
  //   echo Api::HandleInstallAPI();
  // } else {
  //   echo Page::HandleInstallRoute();
  // }

  // 捕获异常并返回错误信息
  // header('Content-Type: application/json');
  // echo json_encode([
  //   'error' => true,
  //   'message' => $e->getMessage(),
  //   'code' => $e->getCode()
  // ]);

  header('Content-Type: text/html; charset=utf-8');
  echo '<!DOCTYPE html>';
  echo '<html lang="zh-CN">';
  echo '<head>';
  echo '<meta charset="UTF-8">';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
  echo '<title>Material Design Forum 错误</title>';
  echo '</head>';
  echo '<body>';

  echo '<h1 style="color:red;">Material Design Forum 发生错误 error</h1>';
  echo '<code>发生错误：<br>';
  echo '<pre>';
  echo '错误信息：' . $e->getMessage() . '<br>';
  echo '错误代码：' . $e->getCode() . '<br>';
  echo '错误文件：' . $e->getFile() . '<br>';
  echo '错误行号：' . $e->getLine() . '<br>';
  echo '错误追踪：<br>' . nl2br($e->getTraceAsString()) . '<br>';
  echo '</pre>';
  echo '</code>';

  echo '<p>请检查服务器日志以获取更多详细信息。</p>';
  echo '<p>如果您是开发者，请检查代码并修复错误。</p>';
  echo '<p>如果您是用户，请联系网站管理员。</p>';
  echo '<p>感谢您的理解和支持！</p>';
  echo '<p>Please check the server logs for more detailed information.</p>';
  echo '<p>If you are a developer, please check the code and fix the error.</p>';
  echo '<p>If you are a user, please contact the website administrator.</p>';
  echo '<p>Thank you for your understanding and support!</p>';



  echo '</body>';
  echo '</html>';
  exit;
}
