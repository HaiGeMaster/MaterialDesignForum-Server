<?php

include_once __DIR__ . '/vendor/autoload.php';

include_once __DIR__ . '/src/Core/Database.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

use MaterialDesignForum\Routes\Api;
use MaterialDesignForum\Routes\Page;
use MaterialDesignForum\Config\Install;
use MaterialDesignForum\Config\Config;


$whoops = new \Whoops\Run;
if(Config::Dev()){
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
}else{
  $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
}
// $whoops->pushHandler(new \Whoops\Handler\CallbackHandler);
// $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler);
// $whoops->pushHandler(new \Whoops\Handler\XmlResponseHandler);
$whoops->register();

try {

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

  $whoops->handleException($e);

}
