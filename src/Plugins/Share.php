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

namespace MaterialDesignForum\Plugins;

use Carbon\Carbon;

use MaterialDesignForum\Config\Config;

use MaterialDesignForum\Models\Option;

use MaterialDesignForum\Plugins\i18n;

class Share
{
  /**
   * 处理主题页面数据嵌入
   * @param string $lang 语言
   * @param string $title 标题
   * @param string $description 描述
   * @param string $keywords 关键词
   * @param string $script 脚本
   * @return string
   */
  public static function HandleThemePage(
    $lang = '',
    $title = '',
    $description = '',
    $keywords = '',
    $content = '',
    $script = '',
    $theme = ''
  ) {
    if ($theme == '') {
      $theme = self::GetClientThemeName();
    }
    if ($lang == '') {
      $lang = i18n::i18n()->locale;
    }
    $index_html = '';

    // //如果域名包含localhost则为开发环境
    // // if (Config::Dev() && $_SERVER['HTTP_HOST'] == 'localhost:8080') {
    // if (Config::Dev() && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    //   // $index_html = 本地localhost:8080
    //   // $index_html = file_get_contents('http://localhost:8080');

    //   //如果theme包含Vuetify2则为Vuetify2主题
    //   if (strpos($theme, 'Vuetify2') !== false) {
    //     $index_html = file_get_contents('http://localhost:8080');
    //   } else {
    //     $index_html = file_get_contents('http://localhost:5173');
    //   }
    // } else {
    //   // $index_html = 服务器
    //   $index_html = file_get_contents(Config::GetWebThemePath() . $theme . '/index.html');
    // }
    $index_html = file_get_contents(Config::GetWebThemePath() . $theme . '/index.html');
    $upcoming = i18n::t('Message.App.UpComing', $lang);
    if (i18n::VerificationLanguages($lang)) {
      $index_html = str_replace('{lang}', $lang, $index_html);
      $index_html = str_replace('{title}', $title, $index_html);
      $index_html = str_replace('{description}', $description, $index_html);
      $index_html = str_replace('{keywords}', $keywords, $index_html);
      $index_html = str_replace('{upcoming}', $upcoming, $index_html);
      $index_html = str_replace('{content}', $content, $index_html);
      $index_html = str_replace('<script id="seo"></script>', '<script id="seo">' . $script . '</script>', $index_html);
    } else {
      $index_html = str_replace('{lang}', i18n::i18n()->locale, $index_html);
      $index_html = str_replace('{title}', Option::Get('site_name'), $index_html);
      $index_html = str_replace('{description}', Option::Get('site_description'), $index_html);
      $index_html = str_replace('{keywords}', Option::Get('site_keywords'), $index_html);
      $index_html = str_replace('{upcoming}', $upcoming, $index_html);
      $index_html = str_replace('{content}', Option::Get('site_name') . ' - ' . Option::Get('site_keywords') . ' - ' . Option::Get('site_description'), $index_html);
      $index_html = str_replace('<script id="seo"></script>', '<script id="seo">' . $script . '</script>', $index_html);
    }

    return $index_html;
  }
  /**
   * share.php
   * 读入index.html
   * @param string $lang 语言
   * @param string $title 标题
   * @param string $description 描述
   * @param string $keywords 关键词
   * @param string $content 内容
   * @param string $script 脚本
   * @return string
   */
  public static function HandleAdminPage($lang, $theme = 'MaterialDesignForum-Vuetify2')
  {
    if ($theme == '') {
      //$theme = self::GetClientThemeName();
      // $theme = Config::GetDefaultTheme();
      $theme = 'MaterialDesignForum-Vuetify2';
    }
    $index_html = file_get_contents(Config::GetWebThemePath() . $theme . '/admin.html');
    $upcoming = i18n::t('Message.App.UpComing', $lang);
    if (i18n::VerificationLanguages($lang)) {
      $index_html = str_replace('{lang}', $lang, $index_html);
    } else {
      $index_html = str_replace('{lang}', i18n::i18n()->locale, $index_html);
    }
    $index_html = str_replace('{title}', i18n::t('Message.Components.Avatar.ManagementPanel'), $index_html);
    $index_html = str_replace('{description}', i18n::t('Message.Components.Avatar.ManagementPanel'), $index_html);
    $index_html = str_replace('{keywords}', i18n::t('Message.Components.Avatar.ManagementPanel'), $index_html);
    $index_html = str_replace('{upcoming}', $upcoming, $index_html);
    // $index_html = str_replace('{content}', '<div style="display:none">' . i18n::t('Message.Components.Avatar.ManagementPanel') . '</div>', $index_html);
    $index_html = str_replace('{script}', '<script id="seo"></script>', $index_html);
    return $index_html;
  }
  /**
   * 处理Array数据为SQL 处理排序
   * @param string $data 数据 示例:-follower_count +follower_count
   * @return array [field,sort]
   */
  public static function HandleArrayField($data): array
  {
    //示例:-follower_count
    //获取第一个字符为排序方式
    $sort = substr($data, 0, 1);
    //获取排序字段
    $field = substr($data, 1);
    return array(
      'field' => $field,
      'sort' => $sort == '-' ? 'desc' : 'asc'
    );
  }
  /**
   * 处理Array数据为JSON
   * @param array $data 数据
   * @return JSON||null
   */
  public static function HandleArrayToJSON($data)
  {
    try {
      //return json_encode($data);
      if (Config::Dev()) {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      } else {
        return json_encode($data); //, JSON_UNESCAPED_UNICODE);
      }
    } catch (\Exception $e) {
      return null;
    }
    return null;
  }
  /**
   * 处理JSON数据为数组
   * @param string $data 数据
   * @return array
   */
  public static function HandleJSONToArray($data, $assoc = false)
  {
    if (Config::Dev() || $assoc) {
      return json_decode($data, true);
    } else {
      return json_decode($data);
    }
  }
  /**
   * 处理数据和分页
   * @param $data 查询的分页数据数据
   * @return array ['is_get' => bool, 'data' => array, 'pagination' => array]
   */
  public static function HandleDataAndPagination($data = null)
  {
    $rdata = [
      'is_get' => false,
      'data' => null,
      'pagination' => [
        'page' => 1, //当前页码
        'per_page' => 0, //每页显示的数量
        'total' => 0, //总共有多少个项目
        'pages' => 0, //总共有多少页
        'previous' => null, //上一页
        'next' => null, //下一页
      ]
    ];
    if ($data != null) {
      $previousPageUrl = $data->total() == 1 ? null : $data->previousPageUrl();
      $nextPageUrl = $data->total() == 1 ? null : $data->nextPageUrl();
      $previousPageUrl = $previousPageUrl != null ? intval(str_replace('/?page=', '', $previousPageUrl)) : null;
      $nextPageUrl = $nextPageUrl != null ? intval(str_replace('/?page=', '', $nextPageUrl)) : null;
      $data_items = $data->items();
      //如果$data_items是空数组，则返回null
      if (count($data_items) == 0 || $data_items == null || $data_items == []) {
        $data_items = null;
      }
      $rdata = [
        'is_get' => $data_items != null,
        'data' => $data_items, //bug 可能为[]空数组
        'pagination' => [
          'page' => $data->total() == 1 ? 1 : $data->currentPage(), //当前页码
          'per_page' => $data->total() == 1 ? 1 : $data->perPage(), //每页显示的数量
          'total' => $data->total(), //总共有多少个项目
          'pages' => $data->total() == 1 ? 1 : $data->lastPage(), //总共有多少页
          'previous' => $previousPageUrl, //上一页
          'next' => $nextPageUrl, //下一页
        ]
      ];
    }
    return $rdata;
  }
  /**
   * 处理合并数据和分页 适用于不同表的数据和分页
   * @param $data 查询的数据->items()
   * @param $pagination 分页数据
   * @return array ['is_get' => bool, 'data' => array, 'pagination' => array]
   */
  public static function HandleMergeDataAndPagination($data, $pagination)
  {
    $rdata = [
      'is_get' => false,
      'data' => null,
      'pagination' => [
        'page' => 1, //当前页码
        'per_page' => 0, //每页显示的数量
        'total' => 0, //总共有多少个项目
        'pages' => 0, //总共有多少页
        'previous' => null, //上一页
        'next' => null, //下一页
      ]
    ];
    if ($data != null) {
      $previousPageUrl = $pagination->total() == 1 ? null : $pagination->previousPageUrl();
      $nextPageUrl = $pagination->total() == 1 ? null : $pagination->nextPageUrl();
      $previousPageUrl = $previousPageUrl != null ? intval(str_replace('/?page=', '', $previousPageUrl)) : null;
      $nextPageUrl = $nextPageUrl != null ? intval(str_replace('/?page=', '', $nextPageUrl)) : null;
      $data_items = $data;
      //如果$data_items是空数组，则返回null
      if (count($data_items) == 0 || $data_items == null || $data_items == []) {
        $data_items = null;
      }
      $rdata = [
        'is_get' => $data_items != null,
        'data' => $data_items,
        'pagination' => [
          'page' => $pagination->total() == 1 ? 1 : $pagination->currentPage(), //当前页码
          'per_page' => $pagination->total() == 1 ? 1 : $pagination->perPage(), //每页显示的数量
          'total' => $pagination->total(), //总共有多少个项目
          'pages' => $pagination->total() == 1 ? 1 : $pagination->lastPage(), //总共有多少页
          'previous' => $previousPageUrl, //上一页
          'next' => $nextPageUrl, //下一页
        ]
      ];
    }
    return $rdata;
  }
  /**
   * 为404页面
   */
  public static function As404Page()
  {
    $text = '';
    foreach (i18n::getLocaleList() as $key => $value) {
      //$text .= t()->t('Message.Client.PageNotFound.Page404', $value);
      $text .= '<span>' . i18n::t('Message.Client.PageNotFound.Page404', $value) . '</span><br>';
    }
    return $text;
  }
  /**
   * 从原始请求中获取数据 仅限POST且Content-Type为application/x-www-form-urlencoded
   * @return array
   */
  public static function GetRequestData()
  {
    $data = file_get_contents('php://input');
    // 解码 URL 参数字符串
    $decodedParams = urldecode($data);
    // 将解码后的字符串转换为数组
    $paramsArray = array();
    parse_str($decodedParams, $paramsArray);
    return $paramsArray;
  }
  /**
   * 从原始请求中获取JSON数据 仅限POST且Content-Type为application/x-www-form-urlencoded
   * @return array||null
   */
  public static function GetRequestJSONData()
  {
    $rawPostData = file_get_contents('php://input');
    $postData = json_decode($rawPostData, true);
    //检查postData是否是数组
    if (is_array($postData)) {
      return $postData;
    } else {
      return null;
    }

    //json_encode($postData, JSON_PRETTY_PRINT)) //转换为正常的json
  }
  public static function GetLanguage($lang)
  {
    $dataFolder = '././public/locale/json/' . $lang . '.json';
    if (file_exists($dataFolder)) {
      return file_get_contents($dataFolder);
    } else {
      return '404 ' . $lang . ' language is null';
    }
  }
  /**
   * 获取Cookie
   * @param string $name Cookie名称
   * @return string
   */
  public static function GetCookie($name = ''): string
  {
    if (isset($_COOKIE[$name])) {
      return $_COOKIE[$name];
    } else {
      return isset(self::GetRequestData()[$name]) ? self::GetRequestData()[$name] : ''; //Option::Get('theme') || Config::GetDefaultTheme();
    }
  }
  /**
   * 获取客户端主题名称
   * @return string
   */
  public static function GetClientThemeName(): string
  {

    // //停止报错
    // error_reporting(0);

    // if(!isset($_COOKIE['theme'])){
    //   //开启报错
    //   error_reporting(E_ALL);
    //   $sql_theme = Option::Get('theme');
    //   return self::GetRequestData()['theme'] || $sql_theme || Config::GetDefaultTheme();
    // }else{
    //   //开启报错
    //   error_reporting(E_ALL);
    //   return $_COOKIE['theme'];
    // }

    // 停止报错（不推荐长期使用）
    error_reporting(0);

    $theme = '';

    // 检查 cookie 是否存在
    if (!isset($_COOKIE['theme'])) {
      // 开启报错
      error_reporting(E_ALL);

      // 检查其他来源的主题
      $sql_theme = Option::Get('theme');
      $request_data = self::GetRequestData();

      // 使用空合并运算符获取主题，确保不会报错
      $theme = $request_data['theme'] ?? $sql_theme ?? Config::GetDefaultTheme();
    } else {
      // 开启报错
      error_reporting(E_ALL);
      $theme = $_COOKIE['theme'];
    }

    return $theme;


    // return $_COOKIE['theme'] || self::GetRequestData()['theme'] || Option::Get('theme') || Config::GetDefaultTheme();
    // return $_COOKIE['theme'];
    //只需要cookie中指定的主题名称,不需要localsotrage中的主题名称
    //因为localsotrage中的主题名称是为了前端在切换主题风格时使用的
    //优先级：cookie用户浏览器cookie存储的 > request用户浏览器请求时网址携带的参数/?theme=xxx > option网站数据库定义的theme > config网站默认的theme
    // return self::GetCookie('theme') || self::GetRequestData()['theme'] || Option::Get('theme') || Config::GetDefaultTheme();
  }
  /**
   * 获取客户端用户token
   * @return string
   */
  public static function GetClientUserToken()
  {
    $user_token = self::GetCookie('user_token') ?? self::GetCookie('user_token') ?? $_COOKIE['user_token'] ?? '';
    if ($user_token == 1) {
      $user_token = $_COOKIE['user_token'];
    }
    return $user_token;
  }
  /**
   * 获取主题列表
   * @return array
   */
  public static function GetThemesInfo()
  {
    $themeList = array();
    $dir = opendir(Config::GetWebThemePath());
    while (($file = readdir($dir)) !== false) {
      if ($file != '.' && $file != '..') {
        //如果是单个文件的话，则跳过
        if (is_file(Config::GetWebThemePath() . $file)) {
          continue;
        }
        $themeFilePath = Config::GetWebThemePath() . $file . '/theme.json';
        if (file_exists($themeFilePath)) {
          $themeInfo = json_decode(file_get_contents($themeFilePath), true);
          //记录当前主题的路径
          $themeInfo['path'] = Config::GetWebThemePath() . $file;

          //记录其主题下的所有文件路径名称和路径，包括所有子目录的文件
          $themeInfo['files'] = array();
          $dir2 = opendir($themeInfo['path']);
          while (($file2 = readdir($dir2)) !== false) {
            if ($file2 != '.' && $file2 != '..') {
              $themeInfo['files'][$file2] = $themeInfo['path'] . '/' . $file2;
            }
          }
          closedir($dir2);

          array_push($themeList, $themeInfo);
        } else {
          continue;
        }
      }
    }
    return $themeList;
    //[{
    //     "name": "MaterialDesignVuetify2",
    //     "version": "1.0.0",
    //     "description": "Material Design for Vuetify2",
    //     "disabled": false,
    //     "path": "././public/themes/MaterialDesignVuetify2"
    // }]
  }
  public static function GetThemeInfo($theme_name)
  {
    $themeFilePath = Config::GetWebThemePath() . $theme_name . '/theme.json';
    $info = '';
    try {
      $info = file_get_contents($themeFilePath);
    } catch (\Exception $e) {
      $info = '';
    }
    //如果为空，则遍历读取theme.json中的name字段确定主题
    if ($info == '') {
      $themeList = self::GetThemesInfo();
      foreach ($themeList as $key => $value) {
        if ($value['name'] == $theme_name) {
          $info = file_get_contents($value['path'] . '/theme.json');
          break;
        }
      }
    }
    return json_decode($info, true);
  }
  /**
   * 获取主题HTML
   * @param string $theme_name 主题名称
   * @param string $file_name 文件名称 默认为index.html
   * @return string HTML
   */
  public static function GetThemeHtml($theme_name, $file_name = 'index.html')
  {
    $index_html = '';
    try {
      $index_html = file_get_contents(Config::GetWebThemePath() . $theme_name . '/' . $file_name);
    } catch (\Exception $e) {
      $index_html = '';
    }
    //如果按路径寻找$index_html等于空，则遍历读取theme.json中的name字段确定主题
    if ($index_html == '') {
      $themeList = self::GetThemesInfo();
      foreach ($themeList as $key => $value) {
        if ($value['name'] == $theme_name) {
          $index_html = file_get_contents($value['path'] . '/' . $file_name);
          break;
        }
      }
    }
    return $index_html;
  }
  /**
   * 获取主题是否支持ie浏览器 最低ie6起步
   */
  public static function GetThemeIsSupportIE($theme_name)
  {
    $themeInfo = self::GetThemeInfo($theme_name);
    if (isset($themeInfo['support_ie'])) {
      return $themeInfo['support_ie'];
    } else {
      return false;
    }
  }
  /**
   * 判断是否为IE浏览器
   * @return bool
   */
  public static function ClientIsIE()
  {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
      return true;
    } else {
      return false;
    }
  }
  /**
   * 服务器时间戳
   */
  public static function ServerTime()
  {
    $timestamp = Carbon::now()->timestamp;
    return $timestamp;
  }
  // /**
  //  * 获取通用搜索字段
  //  * @param string $type 类型
  //  * @return array
  //  */
  // public static function GetCommonSearchField($type = 'user'){
  //   match($type){

  //   }
  // }

  /**
   * 将请求地址和请求数据输出到/log/request.json
   * @param array $request_data 请求数据
   * @return void
   */
  public static function SaveRequest($request_data)
  {
    return; //暂时不记录请求日志
    if (!Config::Dev()) {
      //如果是开发环境，则不记录请求日志
      return;
    }

    //将请求地址和请求数据输出到/log/request.json
    $requestIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestData = file_get_contents('php://input');
    $logData = [
      'ip' => $requestIP,
      'uri' => $requestUri,
      'method' => $requestMethod,
      'data' => $requestData,
      'return' => $request_data,
      'timestamp' => date('Y-m-d H:i:s'),
    ];
    $date = date('Y-m-d');
    //检查是否创建目录
    if (!is_dir('log/' . $date)) {
      mkdir('log/' . $date, 0755, true);
    }
    file_put_contents('log/' . $date . '/request.json', json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
  }
  /**
   * 渲染错误页面
   */
  public static function RenderErrorPage(\Exception $e): string
  {
    $errorDetails = [
      'message' => htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'),
      'code' => $e->getCode(),
      'file' => htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8'),
      'line' => $e->getLine(),
      'trace' => nl2br(htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8'))
    ];

    return <<<HTML
      <!DOCTYPE html>
      <html lang="zh-CN">
      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Material Design Forum - 系统错误 System Error</title>
          <style>
              * { margin: 0; padding: 0; box-sizing: border-box; }
              body { 
                  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                  min-height: 100vh;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  padding: 20px;
              }
              .error-container {
                  background: white;
                  border-radius: 12px;
                  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                  max-width: 800px;
                  width: 100%;
                  overflow: hidden;
              }
              .error-header {
                  background: #f44336;
                  color: white;
                  padding: 20px;
                  text-align: center;
              }
              .error-header h1 {
                  font-size: 24px;
                  margin-bottom: 5px;
              }
              .error-content {
                  padding: 30px;
              }
              .error-section {
                  margin-bottom: 25px;
                  padding: 20px;
                  background: #f8f9fa;
                  border-radius: 8px;
                  border-left: 4px solid #667eea;
              }
              .error-section h3 {
                  color: #495057;
                  margin-bottom: 10px;
                  font-size: 18px;
              }
              .error-details {
                  background: #2d3748;
                  color: #e2e8f0;
                  padding: 15px;
                  border-radius: 6px;
                  font-family: 'Courier New', monospace;
                  font-size: 14px;
                  overflow-x: auto;
                  margin-top: 10px;
              }
              .action-buttons {
                  display: flex;
                  gap: 10px;
                  margin-top: 20px;
                  flex-wrap: wrap;
              }
              .btn {
                  padding: 10px 20px;
                  border: none;
                  border-radius: 6px;
                  text-decoration: none;
                  font-weight: 500;
                  cursor: pointer;
                  transition: all 0.3s ease;
              }
              .btn-primary {
                  background: #667eea;
                  color: white;
              }
              .btn-secondary {
                  background: #6c757d;
                  color: white;
              }
              .btn:hover {
                  transform: translateY(-2px);
                  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
              }
          </style>
      </head>
      <body>
          <div class="error-container">
              <div class="error-header">
                  <h1>🚨 系统错误</h1>
                  <p>Material Design Forum 遇到问题</p>
              </div>
              
              <div class="error-content">
                  <div class="error-section">
                      <h3>错误信息</h3>
                      <p>{$errorDetails['message']}</p>
                  </div>
                  
                  <div class="error-section">
                      <h3>错误详情</h3>
                      <div class="error-details">
                          错误代码: {$errorDetails['code']}<br>
                          文件: {$errorDetails['file']}<br>
                          行号: {$errorDetails['line']}<br>
                      </div>
                  </div>
                  
                  <div class="error-section">
                      <h3>堆栈追踪</h3>
                      <div class="error-details">
                          {$errorDetails['trace']}
                      </div>
                  </div>
                  
                  <div class="action-buttons">
                      <button class="btn btn-primary" onclick="location.reload()">🔄 刷新页面</button>
                      <button class="btn btn-secondary" onclick="history.back()">⬅️ 返回上页</button>
                      <a href="/" class="btn btn-primary">🏠 返回首页</a>
                  </div>
              </div>
          </div>
      </body>
      </html>
      HTML;
  }
}
