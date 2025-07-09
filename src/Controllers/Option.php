<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
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
      //将json文本转换为数组
      // $option->value = json_encode($json_text);
      $is_set = $option->save();
    }
    //{"light":{"primary":"#415f91","secondary":"#415f91","accent":"#8eace3"},"dark":{"primary":"#415f91","secondary":"#415f91","accent":"#8eace3"}}
    return [
      'is_set' => $is_set,
      'json_text' => $json_text,
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
      'json_text' => $option->value,
      //转换为数组
      // 'json_text' => json_encode($option->value, true),
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
      'json_text' => $json_text,
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
      'json_text' => $option->value,
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

    //去除site_activation_key、smtp_host、smtp_password、smtp_port、smtp_reply_to、smtp_secure、smtp_send_name、smtp_username
    unset($options_array['site_activation_key']);
    unset($options_array['smtp_host']);
    unset($options_array['smtp_password']);
    unset($options_array['smtp_port']);
    unset($options_array['smtp_reply_to']);
    unset($options_array['smtp_secure']);
    unset($options_array['smtp_send_name']);
    unset($options_array['smtp_username']);

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
    //如果是site_activation_key、smtp_host、smtp_password、smtp_port、smtp_reply_to、smtp_secure、smtp_send_name、smtp_username其中之一，需要管理员权限
    $arr = ['site_activation_key', 'smtp_host', 'smtp_password', 'smtp_port', 'smtp_reply_to', 'smtp_secure', 'smtp_send_name', 'smtp_username'];
    if (in_array($name, $arr)) {
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
    }else{
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
