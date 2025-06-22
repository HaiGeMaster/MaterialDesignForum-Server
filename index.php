<?php
include_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/src/Core/Database.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

use MaterialDesignForum\Routes\Api;
use MaterialDesignForum\Routes\Page;
use MaterialDesignForum\Config\Install;

    // //输出从请求标头中获取的内容
    // echo json_encode(getallheaders());
    // return;

try{
  //将请求地址和请求数据输出到/log/request.log
  $requestIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $requestUri = $_SERVER['REQUEST_URI'];
  $requestMethod = $_SERVER['REQUEST_METHOD'];
  $requestData = file_get_contents('php://input');
  $logData = [
      'ip' => $requestIP,
      'uri' => $requestUri,
      'method' => $requestMethod,
      'data' => $requestData,
      'timestamp' => date('Y-m-d H:i:s')
  ];
  file_put_contents('request.json', json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
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
}catch(\Exception $e) {

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

  echo '<h1>Material Design Forum 发生错误 errow</h1>';
  echo '<code>发生错误：<br>';
  echo '<pre>';
  echo '错误信息：' . $e->getMessage() . '<br>';
  echo '错误代码：' . $e->getCode() . '<br>';
  echo '错误文件：' . $e->getFile() . '<br>';
  echo '错误行号：' . $e->getLine() . '<br>';
  echo '错误追踪：<br>' . nl2br($e->getTraceAsString()) . '<br>';
  echo '</pre>';
  echo '</code>';
}