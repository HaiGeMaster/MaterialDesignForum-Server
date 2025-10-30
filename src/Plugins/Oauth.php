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

use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Option as OptionController;

class Oauth
{
  private static $sso_client_main_url = '';
  /**
   * 执行完整的GitHub OAuth认证流程
   * @param string $oauthName OAuth名称(如 "github","google","microsoft"等)
  //  * @param string $clientID GitHub应用的Client ID
  //  * @param string $clientSecret GitHub应用的Client Secret
   * @param string $requestCode 前端传递过来的授权code
   * @param string $redirectUri 回调URI(可选，如果不提供则使用GitHub应用设置的默认URI)
   * @return array 包含用户信息和access_token的关联数组,其中mdf_user_token为当前用户的token
   * @throws Exception 如果流程中任何步骤失败则抛出异常
   */
  public static function ExecuteOAuthFlow(
    string $oauthName,
    // string $clientID,
    // string $clientSecret,
    string $requestCode,
    string $redirectUri = ''
  ) {
    try {
      $tokenResponse = null;
      $accessToken = null;
      $userInfo = null;

      if (
        // empty($clientID) || empty($clientSecret) || 
        empty($requestCode)
      ) {
        throw new \Exception("OAuth参数(clientID, clientSecret, requestCode)不能为空");
      }

      switch ($oauthName) {
        case 'github':
          // GitHub OAuth流程
          $clientID = OptionController::where('name', 'github_client_id')->value('value');
          $clientSecret = OptionController::where('name', 'github_client_secret')->value('value');
          // 1. 获取GitHub OAuth Access Token
          $tokenResponse = self::GetGitHubAccessToken($clientID, $clientSecret, $requestCode);
          $accessToken = $tokenResponse['access_token'];

          // echo json_encode($tokenResponse);
          // exit;
          // json_encode($tokenResponse)此时的数据:
          // {
          //   "access_token":"",
          //   "token_type":"bearer",
          //   "scope":"user"
          // }

          // 2. 获取用户基本信息
          $userInfo = self::GetGitHubUserInfo($accessToken);
          // 3. 可以在这里获取更多用户信息(可选)
          // $userEmails = self::GetGitHubUserEmails($accessToken);

          // Github 返回的响应示例
          //{
          //   "access_token": "",
          //   "token_type": "bearer",
          //   "scope": "",
          //   "user": {
          //       "login": "HaiGeMaster",
          //       "id": 54298304,
          //       "node_id": "MDQ6VXNlcjU0Mjk4MzA0",
          //       "avatar_url": "https:\/\/avatars.githubusercontent.com\/u\/54298304?v=4",
          //       "gravatar_id": "",
          //       "url": "https:\/\/api.github.com\/users\/HaiGeMaster",
          //       "html_url": "https:\/\/github.com\/HaiGeMaster",
          //       "followers_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/followers",
          //       "following_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/following{\/other_user}",
          //       "gists_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/gists{\/gist_id}",
          //       "starred_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/starred{\/owner}{\/repo}",
          //       "subscriptions_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/subscriptions",
          //       "organizations_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/orgs",
          //       "repos_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/repos",
          //       "events_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/events{\/privacy}",
          //       "received_events_url": "https:\/\/api.github.com\/users\/HaiGeMaster\/received_events",
          //       "type": "User",
          //       "user_view_type": "public",
          //       "site_admin": false,
          //       "name": "HaiGeMaster",
          //       "company": null,
          //       "blog": "https:\/\/mdf.xbedrock.com",
          //       "location": "中国广东韶关",
          //       "email": null,
          //       "hireable": null,
          //       "bio": "UE5,Vue,PHP,Java,C#,C++,JS,TS,Squirrel",
          //       "twitter_username": null,
          //       "notification_email": null,
          //       "public_repos": 1,
          //       "public_gists": 0,
          //       "followers": 4,
          //       "following": 7,
          //       "created_at": "2019-08-20T02:20:32Z",
          //       "updated_at": "2025-07-16T03:28:36Z"
          //   }
          // }
          break;
        case 'microsoft':
          //流程：使用 应用ID 和 应用秘钥 和 code授权码 先获取 资产token，然后使用资产token获取用户信息
          //格式为{"access_token":"","token_type":"Bearer","scope":"openid profile User.Read email","user":{...}}
          // Microsoft OAuth流程
          $clientID = OptionController::where('name', 'microsoft_client_id')->value('value');
          $clientSecret = OptionController::where('name', 'microsoft_client_secret')->value('value');
          // 1. 获取Microsoft OAuth Access Token
          $tokenResponse = self::GetMicrosoftAccessToken($clientID, $clientSecret, $requestCode);
          
          // echo json_encode($tokenResponse);
          // exit;
          // json_encode($tokenResponse)此时的数据:
          // {
          //   "token_type":"Bearer",
          //   "scope":"openid profile User.Read",
          //   "expires_in":3599,
          //   "ext_expires_in":3599,
          //   "access_token":""
          // }

          $accessToken = $tokenResponse['access_token'];
          // 2. 获取用户基本信息
          $userInfo = self::GetMicrosoftUserInfo($accessToken);

          // Microsoft 返回的响应示例
          // {
          //   "access_token": "",
          //   "token_type": "Bearer",
          //   "scope": "openid profile User.Read email",
          //   "user": {
          //       "@odata.context": "https:\/\/graph.microsoft.com\/v1.0\/$metadata#users\/$entity",
          //       "userPrincipalName": "2652549974@qq.com",
          //       "id": "3ed3e12eeaa95532",
          //       "displayName": "HaiGe Master",
          //       "surname": "Master",
          //       "givenName": "HaiGe",
          //       "preferredLanguage": "zh-CN",
          //       "mail": "2652549974@qq.com",
          //       "mobilePhone": null,
          //       "jobTitle": null,
          //       "officeLocation": null,
          //       "businessPhones": []
          //   }
          // }
          break;
        case 'google':
          // Google OAuth流程
          $clientID = OptionController::where('name', 'google_client_id')->value('value');
          $clientSecret = OptionController::where('name', 'google_client_secret')->value('value');
          // 1. 获取Google OAuth Access Token
          $tokenResponse = self::GetGoogleAccessToken($clientID, $clientSecret, $requestCode);
          $accessToken = $tokenResponse['access_token'];

          echo $tokenResponse;
          exit;
          // 2. 获取用户基本信息
          $userInfo = self::GetGoogleUserInfo($accessToken);
          // 3. 可以在这里获取更多用户信息(可选)
          // $userEmails = self::GetGitHubUserEmails($accessToken);
          break;
        case 'sso':
          self::$sso_client_main_url = OptionController::where('name', 'sso_client_main_url')->value('value');
          if (self::$sso_client_main_url == null) {
            throw new \Exception("SSO客户端主URL未配置");
          }
          // SSO OAuth流程
          $clientID = OptionController::where('name', 'sso_client_id')->value('value');
          $clientSecret = OptionController::where('name', 'sso_client_secret')->value('value');
          // 1. 获取SSO OAuth Access Token
          $tokenResponse = self::GetSSOAccessToken($clientID, $clientSecret, $requestCode);
          $accessToken = $tokenResponse['access_token'];
          // 2. 获取用户基本信息
          $userInfo = self::GetSSOUserInfo($accessToken);
          break;
        default:
          throw new \Exception("不支持的OAuth名称: $oauthName");
      }

      // 返回整合后的用户信息
      // return [
      //   'access_token' => $accessToken,
      //   'token_type' => $tokenResponse['token_type'] ?? 'bearer',
      //   'scope' => $tokenResponse['scope'] ?? '',
      //   'user' => $userInfo,
      //   'mdf_user_token' => $_COOKIE['user_token'] ?? null,
      //   // 'emails' => $userEmails // 如果获取了邮箱信息
      // ];

      $data = [
        'access_token' => $accessToken,
        'token_type' => $tokenResponse['token_type'] ?? 'bearer',
        'scope' => $tokenResponse['scope'] ?? '',
        'user' => $userInfo,
        'mdf_user_token' => $_COOKIE['user_token'] ?? null,
      ];

      $client_user_token = $_COOKIE['user_token'] ?? null;

      $res = [];
      switch ($oauthName) {
        case 'github':
          if ($data['user']['email'] == null) {
            $data['user']['email'] = json_encode(self::GetGitHubUserEmails($data['access_token']));
            $data['user']['email'] = json_decode($data['user']['email'])[0]->email;
          }
          $res = UserController::OauthLoginOrRegister(
            $oauthName,
            $data['user']['id'] ?? '',
            $data['user']['login'] ?? $data['user']['displayName'] ?? '',
            $data['user']['email'],
            $data['user'],
            $client_user_token
          );
          break;
        case 'microsoft':
          $res = UserController::OauthLoginOrRegister(
            $oauthName,
            $data['user']['id'] ?? '',
            $data['user']['displayName'] ?? '',
            $data['user']['mail'] ?? '',
            $data['user'],
            $client_user_token
          );
          break;
        case 'google':
          echo 'Not support google oauth';
          exit;
          // $res = UserController::OauthLoginOrRegister(
          //   $oauthName,
          //   $data['user']['id'] ?? '',
          //   $data['user']['displayName'] ?? '',
          //   $data['user']['email'] ?? '',
          //   $data['user'],
          //   $client_user_token
          // );
          break;
        case 'sso':
          $res = UserController::OauthLoginOrRegister(
            $oauthName,
            $data['user']['id'] ?? '',
            $data['user']['name'] ?? '',
            $data['user']['email'] ?? '',
            $data['user'],
            $client_user_token
          );
          break;
      }
      //设置cookie
      // setcookie('user_token', $res['token'], time() + 3600 * 24 * 30, '/', '', false, true);
      //转到首页
      // header('Location: /');


      //设置cookie，使用js
      header('Content-Type: text/html; charset=utf-8');
      echo '<script>';
      echo 'document.cookie = "user_token=' . $res['token'] . '; path=/; max-age=' . (3600 * 24 * 30) . '";';
      echo 'localStorage.setItem("user_token", "' . $res['token'] . '");';
      echo 'window.location.href = "/";';
      echo '</script>';
      exit;
      // return $res;
    } catch (\Exception $e) {
      // 记录错误日志(实际应用中应该使用日志系统)
      error_log("OAuth流程失败: " . $e->getMessage());
      throw new \Exception("认证失败: " . $e->getMessage());
    }
  }

