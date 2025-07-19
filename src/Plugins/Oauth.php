<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Plugins;

use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Option as OptionController;

class Oauth
{
  /**
   * 执行完整的GitHub OAuth认证流程
   * @param string $oauthName OAuth名称(如 "github","google","microsoft"等)
  //  * @param string $clientID GitHub应用的Client ID
  //  * @param string $clientSecret GitHub应用的Client Secret
   * @param string $requestToken 前端传递过来的授权code
   * @param string $redirectUri 回调URI(可选，如果不提供则使用GitHub应用设置的默认URI)
   * @return array 包含用户信息和access_token的关联数组,其中mdf_user_token为当前用户的token
   * @throws Exception 如果流程中任何步骤失败则抛出异常
   */
  public static function ExecuteOAuthFlow(
    string $oauthName,
    // string $clientID,
    // string $clientSecret,
    string $requestToken,
    string $redirectUri = ''
  ) {
    try {
      $tokenResponse = null;
      $accessToken = null;
      $userInfo = null;

      if (
        // empty($clientID) || empty($clientSecret) || 
        empty($requestToken)
      ) {
        throw new \Exception("OAuth参数(clientID, clientSecret, requestToken)不能为空");
      }

      switch ($oauthName) {
        case 'github':
          // GitHub OAuth流程
          $clientID = OptionController::where('name', 'github_client_id')->value('value');
          $clientSecret = OptionController::where('name', 'github_client_secret')->value('value');
          // 1. 获取GitHub OAuth Access Token
          $tokenResponse = self::GetGitHubAccessToken($clientID, $clientSecret, $requestToken);
          $accessToken = $tokenResponse['access_token'];
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
          // Microsoft OAuth流程
          $clientID = OptionController::where('name', 'microsoft_client_id')->value('value');
          $clientSecret = OptionController::where('name', 'microsoft_client_secret')->value('value');
          // 1. 获取Microsoft OAuth Access Token
          $tokenResponse = self::GetMicrosoftAccessToken($clientID, $clientSecret, $requestToken);
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

      // UserController::OauthLoginOrRegister(
      //   $oauthName,
      //   $userInfo['id'] ?? '',
      //   $userInfo['login'] ?? $userInfo['displayName'] ?? '',
      //   $userInfo['email'] ?? ''
      // );

      $client_user_token = $_COOKIE['user_token'] ?? null;

      $res = [];
      switch ($oauthName) {
        case 'github':
          $res = UserController::OauthLoginOrRegister(
            $oauthName,
            $data['user']['id'] ?? '',
            $data['user']['login'] ?? $data['user']['displayName'] ?? '',
            $data['user']['email'] ?? '',
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
   * @param string $requestToken 前端传递过来的授权 code
   * @return array GitHub 返回的 JSON 响应(已解析为关联数组)
   * @throws Exception 如果请求失败或解析出错则抛出异常
   */
  public static function GetGitHubAccessToken(string $clientID, string $clientSecret, string $requestToken): array
  {
    // 构造请求 URL
    $url = 'https://github.com/login/oauth/access_token?' .
      'client_id=' . urlencode($clientID) .
      '&client_secret=' . urlencode($clientSecret) .
      '&scope=' . urlencode('user') . // 可选的 scope，根据需要调整
      '&code=' . urlencode($requestToken);

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
    $result = self::CallGitHubApi("/user/emails", $accessToken);

    // 检查是否是错误响应
    if (isset($result['error'])) {
      throw new \Exception("获取用户邮箱失败: " . $result['error']);
    }

    // 确保返回的是数组
    if (!is_array($result)) {
      throw new \Exception("GitHub API 返回了无效的数据格式");
    }

    // 过滤出已验证的邮箱
    return array_filter($result, function ($email) {
      // 确保$email是数组且包含'verified'键
      return is_array($email) && isset($email['verified']) && $email['verified'];
    });
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
   * @param string $requestToken 前端传递过来的授权 code
  //  * @param string $redirectUri 回调URI
   * @return array Microsoft 返回的 JSON 响应(已解析为关联数组)
   * @throws Exception 如果请求失败或解析出错则抛出异常
   */
  public static function GetMicrosoftAccessToken(
    string $clientID,
    string $clientSecret,
    string $requestToken,
    // string $redirectUri
  ): array {
    // 构造请求 URL
    $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

    //如果路由包含localhost
    $redirect_uri = '';
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
      $redirect_uri = 'http://localhost:83/api/oauth/redirect/microsoft';
    } else {
      //获取当前域名
      $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/api/oauth/redirect/microsoft';
    }

    // 构造请求数据
    $data = [
      'client_id' => $clientID,
      'client_secret' => $clientSecret,
      'code' => $requestToken,
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
}
