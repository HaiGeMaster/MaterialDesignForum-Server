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

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Option as OptionModel;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Plugins\Share;
// form_data: {
//   site_name: '',
//   site_description: '',
//   site_keywords: '',
//   site_icp_beian: '',
//   site_gongan_beian: '',
//   default_language: '',
// },
class Option extends OptionModel
{
  private $sensitive_options = [
    'site_activation_key',
    'smtp_host',
    'smtp_password',
    'smtp_port',
    'smtp_reply_to',
    'smtp_secure',
    'smtp_send_name',
    'smtp_username',
    'github_client_id',
    'github_client_secret',
    'google_client_id',
    'google_client_secret',
    'microsoft_client_id',
    'microsoft_client_secret',
  ];
  /**
   * 获取Oauth所有的选项
   * @param string $user_token 用户token
   * @return array [is_get, options]
   */
  public static function GetOauthOptions($user_token)
  {
    $form_data = [];
    if (UserGroupController::IsAdmin($user_token)) {
      $option = self::find('github_client_id');
      if ($option) {
        $form_data['github_client_id'] = $option->value;
      }
      $option = self::find('github_client_secret');
      if ($option) {
        $form_data['github_client_secret'] = $option->value;
      }
      $option = self::find('google_client_id');
      if ($option) {
        $form_data['google_client_id'] = $option->value;
      }
      $option = self::find('google_client_secret');
      if ($option) {
        $form_data['google_client_secret'] = $option->value;
      }
      $option = self::find('microsoft_client_id');
      if ($option) {
        $form_data['microsoft_client_id'] = $option->value;
      }
      $option = self::find('microsoft_client_secret');
      if ($option) {
        $form_data['microsoft_client_secret'] = $option->value;
      }
    }
    return [
      'is_get' => !empty($form_data),
      'form_data' => $form_data,
    ];
  }
  public static function SetOauthOptions($form_data, $user_token)
  {
    $is_set = false;
    if (UserGroupController::IsAdmin($user_token)) {
      $option = self::find('github_client_id');
      if ($option) {
        $option->value = $form_data['github_client_id'];
        $option->save();
      }
      $option = self::find('github_client_secret');
      if ($option) {
        $option->value = $form_data['github_client_secret'];
        $option->save();
      }
      $option = self::find('google_client_id');
      if ($option) {
        $option->value = $form_data['google_client_id'];
        $option->save();
      }
      $option = self::find('google_client_secret');
      if ($option) {
        $option->value = $form_data['google_client_secret'];
        $option->save();
      }
      $option = self::find('microsoft_client_id');
      if ($option) {
        $option->value = $form_data['microsoft_client_id'];
        $option->save();
      }
      $option = self::find('microsoft_client_secret');
      if ($option) {
        $option->value = $form_data['microsoft_client_secret'];
        $option->save();
      }
      $is_set = true;
    }
    return [
      'is_set' => $is_set,
    ];
  }
  /**
   * 获取指定第三方平台的Client ID
   * @param string $oauthName 第三方平台标识符
   * @return string|null 返回Client ID或null
   */
  public static function GetOauthClientId($oauthName)
  {
    $option = self::find($oauthName . '_client_id');
    if ($option) {
      return [
        'is_get' => true,
        'client_id' => $option->value,
      ];
    }
    return [
      'is_get' => false,
      'client_id' => null,
    ];
  }
  /**
   * 获取指定第三方平台的Client Link
   * @param string $oauthName 第三方平台标识符 可选值：github、microsoft
   * @return string|null 返回Client Link或null
   */
  public static function GetOauthClientLink($oauthName){
    $url = '';
    $redirectUri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/api/oauth/redirect/' . $oauthName;
    //如果域名包含localhost
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false){
      $redirectUri = 'http://localhost:83/api/oauth/redirect/' . $oauthName;
    }
    switch($oauthName){
      case 'github':
        $client_id = self::GetOauthClientId('github');
        $url = 'https://github.com/login/oauth/authorize?client_id=' . $client_id . '&redirect_uri=' . $redirectUri . '&scope=user';
        break;
      case 'microsoft':
        $client_id = self::GetOauthClientId('microsoft');
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirectUri . '&scope=openid%20profile%20User.Read';
        break;
      case 'google':
        $client_id = self::GetOauthClientId('google');
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $redirectUri . '&scope=openid%20profile%20email';
        break;
    }
    header('Location: ' . $url);
    exit;
    // return [
    //   'is_get' => !empty($url),
    //   'url' => $url,
    // ];
  }
  /**
   * 获取网站信息
   * @return array [is_get, form_data]
   */
  public static function GetInfoData(
    // $user_token
  )
  {
    $form_data = null;
    // if (UserGroupController::IsAdmin($user_token)) {
    $option = self::find('site_name');
    if ($option) {
      $form_data['site_name'] = $option->value;
    }
    $option = self::find('site_description');
    if ($option) {
      $form_data['site_description'] = $option->value;
    }
    $option = self::find('site_keywords');
    if ($option) {
      $form_data['site_keywords'] = $option->value;
    }
    $option = self::find('site_icp_beian');
    if ($option) {
      $form_data['site_icp_beian'] = $option->value;
    }
    $option = self::find('site_gongan_beian');
    if ($option) {
      $form_data['site_gongan_beian'] = $option->value;
    }
    $option = self::find('default_language');
    if ($option) {
      $form_data['default_language'] = $option->value;
    }
    // }
    return [
      'is_get' => $form_data != null,
      'form_data' => $form_data,
    ];
  }
  /**
   * 设置网站信息
   * @param  array  $form_data 传入的数据
   * @param  string $user_token 用户token
   * @return array [is_set]
   */
  public static function SetInfoData($form_data, $user_token)
  {
    $is_set = false;
    if (UserGroupController::IsAdmin($user_token)) {
      $option = self::find('site_name');
      if ($option) {
        $option->value = $form_data['site_name'];
        $option->save();
      }
      $option = self::find('site_description');
      if ($option) {
        $option->value = $form_data['site_description'];
        $option->save();
      }
      $option = self::find('site_keywords');
      if ($option) {
        $option->value = $form_data['site_keywords'];
        $option->save();
      }
      $option = self::find('site_icp_beian');
      if ($option) {
        $option->value = $form_data['site_icp_beian'];
        $option->save();
      }
      $option = self::find('site_gongan_beian');
      if ($option) {
        $option->value = $form_data['site_gongan_beian'];
        $option->save();
      }
      $option = self::find('default_language');
      if ($option) {
        $option->value = $form_data['default_language'];
        $option->save();
      }
      $is_set = true;
    }
    return [
      'is_set' => $is_set,
    ];
  }
  /**
   * 获取邮件信息
   * @param  string $user_token 用户token
   * @return array [is_get, form_data]
   */
  public static function GetMailData($user_token)
  {
    $form_data = null;
    if (UserGroupController::IsAdmin($user_token)) {
      $option = self::find('smtp_host');
      if ($option) {
        $form_data['smtp_host'] = $option->value;
      }
      $option = self::find('smtp_password');
      if ($option) {
        $form_data['smtp_password'] = $option->value;
      }
      $option = self::find('smtp_port');
      if ($option) {
        $form_data['smtp_port'] = $option->value;
      }
      $option = self::find('smtp_reply_to');
      if ($option) {
        $form_data['smtp_reply_to'] = $option->value;
      }
      $option = self::find('smtp_secure');
      if ($option) {
        $form_data['smtp_secure'] = $option->value;
      }
      $option = self::find('smtp_send_name');
      if ($option) {
        $form_data['smtp_send_name'] = $option->value;
      }
      $option = self::find('smtp_username');
      if ($option) {
        $form_data['smtp_username'] = $option->value;
      }
    }
    return [
      'is_get' => $form_data != null,
      'form_data' => $form_data,
    ];
  }
  /**
   * 设置邮件信息
   * @param  array  $form_data 传入的数据
   * @param  string $user_token 用户token
   * @return array [is_set]
   */
  public static function SetMailData($form_data, $user_token)
  {
    $is_set = false;
    if (UserGroupController::IsAdmin($user_token)) {
      $option = self::find('smtp_host');
      if ($option) {
        $option->value = $form_data['smtp_host'];
        $option->save();
      }
      $option = self::find('smtp_password');
      if ($option) {
        $option->value = $form_data['smtp_password'];
        $option->save();
      }
      $option = self::find('smtp_port');
      if ($option) {
        $option->value = $form_data['smtp_port'];
        $option->save();
      }
      $option = self::find('smtp_reply_to');
      if ($option) {
        $option->value = $form_data['smtp_reply_to'];
        $option->save();
      }
      $option = self::find('smtp_secure');
      if ($option) {
        $option->value = $form_data['smtp_secure'];
        $option->save();
      }
      $option = self::find('smtp_send_name');
      if ($option) {
        $option->value = $form_data['smtp_send_name'];
        $option->save();
      }
      $option = self::find('smtp_username');
      if ($option) {
        $option->value = $form_data['smtp_username'];
        $option->save();
      }
      $is_set = true;
    }
    return [
      'is_set' => $is_set,
    ];
  }
  /**
   * 获取主题信息
   * @param  string $user_token 用户token
   * @return array [is_get, form_data]
   */
  public static function GetThemeData($user_token)
  {
    $form_data = null;
    //$form_data = Share::GetThemesInfo();

    // if (UserGroupController::IsAdmin($user_token)) {

    // $option = self::find('theme');
    // if ($option) {
    //   $form_data['theme'] = $option->value;
    // }
    $form_data = Share::GetThemesInfo();

    // }
    return [
      'is_get' => $form_data != null,
      'form_data' => $form_data,
    ];
  }
  /**
   * 获取当前主题
   * @return string
   */
  public static function GetCurrentTheme()
  {
    $option = self::find('theme');
    if ($option) {
      return [
        'is_get' => true,
        'theme' => $option->value,
      ];
    } else {
      return [
        'is_get' => false,
        'theme' => null,
      ];;
    }
  }
  /**
   * 设置主题信息
   * @param  string $theme_name 传入的数据
   * @param  string $user_token 用户token
   * @return array [is_set]
   */
  public static function SetCurrentTheme($theme_name, $user_token)
  {
    $is_set = false;
    if (UserGroupController::IsAdmin($user_token)) {
      $option = self::find('theme');
      if ($option) {
        $option->value = $theme_name;
        $option->save();
      }
      $is_set = true;
    }
    return [
      'is_set' => $is_set,
    ];
  }
  /**
   * 设置激活码
   * @param  string $user_token 用户token
   * @param  string $key 激活码
   * @return array [is_set]
   */
  public static function SetActivationKey($user_token, $key)
  {
    $is_set = false;
    if (!UserGroupController::IsAdmin($user_token)) {
      return [
        'is_set' => $is_set,
      ];
    }
    $option = self::find('site_activation_key');
    if ($option) {
      $option->value = $key;
      $is_set = $option->save();
    }
    return [
      'is_set' => $is_set,
    ];
  }
  /**
   * 获取激活码
   * @param  string $user_token 用户token
   * @return array [is_get, key]
   */
  public static function GetActivationKey($user_token)
  {
    $option = self::find('site_activation_key');
    return [
      'is_get' => $option != null && $option->value != null && $option->value != '',
      'key' => base64_encode($option->value),
    ];
  }
  /**
   * 设置主题颜色参数
   * @param  string $user_token 用户token
   * @param  string $json_text json文本
   * @return array [is_get, json_text]
   */
  public static function SetThemeColorParamJson($user_token, $json_text)
  {
    $is_set = false;
    if (!UserGroupController::IsAdmin($user_token)) {
      return [
        'is_set' => $is_set,
      ];
    }
    $option = self::find('theme_color_param');
    if ($option) {
      $option->value = $json_text;
      $is_set = $option->save();
    }
    //{"light":{"primary":"#415f91","secondary":"#415f91","accent":"#8eace3"},"dark":{"primary":"#415f91","secondary":"#415f91","accent":"#8eace3"}}
    return [
      'is_set' => $is_set,
      'json_text' => json_decode($json_text, true),//json文本转换为php数组
    ];
  }
  /**
   * 获取主题颜色参数
   * @param  string $user_token 用户token
   * @return array [is_get, json_text]
   */
  public static function GetThemeColorParamJson($user_token)
  {
    $option = self::find('theme_color_param');
    //{"light":{"primary":"#415f91","secondary":"#415f91","accent":"#8eace3"},"dark":{"primary":"#415f91","secondary":"#415f91","accent":"#8eace3"}}

    return [
      'is_get' => $option != null && $option->value != null && $option->value != '',
      'json_text' => json_decode($option->value, true),//json文本转换为php数组
    ];
  }
  /**
   * 设置打字机效果参数
   * @param  string $user_token 用户token
   * @param  string $json_text json文本
   * @return array [is_get, json_text]
   */
  public static function SetThemeTypedParamJson($user_token, $json_text)
  {
    $is_set = false;
    if (!UserGroupController::IsAdmin($user_token)) {
      return [
        'is_set' => $is_set,
      ];
    }
    $option = self::find('theme_typed_param');
    if ($option) {
      $option->value = $json_text;
      $is_set = $option->save();
    }
    //{"header":"Message.Components.TextPlay.With","body":"Message.Components.TextPlay.MaterialDesign,Message.Components.TextPlay.VueAsTheCore,Message.Components.TextPlay.ImplementedByVuetify,Message.Components.TextPlay.MoreElegant,Message.Components.TextPlay.UnlimitedDistance,Message.Components.TextPlay.CrossPlatform,Message.Components.TextPlay.DynamicResponsive","footer_header":"Message.Components.TextPlay.TheWay","footer_tail":"Message.Components.TextPlay.EnjoyCommunication"}

    return [
      'is_set' => $is_set,
      'json_text' => json_decode($json_text, true),//json文本转换为php数组
    ];
  }
  /**
   * 获取打字机效果参数
   * @param  string $user_token 用户token
   * @return array [is_get, json_text]
   */
  public static function GetThemeTypedParamJson($user_token)
  {
    $option = self::find('theme_typed_param');
    //{"header":"Message.Components.TextPlay.With","body":"Message.Components.TextPlay.MaterialDesign,Message.Components.TextPlay.VueAsTheCore,Message.Components.TextPlay.ImplementedByVuetify,Message.Components.TextPlay.MoreElegant,Message.Components.TextPlay.UnlimitedDistance,Message.Components.TextPlay.CrossPlatform,Message.Components.TextPlay.DynamicResponsive","footer_header":"Message.Components.TextPlay.TheWay","footer_tail":"Message.Components.TextPlay.EnjoyCommunication"}
    return [
      'is_get' => $option != null && $option->value != null && $option->value != '',
      'json_text' => json_decode($option->value, true),//json文本转换为php数组
    ];
  }
  /**
   * 设置轮播图参数
   * @param  string $user_token 用户token
   * @param  string $json_text json文本
   * @return array [is_set, json_text]
   */
  public static function SetThemeCarouselParamJson($user_token, $json_text)
  {
    $is_set = false;
    if (!UserGroupController::IsAdmin($user_token)) {
      return [
        'is_set' => $is_set,
      ];
    }
    $option = self::find('theme_carousel_param');
    //如果json_text是json数组，需要使用json_encode转换json表现
    $json_text = json_encode($json_text);
    if ($option) {
      $option->value = $json_text;
      $is_set = $option->save();
    }else{
      $option = new Option();
      $option->name = 'theme_carousel_param';
      $option->value = $json_text;
      $is_set = $option->save();
    }
    return [
      'is_set' => $is_set,
      // 'json_text' => json_decode($json_text, true),//json文本转换为php数组
      'json_text' => json_decode($json_text, true)??[],//json文本转换为php数组
    ];
  }
  /**
   * 获取轮播图参数
   * @param  string $user_token 用户token
   * @return array [is_get, json_text]
   */
  public static function GetThemeCarouselParamJson($user_token)
  {
    $option = self::find('theme_carousel_param');
    return [
      'is_get' => $option != null && $option->value != null && $option->value != '',
      'json_text' => json_decode($option->value, true)??[],//json文本转换为php数组
    ];
  }
  /**
   * 获取所有的配置信息
   * @return array
   */
  public static function GetAllOptions()
  {
    $options = self::all();
    $options_array = [];
    foreach ($options as $option) {
      $options_array[$option->name] = $option->value;
    }

    foreach(self::$sensitive_options as $sensitive_option) {
      unset($options_array[$sensitive_option]);// 移除敏感选项
    }

    return $options_array;
  }
  /**
   * 获取指定的配置信息
   * @param  string $name 配置名称
   * @param  string $user_token 用户token 需要管理员权限
   * @return array
   */
  public static function GetOption($name, $user_token)
  {
    $option = self::find($name);
    if (in_array($name, self::$sensitive_options)) {//检查是否敏感字段
      if (!UserGroupController::IsAdmin($user_token)) {
        return [
          'is_get' => false,
          'value' => null,
        ];
      }
    }
    return [
      'is_get' => $option != null,
      'value' => $option->value,
    ];
  }
  /**
   * 设置指定的配置信息
   * @param  string $name 配置名称
   * @param  string $value 配置值
   * @param  string $user_token 用户token 需要管理员权限
   * @return array
   */
  public static function SetOption($name, $value, $user_token)
  {
    $is_set = false;
    if (!UserGroupController::IsAdmin($user_token)) {
      return [
        'is_set' => $is_set,
      ];
    }
    $option = self::find($name);
    if ($option) {
      $option->value = $value;
      $is_set = $option->save();
    } else {
      $option = new OptionModel();
      $option->name = $name;
      $option->value = $value;
      $is_set = $option->save();
    }
    return [
      'is_set' => $is_set,
    ];
  }
}