  /**
   * 获取 GitHub OAuth Access Token
   *
   * @param string $clientID GitHub 应用的 Client ID
   * @param string $clientSecret GitHub 应用的 Client Secret
   * @param string $requestCode 前端传递过来的授权 code
   * @return array GitHub 返回的 JSON 响应(已解析为关联数组)
   * @throws Exception 如果请求失败或解析出错则抛出异常
   */
  public static function GetGitHubAccessToken(string $clientID, string $clientSecret, string $requestCode): array
  {
    // 构造请求 URL
    $url = 'https://github.com/login/oauth/access_token?' .
      'client_id=' . urlencode($clientID) .
      '&client_secret=' . urlencode($clientSecret) .
      // '&scope=' . urlencode('user') . // 可选的 scope，根据需要调整
      '&code=' . urlencode($requestCode);

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应内容
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json' // 设置请求头为 JSON
    ]);

    // SSL 验证设置
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // 启用证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);      // 严格验证主机名
    curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem'); // 指定 CA 证书路径

    // 执行请求
    $response = curl_exec($ch);

    // 检查 cURL 是否有错误
    if (curl_errno($ch)) {
      $errorMsg = 'cURL 错误: ' . curl_error($ch);
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 获取 HTTP 状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
      $errorMsg = "GitHub API 返回非 200 状态码: $httpCode";
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 关闭 cURL
    curl_close($ch);

    // 解析 JSON 响应
    $tokenResponse = json_decode($response, true);

    // 检查 JSON 解析是否成功
    if ($tokenResponse === null) {
      throw new \Exception('JSON 解析失败: ' . json_last_error_msg());
    }

    // 检查是否包含 access_token 字段
    if (!isset($tokenResponse['access_token'])) {
      throw new \Exception('GitHub API 响应中缺少 access_token 字段。$tokenResponse:' . $tokenResponse . '');
    }

    return $tokenResponse;
  }
  /**
   * 获取GitHub用户信息
   *
   * @param string $accessToken GitHub OAuth Access Token
   * @return array 用户信息数组
   * @throws Exception 如果请求失败则抛出异常
   */
  public static function GetGitHubUserInfo(string $accessToken): array
  {
    return self::CallGitHubApi("/user", $accessToken);
  }

  /**
   * 获取GitHub用户邮箱(可选)
   *
   * @param string $accessToken GitHub OAuth Access Token
   * @return array 用户邮箱数组
   * @throws Exception 如果请求失败则抛出异常
   */
  public static function GetGitHubUserEmails(string $accessToken): array
  {
    $result = self::CallGitHubApi("/user/emails?access_token=$accessToken", $accessToken);

    // 检查是否是错误响应
    if (isset($result['error'])) {
      throw new \Exception("获取用户邮箱失败: " . $result['error']);
    }

    // 确保返回的是数组
    if (!is_array($result)) {
      throw new \Exception("GitHub API 返回了无效的数据格式");
    }

    // echo $result;
    // exit;

    // 过滤出已验证的邮箱
    return array_filter($result, function ($email) {
      // 确保$email是数组且包含'verified'键
      return is_array($email) && isset($email['verified']) && $email['verified'];
    });

    // $url = "https://api.github.com/user/emails?access_token=$accessToken";
    // $result = self::CallGitHubApi($url, $accessToken);
  }

  /**
   * 调用 GitHub API(cURL 版)
   * 
   * @param string $endpoint GitHub API 端点(如 "/user")
   * @param string $accessToken GitHub OAuth Access Token
   * @param string $method HTTP 方法(GET/POST/PUT/DELETE，默认 GET)
   * @param array $postData POST 数据(仅用于 POST/PUT 请求)
   * @param int $timeout 超时时间(秒，默认 30)
   * @return array|string API 返回的 JSON 数据(解析为数组)或错误信息
   */
  public static function CallGitHubApi($endpoint, $accessToken, $method = "GET", $postData = [], $timeout = 30)
  {
    $url = "https://api.github.com" . $endpoint;

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置默认请求头
    $headers = [
      "Authorization: Bearer $accessToken",
      "User-Agent: PHP GitHub API Client",
      "Accept: application/vnd.github.v3+json" // GitHub 推荐的 API 版本
    ];

    // 如果是 POST/PUT 请求，添加 Content-Type
    if (in_array(strtoupper($method), ["POST", "PUT"])) {
      $headers[] = "Content-Type: application/json";
    }

    // 设置 cURL 选项
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_RETURNTRANSFER => true, // 返回响应数据而不是直接输出
      CURLOPT_FOLLOWLOCATION => true, // 跟随重定向
      CURLOPT_USERAGENT => "PHP GitHub API Client", // GitHub API 要求 User-Agent
      CURLOPT_TIMEOUT => $timeout, // 超时时间
      CURLOPT_SSL_VERIFYPEER => true, // 验证 SSL 证书(生产环境必须开启)
      CURLOPT_SSL_VERIFYHOST => 2, // 严格验证主机名
      CURLOPT_CAINFO => $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem' // 指定 CA 证书路径(如果需要)
    ]);

    // 如果是 POST/PUT 请求，添加请求体
    if (in_array(strtoupper($method), ["POST", "PUT"]) && !empty($postData)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    }

    // 设置 HTTP 方法
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    // 发送请求
    $response = curl_exec($ch);

    // 检查 cURL 错误
    if ($response === FALSE) {
      $curlError = curl_error($ch);
      $curlErrno = curl_errno($ch);
      curl_close($ch);
      return ["error" => "cURL Error", "details" => $curlError, "errno" => $curlErrno];
    }

    // 获取 HTTP 状态码
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查 GitHub API 错误状态码
    if ($statusCode >= 400) {
      return [
        "error" => "GitHub API Error",
        "status_code" => $statusCode,
        "response" => json_decode($response, true) ?: $response
      ];
    }

    // 解析 JSON 响应
    $data = json_decode($response, true);
    if ($data === NULL && $response !== "") {
      return ["error" => "Failed to decode JSON response", "raw_response" => $response];
    }

    return $data;
  }

  /**
   * 获取 Microsoft OAuth Access Token
   *
   * @param string $clientID Microsoft 应用的 Client ID
   * @param string $clientSecret Microsoft 应用的 Client Secret
   * @param string $requestCode 前端传递过来的授权 code
  //  * @param string $redirectUri 回调URI
   * @return array Microsoft 返回的 JSON 响应(已解析为关联数组)
   * @throws Exception 如果请求失败或解析出错则抛出异常
   */
  public static function GetMicrosoftAccessToken(
    string $clientID,
    string $clientSecret,
    string $requestCode,
    // string $redirectUri
  ): array {
    // 构造请求 URL
    $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

    //如果路由包含localhost
    $redirect_uri = '';
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
      $redirect_uri = 'http://localhost:83/api/oauth/redirect/microsoft';
    } else {
      //获取当前域名
      $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/oauth/redirect/microsoft';
    }

    // 构造请求数据
    $data = [
      'client_id' => $clientID,
      'client_secret' => $clientSecret,
      'code' => $requestCode,
      'redirect_uri' => $redirect_uri, // 确保与 Microsoft 应用设置的回调 URI 一致
      'grant_type' => 'authorization_code'
    ];

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应内容
    curl_setopt($ch, CURLOPT_POST, true); // POST 请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json' // 设置请求头为 JSON
    ]);

    // SSL 验证设置
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // 启用证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);      // 严格验证主机名
    curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem'); // 指定 CA 证书路径

    // 执行请求
    $response = curl_exec($ch);

    // 检查 cURL 是否有错误
    if (curl_errno($ch)) {
      $errorMsg = 'cURL 错误: ' . curl_error($ch);
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 获取 HTTP 状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
      $errorMsg = "Microsoft API 返回非 200 状态码: $httpCode 。响应内容: $response";
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 关闭 cURL
    curl_close($ch);

    // 解析 JSON 响应
    $tokenResponse = json_decode($response, true);

    // 检查 JSON 解析是否成功
    if ($tokenResponse === null) {
      throw new \Exception('JSON 解析失败: ' . json_last_error_msg());
    }

    // 检查是否包含 access_token 字段
    if (!isset($tokenResponse['access_token'])) {
      throw new \Exception('Microsoft API 响应中缺少 access_token 字段。$tokenResponse:' . $tokenResponse . '');
    }

    return $tokenResponse;
  }

  /**
   * 获取Microsoft用户信息
   *
   * @param string $accessToken Microsoft OAuth Access Token
   * @return array 用户信息数组
   * @throws Exception 如果请求失败则抛出异常
   */
  public static function GetMicrosoftUserInfo(string $accessToken): array
  {
    return self::CallMicrosoftApi("/me", $accessToken);
  }

  /**
   * 调用 Microsoft API(cURL 版)
   * 
   * @param string $endpoint Microsoft API 端点(如 "/me")
   * @param string $accessToken Microsoft OAuth Access Token
   * @param string $method HTTP 方法(GET/POST/PUT/DELETE，默认 GET)
   * @param array $postData POST 数据(仅用于 POST/PUT 请求)
   * @param int $timeout 超时时间(秒，默认 30)
   * @return array|string API 返回的 JSON 数据(解析为数组)或错误信息
   */
  public static function CallMicrosoftApi($endpoint, $accessToken, $method = "GET", $postData = [], $timeout = 30)
  {
    $url = "https://graph.microsoft.com/v1.0" . $endpoint;

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置默认请求头
    $headers = [
      "Authorization: Bearer $accessToken",
      "Accept: application/json" // Microsoft 推荐的 API 版本
    ];

    // 如果是 POST/PUT 请求，添加 Content-Type
    if (in_array(strtoupper($method), ["POST", "PUT"])) {
      $headers[] = "Content-Type: application/json";
    }

    // 设置 cURL 选项
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_RETURNTRANSFER => true, // 返回响应数据而不是直接输出
      CURLOPT_FOLLOWLOCATION => true, // 跟随重定向
      CURLOPT_TIMEOUT => $timeout, // 超时时间
      CURLOPT_SSL_VERIFYPEER => true, // 验证 SSL 证书(生产环境必须开启)
      CURLOPT_SSL_VERIFYHOST => 2, // 严格验证主机名
      CURLOPT_CAINFO => $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem' // 指定 CA 证书路径(如果需要)
    ]);

    // 如果是 POST/PUT 请求，添加请求体
    if (in_array(strtoupper($method), ["POST", "PUT"]) && !empty($postData)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    }

    // 设置 HTTP 方法
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    // 发送请求
    $response = curl_exec($ch);

    // 检查 cURL 错误
    if ($response === FALSE) {
      $curlError = curl_error($ch);
      $curlErrno = curl_errno($ch);
      curl_close($ch);
      return ["error" => "cURL Error", "details" => $curlError, "errno" => $curlErrno];
    }

    // 获取 HTTP 状态码
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查 Microsoft API 错误状态码
    if ($statusCode >= 400) {
      return [
        "error" => "Microsoft API Error",
        "status_code" => $statusCode,
        "response" => json_decode($response, true) ?: $response
      ];
    }

    // 解析 JSON 响应
    $data = json_decode($response, true);
    if ($data === NULL && $response !== "") {
      return ["error" => "Failed to decode JSON response", "raw_response" => $response];
    }

    return $data;
  }
  /**
   * 获取 Google OAuth Access Token
   *
   * @param string $clientID Google 应用的 Client ID
   * @param string $clientSecret Google 应用的 Client Secret
   * @param string $requestCode 前端传递过来的授权 code
   * @return array Google 返回的 JSON 响应(已解析为关联数组)
   * @throws Exception 如果请求失败或解析出错则抛出异常
   */
  public static function GetGoogleAccessToken(string $clientID, string $clientSecret, string $requestCode): array
  {
    // 构造请求 URL
    $url = 'https://oauth2.googleapis.com/token';
    // $url = 'https://accounts.google.com/o/oauth2/v2/auth';

    // 动态获取 redirect_uri（与 Microsoft 版本保持一致）
    $redirect_uri = '';
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
      $redirect_uri = 'http://localhost:83/api/oauth/redirect/google';
    } else {
      // 获取当前域名
      $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/oauth/redirect/google';
    }

    // 构造请求数据
    $data = [
      'client_id' => $clientID,
      'client_secret' => $clientSecret,
      'code' => $requestCode,
      'redirect_uri' => $redirect_uri, // 必须与 Google Cloud Console 中注册的一致
      'grant_type' => 'authorization_code'
    ];

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应内容
    curl_setopt($ch, CURLOPT_POST, true); // POST 请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json' // 设置请求头为 JSON
    ]);

    
    //设置超时时间
    // curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 超时时间设置为 30 秒

    // SSL 验证设置
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // 启用证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);      // 严格验证主机名
    curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem'); // 指定 CA 证书路径

    // 执行请求
    $response = curl_exec($ch);

    // 检查 cURL 是否有错误
    if (curl_errno($ch)) {
      $errorMsg = 'cURL 错误: ' . curl_error($ch);
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 获取 HTTP 状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
      $errorMsg = "Google API 返回非 200 状态码: $httpCode 。响应内容: $response";
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 关闭 cURL
    curl_close($ch);

    // 解析 JSON 响应
    $tokenResponse = json_decode($response, true);

    // 检查 JSON 解析是否成功
    if ($tokenResponse === null) {
      throw new \Exception('JSON 解析失败: ' . json_last_error_msg());
    }

    // 检查是否包含 access_token 字段
    if (!isset($tokenResponse['access_token'])) {
      throw new \Exception('Google API 响应中缺少 access_token 字段。$tokenResponse:' . $tokenResponse . '');
    }

    return $tokenResponse;
  }
  /**
   * 获取 Google 用户信息
   *
   * @param string $accessToken Google OAuth Access Token
   * @return array 用户信息数组
   * @throws Exception 如果请求失败则抛出异常
   */
  public static function GetGoogleUserInfo(string $accessToken): array
  {
    return self::CallGoogleApi("/oauth2/v3/userinfo", $accessToken);
  }
  /**
   * 调用 Google API(cURL 版)
   * 
   * @param string $endpoint Google API 端点(如 "/oauth2/v3/userinfo")
   * @param string $accessToken Google OAuth Access Token
   * @param string $method HTTP 方法(GET/POST/PUT/DELETE，默认 GET)
   * @param array $postData POST 数据(仅用于 POST/PUT 请求)
   * @param int $timeout 超时时间(秒，默认 30)
   * @return array|string API 返回的 JSON 数据(解析为数组)或错误信息
   */
  public static function CallGoogleApi($endpoint, $accessToken, $method = "GET", $postData = [], $timeout = 30)
  {
    $url = "https://www.googleapis.com" . $endpoint;

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置默认请求头
    $headers = [
      "Authorization: Bearer $accessToken",
      "Accept: application/json" // Google 推荐的 API 版本
    ];

    // 如果是 POST/PUT 请求，添加 Content-Type
    if (in_array(strtoupper($method), ["POST", "PUT"])) {
      $headers[] = "Content-Type: application/json";
    }

    // 设置 cURL 选项
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_RETURNTRANSFER => true, // 返回响应数据而不是直接输出
      CURLOPT_FOLLOWLOCATION => true, // 跟随重定向
      CURLOPT_TIMEOUT => $timeout, // 超时时间
      CURLOPT_SSL_VERIFYPEER => true, // 验证 SSL 证书(生产环境必须开启)
      CURLOPT_SSL_VERIFYHOST => 2, // 严格验证主机名
      CURLOPT_CAINFO => $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem' // 指定 CA 证书路径(如果需要)
    ]);

    // 如果是 POST/PUT 请求，添加请求体
    if (in_array(strtoupper($method), ["POST", "PUT"]) && !empty($postData)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    }

    // 设置 HTTP 方法
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    // 发送请求
    $response = curl_exec($ch);

    // 检查 cURL 错误
    if ($response === FALSE) {
      $curlError = curl_error($ch);
      $curlErrno = curl_errno($ch);
      curl_close($ch);
      return ["error" => "cURL Error", "details" => $curlError, "errno" => $curlErrno];
    }

    // 获取 HTTP 状态码
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查 Google API 错误状态码
    if ($statusCode >= 400) {
      return [
        "error" => "Google API Error",
        "status_code" => $statusCode,
        "response" => json_decode($response, true) ?: $response
      ];
    }

    // 解析 JSON 响应
    $data = json_decode($response, true);
    if ($data === NULL && $response !== "") {
      return ["error" => "Failed to decode JSON response", "raw_response" => $response];
    }

    return $data;
  }
  /**
   * 获取 SSO 访问令牌
   *
   * @param string $clientId 客户端ID
   * @param string $clientSecret 客户端密钥
   * @param string $requestCode 请求码
   * @return array|string 访问令牌数组或错误信息 至少返回[access_token, token_type, scope]
   */
  public static function GetSSOAccessToken($clientId, $clientSecret, $requestCode)
  {

    $url = self::$sso_client_main_url . '/api/sso/token';

    $data = [
      'client_id' => $clientId,
      'client_secret' => $clientSecret,
      'code' => $requestCode,
      'grant_type' => 'authorization_code',
      'scope' => 'openid profile email'
    ];
    
    // 初始化 cURL
    $ch = curl_init($url);
    
    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应内容
    curl_setopt($ch, CURLOPT_POST, true); // POST 请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json' // 设置请求头为 JSON
    ]);

    // SSL 验证设置
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // 启用证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);      // 严格验证主机名
    curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem'); // 指定 CA 证书路径

    // 执行请求
    $response = curl_exec($ch);

    // 检查 cURL 是否有错误
    if (curl_errno($ch)) {
      $errorMsg = 'SSO cURL 错误: ' . curl_error($ch);
      curl_close($ch);
      throw new \Exception($errorMsg);
    }

    // 获取 HTTP 状态码
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
      // print_r($response);
      $errorMsg = "SSO API 返回非 200 状态码: $httpCode";
      curl_close($ch);
      throw new \Exception($errorMsg);
    }
    
     // 关闭 cURL
    curl_close($ch);
    
    // 解析 JSON 响应
    $tokenResponse = json_decode($response, true);

    // 检查 JSON 解析是否成功
    if ($tokenResponse === null) {
      throw new \Exception('JSON 解析失败: ' . json_last_error_msg());
    }

    // 检查是否包含 access_token、token_type、scope 字段
    if (!isset($tokenResponse['access_token']) || !isset($tokenResponse['token_type']) || !isset($tokenResponse['scope'])) {
      throw new \Exception('SSO 响应中缺少 access_token、token_type、scope 字段');
    }

    return $tokenResponse;
  }
  /**
   * 获取 SSO 用户信息
   *
   * @param string $accessToken SSO OAuth Access Token
   * @return array 用户信息数组 至少返回[access_token,token_type,scope,user{}]
   * @throws Exception 如果请求失败则抛出异常
   */
  public static function GetSSOUserInfo(string $accessToken): array
  {
    $data = self::CallSSOApi(self::$sso_client_main_url . "/api/sso/user", $accessToken, "POST");
    // echo json_encode($data);
    // exit;
    // 检查 JSON 解析是否成功
    // if ($data === null) {
    //   throw new \Exception('JSON 解析失败: ' . json_last_error_msg());
    // }
    // 检查是否包含 id,name,email 字段
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['email'])) {
      throw new \Exception('SSO 响应中缺少 id、name、email 字段');
    }
    return $data;
  }
  /**
   * 调用 SSO API(cURL 版)
   *
   * @param string $endpoint SSO API 端点(如 "http://localhost:83/api/sso/user")
   * @param string $accessToken SSO OAuth Access Token
   * @param string $method HTTP 方法(GET/POST/PUT/DELETE，默认 GET)
   * @param array $postData POST 数据(仅用于 POST/PUT 请求)
   * @param int $timeout 超时时间(秒，默认 30)
   * @return array|string API 返回的 JSON 数据(解析为数组)或错误信息
   */
  public static function CallSSOApi($endpoint, $accessToken, $method = "GET", $postData = [], $timeout = 30)
  {
    $url = $endpoint;

    // 初始化 cURL
    $ch = curl_init($url);

    // 设置默认请求头
    // $headers = [
    //   "Authorization: Bearer $accessToken",
    //   "Accept: application/json" // SSO 推荐的 API 版本
    // ];

    // 构造请求数据
    $data = [
      "access_token" => $accessToken
    ];

    // 如果是 POST/PUT 请求，添加 Content-Type
    // if (in_array(strtoupper($method), ["POST", "PUT"])) {
    //   $headers[] = "Content-Type: application/json";
    // }

    // // 设置 cURL 选项
    // curl_setopt_array($ch, [
    //   CURLOPT_HTTPHEADER => $headers,
    //   CURLOPT_RETURNTRANSFER => true, // 返回响应数据而不是直接输出
    //   CURLOPT_FOLLOWLOCATION => true, // 跟随重定向
    //   CURLOPT_TIMEOUT => $timeout, // 超时时间
    //   CURLOPT_SSL_VERIFYPEER => true, // 验证 SSL 证书(生产环境必须开启)
    //   CURLOPT_SSL_VERIFYHOST => 2, // 严格验证主机名
    //   CURLOPT_CAINFO => $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem' // 指定 CA 证书路径(如果需要)
    // ]);
    
    // curl_setopt($ch, CURLOPT_POST, true); // POST 请求
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    // // 如果是 POST/PUT 请求，添加请求体
    // if (in_array(strtoupper($method), ["POST", "PUT"]) && !empty($postData)) {
    //   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    // }

    // 设置 HTTP 方法
    // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));


    //改成post中传递参数
    // 设置 cURL 选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应内容
    curl_setopt($ch, CURLOPT_POST, true); // POST 请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json' // 设置请求头为 JSON
    ]);

    // SSL 验证设置
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // 启用证书验证
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);      // 严格验证主机名
    curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'] . '/assets/cacert-2025-07-15.pem'); // 指定 CA 证书路径


    // 发送请求
    $response = curl_exec($ch);

    // 检查 cURL 错误
    if ($response === FALSE) {
      $curlError = curl_error($ch);
      $curlErrno = curl_errno($ch);
      curl_close($ch);
      return ["error" => "cURL Error", "details" => $curlError, "errno" => $curlErrno];
    }

    // 获取 HTTP 状态码
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查 SSO API 错误状态码
    if ($statusCode >= 400) {
      return [
        "error" => "SSO API Error",
        "status_code" => $statusCode,
        "response" => json_decode($response, true) ?: $response
      ];
    }

    // 解析 JSON 响应
    $data = json_decode($response, true); 
    if ($data === NULL && $response !== "") {
      return ["error" => "Failed to decode JSON response", "raw_response" => $response];
    } 

    return $data;
  }
}
