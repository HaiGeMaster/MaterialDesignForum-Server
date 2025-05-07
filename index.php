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

// echo (
//   Install::AsInstall()) ?
//   (
//     (
//       Api::IsApi()) ?
//     Api::HandleAPI() :
//     Route::HandleRoute()
//   ) : (
//     (
//       Api::IsApi()) ?
//     Api::HandleInstallAPI() :
//     Route::HandleInstallRoute()
//   );
