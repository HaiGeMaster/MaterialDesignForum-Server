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

use MaterialDesignForum\Models\i18n as i18nModel;
use MaterialDesignForum\Config\Config;
use MaterialDesignForum\Models\Option;

class i18n
{
  public static $i18n = null;
  public static $null_language = null;

  private static function Init()
  {
    if (self::$i18n == null) {
      self::$i18n = self::CreateInstance();
      self::$null_language = '404 ' . self::$i18n->locale . ' language is null';
    }
  }
  /**
   * 语言国际化i18n对象
   */
  public static function i18n()
  {
    if (self::$i18n == null) {
      self::Init();
    }
    return self::$i18n;
  }
  /**
   * 语言国际化t函数
   * @param string $t
   * @param string $l
   * @return string
   */
  public static function t($t = 'Message.hello', $l = '')
  {
    self::Init();
    return self::$i18n->t($t, $l);
  }
  /**
   * 验证语言 仅限于构造实例self::i18n后使用
   * @return string
   */
  public static function VerificationLanguages($lang)
  {
    if (self::i18n()->hasLocale($lang)) {
      return true;
    } else {
      return false;
    }
  }

  // private static function CreateInstance()
  // {
  //   $dataFolder = Config::GetWebLocalePath(); //'././public/locale/json/'

  //   //遍历语言文件夹，获取语言文件，为.json文件
  //   $localization = array();
  //   $dir = opendir($dataFolder);
  //   while (($file = readdir($dir)) !== false) {
  //     if ($file != '.' && $file != '..') {
  //       $localization[str_replace('.json', '', $file)] = json_decode(file_get_contents($dataFolder . $file), true); //$localization['zh_CN']=array()
  //     }
  //   }

  //   try {

  //     $lang = '';

  //     foreach ($localization as $key => $value) { //$key=zh_CN,$value=array()
  //       //如果$_SERVER['REQUEST_URI']的部分与$key相同，那么就是$key
  //       if (str_contains($_SERVER['REQUEST_URI'], $key)) {
  //         $lang = $key;
  //         break;
  //       }
  //     }

  //     if ($lang == '' || !isset($localization[$lang])) { //如果为空或不来自localization
  //       //停止任何报错
  //       // error_reporting(0);

  //       // $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5); //从请求协议中获取语言符号
  //       $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] || '';
  //       if ($lang != '' && $acceptLanguage != '') {
  //         $lang = substr($acceptLanguage, 0, 5); //从请求协议中获取语言符号
  //         $lang = str_replace("-", "_", $lang);
  //       }

  //       //开启报错
  //       // error_reporting(E_ALL);
  //     }

  //     $locale = $lang ?? $_COOKIE['lang'] ?? Option::Get('default_language') ?? 'en_US';//?? array_keys($localization)[0] ?? 'en_US';


  //     $fallbackLocale = Option::Get('default_language') ?? 'en_US';

  //     if (is_numeric($locale)||$locale == ''||$locale == null) {
  //       $locale = array_keys($localization)[0] ?? 'en_US';
  //     }
  //     if ($fallbackLocale == null || $fallbackLocale == '') {
  //       $fallbackLocale = array_keys($localization)[0]??'en_US';
  //     }

  //     $i18n = new i18nModel($locale, $fallbackLocale, $localization);
  //     return $i18n;
  //   } catch (\Exception $e) {
  //     $locale = 'en_US';
  //     return new i18nModel($locale, $locale, $localization);
  //   }
  // }

  private static function CreateInstance()
  {
    $dataFolder = Config::GetWebLocalePath();

    $localization = array();
    $dir = opendir($dataFolder);
    while (($file = readdir($dir)) !== false) {
      if ($file != '.' && $file != '..') {
        $localization[str_replace('.json', '', $file)] = json_decode(file_get_contents($dataFolder . $file), true);
      }
    }

    try {
      $lang = '';

      foreach ($localization as $key => $value) {
        if (str_contains($_SERVER['REQUEST_URI'], $key)) {
          $lang = $key;
          break;
        }
      }

      if ($lang === '' || !isset($localization[$lang])) {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($acceptLanguage !== '') {
          $lang = substr($acceptLanguage, 0, 5);
          $lang = str_replace("-", "_", $lang);
        }
      }

      // 确保默认值逻辑正确
      $defaultLang = Option::Get('default_language') ?: array_keys($localization)[0];
      $locale = isset($localization[$lang]) ? $lang : $defaultLang;

      // 强制类型转换
      $locale = (string)$locale;
      $fallbackLocale = (string)(Option::Get('default_language') ?: $defaultLang);

      $i18n = new i18nModel($locale, $fallbackLocale, $localization);
      return $i18n;
    } catch (\Exception $e) {
      // 异常时确保 locale 有效
      $locale = (string)(Option::Get('default_language') ?: 'en_US');
      return new i18nModel($locale, $locale, $localization);
    }
  }

  // private static function CreateInstance()
  // {
  //   $dataFolder = Config::GetWebLocalePath(); //'././public/locale/json/'

  //   //遍历语言文件夹，获取语言文件，为.json文件
  //   $localization = array();
  //   $dir = opendir($dataFolder);
  //   while (($file = readdir($dir)) !== false) {
  //     if ($file != '.' && $file != '..') {
  //       $localization[str_replace('.json', '', $file)] = json_decode(file_get_contents($dataFolder . $file), true); //$localization['zh_CN']=array()
  //     }
  //   }

  //   try {

  //     $lang = '';

  //     foreach ($localization as $key => $value) { //$key=zh_CN,$value=array()
  //       //如果$_SERVER['REQUEST_URI']的部分与$key相同，那么就是$key
  //       if (str_contains($_SERVER['REQUEST_URI'], $key)) {
  //         $lang = $key;
  //         break;
  //       }
  //     }

  //     if ($lang == '' || !isset($localization[$lang])) { //如果为空或不来自localization
  //       //停止任何报错
  //       // error_reporting(0);

  //       // $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5); //从请求协议中获取语言符号
  //       $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] || '';
  //       if ($lang != '' && $acceptLanguage != '') {
  //         $lang = substr($acceptLanguage, 0, 5); //从请求协议中获取语言符号
  //         $lang = str_replace("-", "_", $lang);
  //       }

  //       //开启报错
  //       // error_reporting(E_ALL);
  //     }

  //     $locale  = isset($localization[$lang]) ? $lang : (Option::Get('default_language') ?? $_COOKIE['lang'] ?? array_keys($localization)[0] ?? 'en_US');

  //     if (is_numeric($locale)||$locale == ''||$locale == null) {
  //       $locale = array_keys($localization)[0] ?? 'en_US';
  //     }

  //     $fallbackLocale = Option::Get('default_language') ?? $_COOKIE['lang'] ?? array_keys($localization)[0] ?? 'en_US';

  //     if ($fallbackLocale == null || $fallbackLocale == '') {
  //       $fallbackLocale = array_keys($localization)[0]??'en_US';
  //     }

  //     $i18n = new i18nModel($locale || 'en_US', $fallbackLocale, $localization);
  //     return $i18n;
  //   } catch (\Exception $e) {
  //     $locale = 'en_US';
  //     return new i18nModel($locale, $locale, $localization);
  //   }
  // }

  /**
   * 获取语言列表
   * @return array
   */
  public static function getLocaleList()
  {
    return self::i18n()->getLocaleList();
  }

  /**
   * 获取语言信息列表
   * @return array
   */
  public static function GetLocaleInfoList()
  {
    return self::i18n()->GetLocaleInfoList();
  }
}
