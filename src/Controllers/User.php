<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use itbdw\Ip\IpLocation;

use MaterialDesignForum\Controllers\Oauth as OauthController;
use MaterialDesignForum\Controllers\Cache as CacheController;
use MaterialDesignForum\Controllers\Image as ImageController;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\Follow as FollowController;
use MaterialDesignForum\Controllers\Answer as AnswerController;
use MaterialDesignForum\Controllers\Article as ArticleController;
use MaterialDesignForum\Controllers\Question as QuestionController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;

use MaterialDesignForum\Models\User as UserModel;
use MaterialDesignForum\Models\MailCaptcha as MailCaptchaModel;
use MaterialDesignForum\Models\ImageCaptcha as ImageCaptchaModel;

use MaterialDesignForum\Config\Config;
use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Plugins\Share;

class User extends UserModel
{
  /**
   * 通过第三方平台登录或注册用户
   * @param string $oauthName 第三方平台标识符 如 github, microsoft 等
   * @param string $oauthUserId 第三方平台用户ID
   * @param string $oauthUserName 第三方平台用户名
   * @param string $oauthUserMail 第三方平台用户邮箱 用来查找数据库中是否有对应的用户
   * @param string $oauthSourceResponse 第三方平台返回的用户信息
   * @param string $user_token 对应的用户token。可以为空
   * @return OauthModel|null 返回添加或更新后的Oauth模型实例或null
   */
  public static function OauthLoginOrRegister($oauthName, $oauthUserId, $oauthUserName, $oauthUserMail, $oauthSourceResponse, $user_token):array
  {
    $is_login = false;
    $token = '';
    //首先根据oauthUserId和oauthName查找是否存在对应的Oauth记录
    $oauthUser = OauthController::GetOauthUser($oauthName, $oauthUserId);
    if ($oauthUser) {//如果有绑定第三方平台记录，则直接查找对应用户去让其登录账号
      $user_id = $oauthUser->user_id;
      $local_user = self::where('user_id', '=', $user_id)
        ->where('disable_time', '=', 0)
        ->first();
        if($local_user){
          //顺便更新oauth记录的用户名
          $update_oauth = OauthController::where('oauth_id', '=', $oauthUser->oauth_id)
            ->update([
              'oauth_user_name' => $oauthUserName,
            ]);
          $is_login = true;
          $token = TokenController::SpawnUserToken($local_user);
        }
    }else if ($user_token) { //如果没有绑定第三方平台记录，且传入用户token，则根据token查找用户
      $user_id = TokenController::GetUserId($user_token);
      if ($user_id) {
        $local_user = self::where('user_id', '=', $user_id)
          ->where('disable_time', '=', 0)
          ->first();
        if ($local_user) {//为其绑定Oauth记录
          $oauthUser = OauthController::AddOauthUser($oauthName, $oauthUserId, $oauthUserName, $oauthUserMail, $oauthSourceResponse, $local_user->user_id);
          if ($oauthUser) {
            $is_login = true;
            $token = TokenController::SpawnUserToken($local_user);
          }
        }
      }
    }else{//如果没有绑定第三方平台记录，则根据 oauthUserMail 查找是否有对应的用户
      $local_user = self::where('email', '=', $oauthUserMail)
        ->where('disable_time', '=', 0)
        ->first();
      if ($local_user) { //如果有对应的用户，则添加或更新Oauth记录
        $oauthUser = OauthController::AddOauthUser($oauthName, $oauthUserId, $oauthUserName, $oauthUserMail, $oauthSourceResponse, $local_user->user_id);
        if ($oauthUser) {// 如果添加或更新成功，则登录用户
          $is_login = true;
          $token = TokenController::SpawnUserToken($local_user);
        }
      } else { //如果没有对应的用户，则注册新用户
        $new_user = self::create([
          'email' => $oauthUserMail,
          'username' => $oauthUserName,
          'password' => self::HandlePassword(Share::ServerTime()), //使用时间戳作为默认密码
          'create_ip' => self::GetClientIP(),
          'create_location' => self::GetClientLocation(),
          'last_login_time' => Share::ServerTime(),
          'last_login_ip' => self::GetClientIP(),
          'last_login_location' => self::GetClientLocation(),
          'location' => self::GetClientLocation(),
          'language' => Config::GetWebDefaultLanguage(),
          'create_time' => Share::ServerTime(),
          'update_time' => Share::ServerTime(),
          'avatar' => self::CreateDefaultAvatar($oauthUserName),
          'cover' => self::CreateDefaultCover()
        ]);
        if ($new_user) {//如果新用户注册成功，则添加或更新Oauth记录
          $new_user_model = self::where('email', '=', $oauthUserMail)->first();
          if ($new_user_model) {
            $oauthUser = OauthController::AddOauthUser($oauthName, $oauthUserId, $oauthUserName, $oauthUserMail, $oauthSourceResponse, $new_user_model->user_id);
            if ($oauthUser) {
              $is_login = true;
              $token = TokenController::SpawnUserToken($new_user_model);
            }
          }
        }
      }
    }
    return [
      'is_login' => $is_login,
      'token' => $token,
    ];
  }
  /**
   * 请求注册用户
   * @param string $email 邮箱
   * @param string $password 密码
   * @param string $email_captcha 邮箱验证码
   * @param string $username 用户名
   * @return json {is_register:是否注册成功}
   */
  public static function AddUser($email, $password, $email_captcha, $username = "", $language = "")
  {
    $v = false;
    $client_email = base64_decode($email);
    $client_password = self::HandlePassword(base64_decode($password)); //base64_decode($password);
    $client_email_captcha = md5(base64_decode($email_captcha));
    //$client_username = base64_decode($username);
    // $client_username = $username == "" ? "User" . Share::ServerTime() : $username;

    //不使用base64的原因是为了防止用户输入的用户名中包含base64编码的字符，导致解码失败
    // $client_username = $username == "" ? "User" . Share::ServerTime() : base64_decode($username);
    $client_username = $username == "" ? "User" . Share::ServerTime() : $username;
    if ($client_username == "") {
      $client_username = $client_email;
    }
    $client_user = self::where('email', '=', $client_email)->first(); // == null;
    if ($client_user == null) {
      $client_user = self::where('username', '=', $client_username)->first(); // == null;
      if ($client_user == null) {
        if (CacheController::IsVaildCaptcha($client_email_captcha)) {
          $user = new self();
          if (self::count() == 0) {
            $user->user_group_id = 1;
          } else {
            $user->user_group_id = 2;
          }
          $user->email = $client_email;
          $user->password = $client_password;
          $user->username = $client_username;
          $user->create_ip = self::GetClientIP();
          $user->create_location =  self::GetClientLocation();
          $user->last_login_time = Share::ServerTime();
          $user->last_login_ip = self::GetClientIP();
          $user->last_login_location = self::GetClientLocation();
          $user->location = self::GetClientLocation();
          $user->language = $language || Config::GetWebDefaultLanguage();
          $user->create_time = Share::ServerTime();
          $user->update_time = Share::ServerTime();
          //获取网络时间

          $user->avatar = self::CreateDefaultAvatar($client_username); //可能会导致注册失败 vue开发时
          $user->cover = self::CreateDefaultCover();
          $v = $user->save();
          if ($v) {
            $add = UserGroupController::AddUserGroupUserCount($user->user_group_id);
            while (!$add) {
              $add = UserGroupController::AddUserGroupUserCount($user->user_group_id);
            }
            CacheController::DeleteCaptcha($client_email_captcha);
          }
        }
      }
    }
    $data = array(
      'is_add' => $v,
      // 'user' => $v ? $user : null,//安全问题不得返回用户信息
    );
    return $data;
  }
  /**
   * 修改用户信息
   * @param string $email 邮箱
   * @param string $username 用户名
   * @param string $user_group_id 用户组id
   * @param string $headline 个人简介
   * @param string $blog 个人主页
   * @param string $company 所属学校或企业
   * @param string $location 所在地
   * @param string $bio 个人介绍
   * @param string $user_token token 修改者用户token字符串
   * @param string $edit_target_user_id 要编辑的用户id
   * @return json {is_edit:是否更新成功,user:新的用户信息}
   */
  public static function EditInfo($email, $username, $user_group_id, $headline, $blog, $company, $location, $bio, $user_token, $edit_target_user_id)
  {
    $is_edit = false;
    // $user = null;

    $user_id = TokenController::GetUserId($user_token);
    $user_data = UserModel::find($edit_target_user_id);
    try {
      if ($user_data != null) {
        if (
          ($user_data->user_id == $user_id &&
            UserGroupController::Ability($user_token, 'ability_edit_own_info')) ||
          (UserGroupController::IsAdmin($user_token) && UserGroupController::Ability($user_token, 'ability_admin_manage_user'))
          // UserGroupController::IsAdmin($user_token)
        ) {
          $user_data->username = $username;
          $user_data->email = $email;

          if (UserGroupController::IsAdmin($user_token)) { //确定是否是管理员再修改用户组
            UserGroupController::MoveUserGroups(
              $user_group_id,
              [$edit_target_user_id]
            ); //移动用户组
          }

          $user_data->headline = $headline;
          $user_data->blog = $blog;
          $user_data->company = $company;
          $user_data->location = $location;
          $user_data->bio = $bio;
          $user_data->update_time = Share::ServerTime();
          $is_edit = $user_data->save();
          // $user = $user_data;
        }
      }
    } catch (\Exception $e) {
      return [
        'is_edit' => $is_edit,
        'user' => self::GetUser($edit_target_user_id, $user_token)['user'],
        'message' => $e->getMessage(),
      ];
    }
    return [
      'is_edit' => $is_edit,
      //'user' => $user,
      'user' => self::GetUser($edit_target_user_id, $user_token)['user'],
    ];
  }
  public static function CreateDefaultAvatar($name, $user_id = 'cache')
  {
    return ImageController::CreateUserDefaultAvatar($name, $user_id);
  }
  public static function CreateDefaultCover()
  {
    return ImageController::CreateUserDefaultCover();
  }
  /**
   * 获取客户端IP地址
   */
  public static function GetClientIP()
  {
    $client_ip = '0.0.0.0';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $client_ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
      $client_ip = $_SERVER['REMOTE_ADDR'];
    }
    return $client_ip;
  }
  /**
   * 获取客户端IP地址的地理位置
   * // 返回结果
   *  array (size=4)
   *    0 => string '中国' (length=6)
   *    1 => string '河南' (length=6)
   *    2 => string '郑州' (length=6)
   *    3 => string '' (length=0)
   *    4 => string '410100' (length=6)
   */
  public static function GetClientLocation()
  {
    $ip = self::GetClientIP();
    $Location = IpLocation::getLocation($ip);
    return $Location['country'] . ' ' . $Location['province'] . ' ' . $Location['city'] . ' ' . $Location['county'] . ' ' . $Location['isp'];
  }
  /**
   * 请求发送邮件验证码
   * @param string $email 邮箱
   * @param string $lang 语言 如zh_CN 必须来自客户端界面支持的语言 可选的 i18n将会自动检测并设置。
   * @return json {send_mail:发送的邮箱,is_send:是否发送成功,email_code:邮箱验证码,locale:语言}
   */
  public static function GetEmailCaptcha($email, $lang = "")
  {
    $client_email = base64_decode($email);
    $client_lang = base64_decode($lang);
    $v = false;
    $config = Config::getConfig();
    $MailCaptcha = new MailCaptchaModel();
    $Captcha = $MailCaptcha->CreateMailCode();
    if ($client_lang != '') {
      i18n::i18n()->setLocale($client_lang);
    }
    $v = MailCaptchaModel::SendMail(
      $client_email,
      $config['smtp_send_name'] . ':' . i18n::t('Message.Components.Account.Code'),
      $Captcha['code']
    );
    CacheController::create([
      'name' => $Captcha['md5code'],
      'value' => $Captcha['code'],
      'create_time' => Share::ServerTime(),
      'life_time' => Share::ServerTime() + (60 * 5)
    ]);
    $data = array(
      'is_send' => $v,
    );
    // if (Config::Dev()) {
    //   $data['dev_client_send_mail'] = $client_email;
    //   $data['dev_client_email_code'] = $Captcha['md5code'];
    //   $data['dev_client_locale'] = $client_lang;
    // }
    return $data;
  }
  /**
   * 请求图片验证码
   * @return img 图片验证码
   */
  public static function GetImageCaptcha($time = 0)
  {
    $ImageCaptcha = new ImageCaptchaModel();
    $Captcha = $ImageCaptcha->CreateImgAndCode();
    CacheController::create([
      'name' => $Captcha['md5code'],
      'value' => $Captcha['code'],
      'create_time' => Share::ServerTime(),
      'life_time' => Share::ServerTime() + (60 * 5)
    ]);
    return $ImageCaptcha->OutputImg();
  }
  /**
   * 请求登录验证
   * @param string $email 邮箱
   * @param string $password 密码
   * @param string $image_capthca 验证码
   * @return json {is_login:是否登录成功,token:token字符串}
   */
  public static function Login($username_or_email, $password, $image_capthca = "")
  {
    try {
      // 不使用base64的原因是为了防止用户输入的用户名中包含base64编码的字符，导致解码失败
      // $username_or_email = base64_decode($username_or_email);
      $password = self::HandlePassword(base64_decode($password));
      $image_capthca = $image_capthca ? md5(base64_decode($image_capthca)) : '';

      $user = self::where('email', '=', $username_or_email)
        ->where('disable_time', '=', 0)->first();
      if ($user == null) {
        $user = self::where('username', '=', $username_or_email)
          ->where('disable_time', '=', 0)->first();
      }


      // $mail_user = self::where('email', '=', $username_or_email)
      // ->where('disable_time', '=', 0)->first();
      // $name_user = self::where('username', '=', $username_or_email)
      // ->where('disable_time', '=', 0)->first();
      // return [
      //   'is_login' => false,
      //   'user' => $user,
      //   'mail_user' => $mail_user,
      //   'name_user' => $name_user,
      // ];


      $token = '';
      $is_login = false;
      if ($user != null) {
        // return 1;
        if (
          ($user->email == $username_or_email || $user->username == $username_or_email) &&
          $user->password == $password &&
          ($image_capthca == '' || CacheController::IsVaildCaptcha($image_capthca))
        ) {
          // return 2;
          $local_user = $user;
          $token = TokenController::SpawnUserToken($local_user);
          $update_user = self::where('user_id', '=', $user->user_id)->update([
            'last_login_ip' => self::GetClientIP(),
            'last_login_location' => self::GetClientLocation(),
            'last_login_time' => Share::ServerTime(),
          ]);
          if ($token != '' && $update_user) {
            // return 3;
            if ($image_capthca != '') {
              CacheController::DeleteCaptcha($image_capthca);
            } //删除验证码
            $is_login = true;
          }
        }
      }
      return [
        'is_login' => $is_login,
        'token' => $token,
      ];
    } catch (\Exception $e) {
      return [
        'is_login' => false,
        'token' => '',
        'message' => $e->getMessage(),
      ];
    }
  }
  /**
   * 请求修改用户密码
   * @param string $email 邮箱
   * @param string $email_captcha 邮箱验证码
   * @param string $password 密码
   * @return json {is_reset:是否重置成功}
   */
  public static function Reset($email, $password, $email_captcha)
  {
    $client_email = base64_decode($email);
    $client_password = self::HandlePassword(base64_decode($password)); //base64_decode($password);
    $client_email_captcha = md5(base64_decode($email_captcha));
    $v = false;
    $client_cache = CacheController::IsVaildCaptcha($client_email_captcha);
    //然后检查邮箱是否存在
    $Update_user = self::where('email', '=', $client_email)->update([
      'password' => $client_password,
    ]);
    if ($client_cache && $Update_user == 1) {
      $v = true;
    }
    $data = array(
      'is_reset' => $v,
    );
    // if (Config::Dev()) {
    //   $data['dev_client_email'] = $client_email;
    //   $data['dev_client_password'] = $client_password;
    //   $data['dev_client_email_captcha'] = $client_email_captcha;
    // }
    return $data;
  }
  /**
   * 请求用户信息
   * @param string $token token字符串
   * @return json {is_login:是否登录成功,user:用户信息}
   */
  public static function Auto_Login($token)
  {
    $is_login = false;
    $user_id = TokenController::GetUserId($token);
    $user = null;
    if ($user_id) {
      $user = self::where('user_id', '=', $user_id)
        ->where('disable_time', '=', 0)
        ->first();
      if ($user) {
        $user->last_login_ip = self::GetClientIP();
        $user->last_login_location = self::GetClientLocation();
        $user->last_login_time = Share::ServerTime();
        if ($user->save()) {
          $user->password = null;
          $user->user_group = UserGroupController::find($user->user_group_id);
          $is_login = true;
        }
      }
    }

    return [
      'is_login' => $is_login,
      'user' => $user,
    ];
  }
  /**
   * 根据user_id请求用户信息
   * @param string $user_id 用户id
   * @param string $user_token token字符串 可选
   * @param string $is_admin 是否是管理员 可选
   * @return json {is_get:是否获取成功,user:用户信息含is_follow、user_group}
   */
  public static function GetUser($user_id, $user_token = '', $is_admin = false)
  {
    $user = self::find($user_id);
    $_user_id = '';
    if ($user_token != '' && $user != null) {
      $_user_id = TokenController::GetUserId($user_token);
    }


    if ($user->cover == null) {
      $user->cover = self::CreateDefaultCover();
      // $user->save();
    }

    if ($user_id == $_user_id) { //用户自己查看自己的信息 必须安全的访问用户信息
      if ($user) {
        $user->is_follow = false; //用户不可能关注自己//FollowController::IsFollow($user_token, 'user', $user_id, true);
        $user->user_group = UserGroupController::find($user->user_group_id);
      }
      if (!$is_admin && $user != null) {
        // $user->email = null;
        $user->password = null;
      }
    } else { //其他用户查看用户信息
      $user = self::GetUserInfo($user_id, $user_token)['user'];
    }

    $data = [
      'is_get' => $user != null,
      'user' => $user != null ? $user : null,
    ];
    return $data;
  }
  /**
   * 根据user_id请求用户简介信息
   * @param string $user_id 用户id
   * @param string $user_token token字符串 可选
   * @return json {is_get:是否获取成功,user:用户信息含is_follow、user_group}
   */
  public static function GetUserInfo($user_id, $user_token = '')
  {
    $user = self::find($user_id);
    $return_user = null;

    if ($user) {
      $user->is_follow = FollowController::IsFollow($user_token, 'user', $user_id, true);
      $user->user_group = UserGroupController::GetUserGroupInfo($user->user_group_id);
      if ($user->cover == null) {
        $user->cover = self::CreateDefaultCover();
        // $user->save();
      }
      $return_user = [
        'user_id' => $user->user_id,
        'username' => $user->username,
        'avatar' => $user->avatar,
        'cover' => $user->cover,
        'headline' => $user->headline,
        'blog' => $user->blog,
        'company' => $user->company,
        'location' => $user->location,
        'bio' => $user->bio,
        'question_count' => $user->question_count,
        'article_count' => $user->article_count,
        'answer_count' => $user->answer_count,
        'follower_count' => $user->follower_count,
        'followee_count' => $user->followee_count,
        'user_group' => $user->user_group,
        'is_follow' => $user->is_follow,
        'create_time' => $user->create_time,
        'update_time' => $user->update_time,
      ];
    }

    $data = [
      'is_get' => $user != null,
      'user' => $return_user,
    ];
    return $data;
  }
  /**
   * 请求用户列表信息
   * @param string $order 排序
   * @param string $page 页码
   * @param string $type 类型 recommended,followees,followers
   * @param string $user_token token字符串
   * @param string $per_page 每页显示的数量
   * @param string $search_keywords 搜索关键词
   * @param array $search_field 搜索字段
   * @param string $is_admin 是否是管理员
   * @return json {分页用户信息}
   */
  public static function GetUsers(
    $order,
    $page = 1,
    $type = 'recommended',
    $user_token = '',
    $per_page = 20,
    $search_keywords = '',
    $search_field = [],
    $is_admin = false
  ) {
    if ($search_field == []) {
      $search_field = self::$search_field;
    }
    $data = Share::HandleDataAndPagination(null);
    $order = Share::HandleArrayField($order);
    $field = $order['field'];
    $order = $order['sort'];
    if ($type == 'recommended') {
      if ($search_keywords != '') {
        $data = self::where('disable_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $order)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('disable_time', '=', 0)
          ->orderBy($field, $order)
          ->paginate($per_page, ['*'], 'page', $page);
      }

      $data = Share::HandleDataAndPagination($data);
    } else if ($type == 'followees') {
      $followees_id = FollowController::where('user_id', '=', TokenController::GetUserId($user_token))
        ->where('followable_type', '=', 'user')
        ->orderBy($field, $order)
        ->pluck('followable_id');

      if ($search_keywords != '') {
        $data = self::where('disable_time', '=', 0)
          ->whereIn('user_id', $followees_id)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $order)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('disable_time', '=', 0)
          ->whereIn('user_id', $followees_id)
          ->orderBy($field, $order)
          ->paginate($per_page, ['*'], 'page', $page);
      }

      $data = Share::HandleDataAndPagination($data);
    } else if ($type == 'followers') {
      $followers_id = FollowController::where('followable_id', '=', TokenController::GetUserId($user_token))
        ->where('followable_type', '=', 'user')
        ->orderBy($field, $order)
        ->pluck('user_id');

      if ($search_keywords != '') {
        $data = self::where('disable_time', '=', 0)
          ->whereIn('user_id', $followers_id)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $order)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('disable_time', '=', 0)
          ->whereIn('user_id', $followers_id)
          ->orderBy($field, $order)
          ->paginate($per_page, ['*'], 'page', $page);
      }

      $data = Share::HandleDataAndPagination($data);
    } else {
      // $data = self::where('disable_time', '=', 0)
      //   ->orderBy($field, $order)
      //   ->paginate($per_page, ['*'], 'page', $page);
      // $data = Share::HandleDataAndPagination($data);

      if (!$is_admin) {
        if ($search_keywords != '') {
          $data = self::where('disable_time', '=', 0)
            //->where($search_field, 'like', '%' . $search_keywords . '%')
            ->where(function ($query) use ($search_field, $search_keywords) {
              foreach ($search_field as $key => $value) {
                $query->orWhere($value, 'like', '%' . $search_keywords . '%');
              }
            })
            ->orderBy($field, $order)
            ->paginate($per_page, ['*'], 'page', $page);
        } else {
          $data = self::where('disable_time', '=', 0)
            ->orderBy($field, $order)
            ->paginate($per_page, ['*'], 'page', $page);
        }
      } else if ($is_admin && (UserGroupController::IsAdmin($user_token) || UserGroupController::IsAdminLogin($user_token))) {
        if ($search_keywords != '') {
          $data = self::where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
            ->orderBy($field, $order)
            ->paginate($per_page, ['*'], 'page', $page);
        } else {
          $data = self::orderBy($field, $order)
            ->paginate($per_page, ['*'], 'page', $page);
        }
      }

      $data = Share::HandleDataAndPagination($data);
    }

    // if($user->cover==null){
    //   $user->cover = self::CreateDefaultCover();
    //   // $user->save();
    // }

    if ($data['data'] != null && !$is_admin) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key] = self::GetUserInfo($value['user_id'], $user_token)['user'];
      }
    }

    if ($data['data'] != null && !UserGroupController::IsAdmin($user_token)) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]['email'] = null; //不返回邮箱
        $data['data'][$key]['password'] = null; //不返回密码
        $data['data'][$key]['create_ip'] = null; //不返回创建ip
        $data['data'][$key]['create_location'] = null; //不返回创建位置
        $data['data'][$key]['last_login_ip'] = null; //不返回最后登录ip
        $data['data'][$key]['last_login_location'] = null; //不返回最后登录位置
      }
    }

    //循环检查有人的背景图是否为空
    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {
        if ($value['cover'] == null) {
          $value['cover'] = self::CreateDefaultCover();
        }
      }
    }

    return $data;
  }
  /**
   * 重置所有人的头像 仅限开发者使用
   * @return json {is_reset:是否重置成功}
   */
  public static function DEV_AllUserAvatarReset()
  {
    if (!Config::Dev()) {
      return array(
        'is_reset' => false,
      );
    }
    $users = self::all();
    foreach ($users as $key => $value) {
      $avatar = self::CreateDefaultAvatar($value->username, $value->user_id);
      self::find($value->user_id)->update([
        'avatar' => $avatar,
      ]);
    }
    return array(
      'is_reset' => true,
    );
  }
  /**
   * 重置所有人的封面 仅限开发者使用
   * @return json {is_reset:是否重置成功}
   */
  public static function DEV_AllUserCoverReset()
  {
    if (!Config::Dev()) {
      return array(
        'is_reset' => false,
      );
    }
    // $users = self::all();
    // foreach ($users as $key => $value) {
    //   $cover = self::CreateDefaultCover();
    //   self::find($value->user_id)->update([
    //     'cover' => $cover,
    //   ]);
    // }
    $users = self::where('cover', '=', null)->get();
    foreach ($users as $key => $value) {
      $cover = self::CreateDefaultCover();
      self::find($value->user_id)->update([
        'cover' => $cover,
      ]);
    }
    return array(
      'is_reset' => true,
    );
  }
  /**
   * 重置用户头像
   * @param string $user_id 用户id
   * @param string $user_token token字符串
   * @return json {is_reset:是否重置成功}
   */
  public static function ResetAvatar($user_id, $user_token)
  {
    $is_reset = false;
    $user = self::find($user_id);
    try {
      if ($user != null) {
        if (UserGroupController::IsAdmin($user_token) || TokenController::IsUserSelf($user_token, $user_id)) {
          if ($user->avatar != null) {
            $avatar = $user->avatar;
            if (is_array($avatar)) {
              if (array_search('default', $avatar) === false) {
                ImageController::DeleteUploadImage($user->avatar);
              }
            } else {
              if (strpos($user->avatar, 'default') === false) {
                ImageController::DeleteUploadImage($user->avatar);
              }
            }
          }
          $avatar = self::CreateDefaultAvatar($user->username, $user_id);
          $is_reset = self::find($user_id)->update([
            'avatar' => $avatar,
          ]);
        }
      }
    } catch (\Exception $e) {
      return [
        'is_reset' => $is_reset,
        'message' => $e->getMessage(),
      ];
    }
    return [
      'is_reset' => $is_reset,
      'user' => self::GetUser($user_id, $user_token)['user'],
    ];
  }
  /**
   * 重置用户封面
   * @param string $user_id 用户id
   * @param string $user_token token字符串
   * @return json {is_reset:是否重置成功}
   */
  public static function ResetCover($user_id, $user_token)
  {
    $is_reset = false;
    $user = self::find($user_id);
    try {
      if ($user != null) {
        if (UserGroupController::IsAdmin($user_token) || TokenController::IsUserSelf($user_token, $user_id)) {
          if ($user->cover != null) {
            $cover = $user->cover;
            if (is_array($cover)) {
              if (array_search('default', $cover) === false) {
                ImageController::DeleteUploadImage($user->cover);
              }
            } else {
              if (strpos($user->cover, 'default') === false) {
                ImageController::DeleteUploadImage($user->cover);
              }
            }
          }
          $cover = self::CreateDefaultCover();
          $is_reset = self::find($user_id)->update([
            'cover' => $cover,
          ]);
        }
      }
    } catch (\Exception $e) {
      return [
        'is_reset' => $is_reset,
        'message' => $e->getMessage(),
      ];
    }
    return [
      'is_reset' => $is_reset,
      'user' => self::GetUser($user_id, $user_token)['user'],
    ];
  }
  /**
   * 上传头像
   * @param string $user_token token字符串
   * @param string $avatar 头像base64
   * @return json {is_upload:是否上传成功,user:用户信息}
   */
  public static function UploadAvatar($user_token, $avatar)
  {
    $v = false;
    $server_user = null;
    $server_avatar = null;
    $server_user_id = TokenController::GetUserId($user_token);
    $server_user = self::find($server_user_id);
    if ($server_user != null) {
      if (ImageController::DeleteUploadImage($server_user->avatar)) {
        $server_avatar = ImageController::SaveUploadImage('user_avatar', $avatar, $server_user_id);
      }
      if ($server_avatar != null) {
        $v = self::find($server_user_id)->update([
          'avatar' => $server_avatar,
        ]);
      }
    }
    $data = array(
      'is_upload' => $v,
      'user' => User::GetUser($server_user_id, $user_token)['user'],
      //'avatar' => $avatar,
    );
    $v ? $data['user']['password'] = '' : ''; //不返回密码
    $v ? $data['user']['user_group'] = UserGroupController::find($server_user->user_group_id) : '';
    return $data;
  }
  /**
   * 上传封面
   * @param string $user_token token字符串
   * @param string $cover 封面base64
   * @return json {is_upload:是否上传成功,user:用户信息}
   */
  public static function UploadCover($user_token, $cover)
  {
    $v = false;
    $server_user = null;
    $server_cover = null;
    $server_user_id = TokenController::GetUserId($user_token);
    $server_user = self::find($server_user_id);
    if ($server_user != null) {
      if (ImageController::DeleteUploadImage($server_user->cover)) {
        $server_cover = ImageController::SaveUploadImage('user_cover', $cover, $server_user_id);
      }
      if ($server_cover != null) {
        $v = self::find($server_user_id)->update([
          'cover' => $server_cover,
        ]);
      }
    }
    $data = array(
      'is_upload' => $v,
      'user' => User::GetUser($server_user_id, $user_token)['user'],
      //'avatar' => $avatar,
    );
    $v ? $data['user']['password'] = '' : ''; //不返回密码
    $v ? $data['user']['user_group'] = UserGroupController::find($server_user->user_group_id) : '';
    return $data;
  }
  /**
   * 获取用户的提问
   * @param string $user_id 用户id
   * @param string $order 排序
   * @param string $page 页码
   * @param string $user_token token字符串
   * @param string $per_page 每页显示的数量
   * @return json {分页提问信息}
   */
  public static function GetUserQuestions(
    $user_id,
    $order,
    $page,
    $user_token,
    $per_page = 20
  ) {
    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    $data = QuestionController::where('user_id', '=', $user_id)
      ->where('delete_time', '=', 0)
      ->orderBy($field, $sort)
      ->paginate($per_page, ['*'], 'page', $page);
    $data = Share::HandleDataAndPagination($data);
    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]->user = self::GetUserInfo($value->user_id, $user_token)['user'];
      }
    }
    return $data;
  }
  /**
   * 获取用户的回答
   * @param string $user_id 用户id
   * @param string $order 排序
   * @param string $page 页码
   * @param string $user_token token字符串
   * @param string $per_page 每页显示的数量
   * @return json {分页回答信息}
   */
  public static function GetUserArticles(
    $user_id,
    $order,
    $page,
    $user_token,
    $per_page = 20
  ) {
    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    $data = ArticleController::where('user_id', '=', $user_id)
      ->where('delete_time', '=', 0)
      ->orderBy($field, $sort)
      ->paginate($per_page, ['*'], 'page', $page);
    $data = Share::HandleDataAndPagination($data);
    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]->user = self::GetUserInfo($value->user_id, $user_token)['user'];
      }
    }
    return $data;
  }
  /**
   * 获取用户的回答
   * @param string $user_id 用户id
   * @param string $order 排序
   * @param string $page 页码
   * @param string $user_token token字符串
   * @param string $per_page 每页显示的数量
   * @return json {分页回答信息}
   */
  public static function GetUserAnswers(
    $user_id,
    $order,
    $page,
    $user_token,
    $per_page = 20
  ) {
    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);
    $field = $orders['field'];
    $sort = $orders['sort'];
    $data = AnswerController::where('user_id', '=', $user_id)
      ->where('delete_time', '=', 0)
      ->orderBy($field, $sort)
      ->paginate($per_page, ['*'], 'page', $page);
    $data = Share::HandleDataAndPagination($data);
    if ($data['data'] != null) {
      // if (isset($data['data']) && is_array($data['data'])) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]->user = self::GetUserInfo($value->user_id, $user_token)['user'];
        //根据question_id获取问题的标题。问题可能被删除。将不再有效
        // $data['data'][$key]->question_title = QuestionController::GetQuestion($value->question_id, $user_token)['question']['title'];

        $question = QuestionController::find($value->question_id);
        //如果$question->delete_time不为0则说明问题已经被删除
        if ($question->delete_time != 0) {
          $data['data'][$key]->question_title = null;

          //同时从数组中删除这个回答
          unset($data['data'][$key]);
        } else {
          $data['data'][$key]->question_title = $question->title;
        }
      }
    }
    return $data;
  }
  /**
   * 用户添加提问、文章、回答图片
   * @param string $user_token token字符串
   * @param string $type 类型 question、article、answer
   * @param string $file 图片base64
   * @return json {is_upload:是否上传成功,upload_url:上传的图片url}
   */
  public static function UploadImage($user_token, $type, $file)
  {
    $data = [
      'is_upload' => false,
      'upload_url' => null,
    ];
    $user_id = TokenController::GetUserId($user_token);
    if ($user_id != null) {
      //确保type是正确的
      if ($type != 'question' && $type != 'article' && $type != 'answer') {
        return $data;
      }
      $upload_url = ImageController::SaveUploadImage($type, $file, $user_id);
      //上传成功,保存一份记录到数据库
      if ($upload_url != null) {
        ImageController::AddImageRecord($type, 0, $user_id, $upload_url['original'], 0, 0);
      }
      $is_upload = $upload_url != null;
      $data['is_upload'] = $is_upload;
      $data['upload_url'] = $upload_url['original'];
    }
    return $data;
  }
  /**
   * 设置用户语言
   * @param string $user_token token字符串
   * @param string $lang 语言
   * @return json {is_set:是否设置成功,user:用户信息}
   */
  public static function SetUserLanguage($user_token, $lang)
  {
    $is_set = false;
    $user_id = TokenController::GetUserId($user_token);
    $user = self::find($user_id);
    if ($user != null) {
      $user->language = $lang;
      $is_set = $user->save();
    }
    $user->language = $lang;
    return [
      'is_set' => $is_set,
      'user' => self::GetUser($user_id, $user_token)['user'],
    ];
  }
  /**
   * 设置多个用户的用户组为新的用户组
   * @param string $user_token token 操作者用户token字符串
   * @param string $user_group_id 用户组id
   * @param array $user_ids 用户id数组
   */
  public static function SetUsersUserGroup($user_token, $user_group_id, $user_ids)
  {
    $is_set = false;
    $is_admin = UserGroupController::IsAdmin($user_token);
    if ($is_admin) {
      $is_set = UserGroupController::MoveUserGroups($user_group_id, $user_ids);
    }
    return [
      'is_set' => $is_set,
    ];
  }
  /**
   * 设置多个用户的禁用时间
   * @param string $user_token token 操作者用户token字符串
   * @param string $user_ids 用户id数组
   * @param string $disable_time 禁用时间默认0是不禁用,其他值是禁用时间
   */
  public static function SetUsersDisableTime($user_token, $user_ids, $disable_time = 0)
  {
    $is_set = false;
    $disable_time != 0 ? Share::ServerTime() : 0;
    $is_admin =  (UserGroupController::IsAdmin($user_token) && UserGroupController::Ability($user_token, 'ability_admin_manage_user'));
    // UserGroupController::IsAdmin($user_token)//UserGroupController::IsAdmin($user_token);
    if ($is_admin) {
      $is_set = self::whereIn('user_id', $user_ids)->update([
        'disable_time' => $disable_time,
      ]);
    }
    return [
      'is_delete' => $is_set,
    ];
  }
}
