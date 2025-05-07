<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Models;

/**
 * HaiGeMaster
 * 2652549974@qq.com
 */
class i18n
{
  // 属性
  public string $locale;
  public $fallbackLocale;
  public $messages;
  // 构造方法
  /**
   * @param function $i18n = new i18n('zh_CN','en_US',$localization['messages']) 构造方法
   * @param function $i18n->t('message.xxx') 翻译方法
   * @param string $locale 语言区域标识符号 例如zh_CN
   * @param string $fallbackLocale 回退语言区域标识符号 例如zh_CN
   * @param array $messages 语言包 array('zh_CN'=>array('message'=>array('hello'=>'你好')), 'en_US'=>array('message'=>array('hello'=>'Hello'))
   */
  public function __construct($locale, $fallbackLocale, $messages)
  {
    //$locale必须是messages的key之一
    if (!isset($messages[$locale])) {
      // throw new \InvalidArgumentException('Locale "' . $locale . '" does not exist in the messages array');
      $locale = $fallbackLocale;
    }
    //确定$locale符合表达式xx_XX
    if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $locale)) {
      // throw new \InvalidArgumentException('Locale "' . $locale . '" is not a valid locale format');
    }
    // 构造函数的逻辑
    $this->locale = $locale;
    $this->fallbackLocale = $fallbackLocale;
    $this->messages = $messages;
  }
  // t方法
  /**
   * @param function $i18n->t('message.hello') 翻译方法
   * @param string $text 待翻译的文本
   * @return string 翻译后的文本
   */
  public function t($text = 'Message.hello', $locale = '')
  {
    if ($locale == '') {
      $locale = $this->locale;
    }
    //如果$locale不来自getLocaleList则返回原文本
    if (!in_array($locale, $this->getLocaleList())) {
      //$locale = $this->fallbackLocale;
      return $text;
    }
    if(is_numeric($text)){
      return $text;
    }
    return $this->translateText($text, $locale);
  }
  /**
   * @param string $text 待查找翻译的文本
   * @param string $locale 查找语言标识符
   */
  private function translateText($text, $locale)
  {
    $texts = [];
    $texts = explode('.', $text);
    $translation = $this->messages[$locale];
    foreach ($texts as $key) {
      if (!is_numeric($key)&&
        isset($translation[$key])) {
        $translation = $translation[$key];
      } else {
        if ($this->fallbackLocale !== null) {
          $fallbackTranslation = $this->messages[$this->fallbackLocale];
          if (isset($fallbackTranslation[$key])) {
            $translation = $fallbackTranslation[$key];
          } else {
            return $text;
          }
        } else {
          return $text;
        }
      }
    }
    return $translation;
  }
  /**
   * 获取语言包列表
   * @return array 语言包['zh_CN','en_US']
   */
  public function getLocaleList()
  {
    $localeList = array();
    foreach ($this->messages as $key => $value) {
      // $localeList[] = $key;
      array_push($localeList, $key);
    }
    return $localeList;
  }
  /**
   * 获取语言包列表对象
   * @return array 语言包['zh_CN'=>self::t('Message.langInfo.langname'),'en_US'=>self::t('Message.langInfo.langname')]
   */
  public function GetLocaleInfoList()
  {
    $localeList = array();
    foreach ($this->messages as $key => $value) {
      $localeList[$key]['Message']['langInfo'] = $this->translateText('Message.langInfo', $key);
    }
    return $localeList;
  }
  /**
   * 确定输入的语言是否存在的函数
   * @param string $locale 语言标识符
   * @return boolean
   */
  public function hasLocale($locale)
  {
    return isset($this->messages[$locale]);
  }
  /**
   * 设置语言
   * @param string $locale 语言标识符
   * @return void
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
}
