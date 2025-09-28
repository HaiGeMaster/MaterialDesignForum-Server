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

namespace MaterialDesignForum\Routes;

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Config\Config;
use MaterialDesignForum\Controllers\Image;
use MaterialDesignForum\Views\Client;
use MaterialDesignForum\Views\Admin;
use MaterialDesignForum\Config\Install;

class Page
{
  public static function HandleRoute()
  {
    $collector = new RouteCollector();
    $collector->get('/info', function () {
      return Share::HandleThemePage(
        i18n::i18n()->locale,
        Config::GetWebTitleName(i18n::t('Message.Client.Info.ProductIntroduction')),
        i18n::t('Message.Client.Info.ProductIntroduction') . ' - ' . Config::getConfig()['site_name'],
        i18n::t('Message.Client.Info.ProductIntroduction'),
        i18n::t('Message.Client.Info.ProductIntroduction'),
        '',
        'MaterialDesignForum-Vuetify2'
      );
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/info', function ($lang) {
      return Share::HandleThemePage(
        $lang,
        Config::GetWebTitleName(i18n::t('Message.Client.Info.ProductIntroduction')),
        i18n::t('Message.Client.Info.ProductIntroduction') . ' - ' . Config::getConfig()['site_name'],
        i18n::t('Message.Client.Info.ProductIntroduction'),
        i18n::t('Message.Client.Info.ProductIntroduction'),
        '',
        'MaterialDesignForum-Vuetify2'
      );
    });
    $collector->get('/', function () {
      Client::$lang = i18n::i18n()->locale;
      return Client::Index();
    });
    $collector->get('/topics', function () {
      Client::$lang = i18n::i18n()->locale;
      return Client::Topics();
    });
    $collector->get('/topics/{topic_id}', function ($topic_id) {
      Client::$lang = i18n::i18n()->locale;
      return Client::Topic($topic_id);
    });
    $collector->get('/questions', function () {
      Client::$lang = i18n::i18n()->locale;
      return Client::Questions();
    });
    $collector->get('/questions/{question_id}', function ($question_id) {
      Client::$lang = i18n::i18n()->locale;
      return Client::Question($question_id);
    });
    $collector->get('/questions/{question_id}/answers/{answer_id}', function ($question_id, $answer_id = null) {
      if ($answer_id !== null && $answer_id !== '') {
        Client::$lang = i18n::i18n()->locale;
        return Client::Question_And_Answers($question_id, $answer_id);
      } else {
        Client::$lang = i18n::i18n()->locale;
        return Client::Question($question_id);
      }
    });
    $collector->get('/articles', function () {
      Client::$lang = i18n::i18n()->locale;
      return Client::Articles();
    });
    $collector->get('/articles/{article_id}', function ($article_id) {
      Client::$lang = i18n::i18n()->locale;
      return Client::Article($article_id);
    });
    $collector->get('/users/{user_id}', function ($user_id) {
      Client::$lang = i18n::i18n()->locale;
      return Client::User($user_id);
    });
    $collector->get('/users', function () {
      Client::$lang = i18n::i18n()->locale;
      return Client::Users();
    });
    // $collector->get('/notifications', function () {
    //   return Share::HandleThemePage(
    //     i18n::i18n()->locale,
    //     Config::GetWebTitleName(i18n::t('Message.Client.Notifications.WebSubTitle')),
    //     //i18n::t('Message.Client.Notifications.WebSubTitle') . ' - ' . Config::getConfig()['site_name'],
    //     i18n::t('Message.Client.Notifications.WebSubTitle'),
    //     i18n::t('Message.Client.Notifications.WebSubTitle')
    //   );
    // });
    $collector->get('/admin/{value}?', function ($value = null) {
      Admin::$lang = i18n::i18n()->locale;
      return Admin::Index();
    });
    $collector->get('/admin/{value1}/{value2}?', function ($value1, $value2) {
      Admin::$lang = i18n::i18n()->locale;
      return Admin::Index();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}', function ($lang) {
      Client::$lang = $lang;
      return Client::Index();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/topics', function ($lang) {
      Client::$lang = $lang;
      return Client::Topics();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/topics/{topic_id}', function ($lang, $topic_id) {
      Client::$lang = $lang;
      return Client::Topic($topic_id);
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/questions', function ($lang) {
      Client::$lang = $lang;
      return Client::Questions();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/questions/{question_id}', function ($lang, $question_id) {
      Client::$lang = $lang;
      return Client::Question($question_id);
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/questions/{question_id}/answers/{answer_id}', function ($lang, $question_id, $answer_id = null) {
      if ($answer_id !== null && $answer_id !== '') {
        Client::$lang = $lang;
        return Client::Question_And_Answers($question_id, $answer_id);
      } else {
        Client::$lang = $lang;
        return Client::Question($question_id);
      }
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/articles', function ($lang) {
      Client::$lang = $lang;
      return Client::Articles();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/articles/{article_id}', function ($lang, $article_id) {
      Client::$lang = $lang;
      return Client::Article($article_id);
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/users/{user_id}', function ($lang, $user_id) {
      Client::$lang = $lang;
      return Client::User($user_id);
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/users', function ($lang) {
      Client::$lang = $lang;
      return Client::Users();
    });
    // $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/notifications', function ($lang) {
    //   return Share::HandleThemePage(
    //     $lang,
    //     Config::GetWebTitleName(i18n::t('Message.Client.Notifications.WebSubTitle')),
    //     //i18n::t('Message.Client.Notifications.WebSubTitle') . ' - ' . Config::getConfig()['site_name'],
    //     i18n::t('Message.Client.Notifications.WebSubTitle'),
    //     i18n::t('Message.Client.Notifications.WebSubTitle')
    //   );
    // });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/admin/{value}?', function ($lang, $value = null) {
      Admin::$lang = $lang;
      return Admin::Index();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/admin/{value1}/{value2}?', function ($lang, $value1, $value2) {
      Admin::$lang = $lang;
      return Admin::Index();
    });
    $collector->get('/images/get/{size}/{path}?', function ($size = 0, $path = '') {
      return Image::GetUploadImage($path, $size);
    });
    $collector->get('/language/get/{name}', function ($name) {
      return Share::GetLanguage($name);
    });
    $dispatcher = new Dispatcher($collector->getData());
    try {
      return $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url(
        $_SERVER['REQUEST_URI'] == '' ? '/' : $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
      ));
    } catch (HttpRouteNotFoundException $e) {
      Client::$lang = i18n::i18n()->locale;
      return Client::Index();
      // header("Location: /");
      exit;
    }
  }
  public static function HandleInstallRoute()
  {
    $collector = new RouteCollector();
    $collector->get('/install', function () {
      return Install::InstallView();
    });
    $collector->get('/{lang:[a-z]{2}_[A-Z]{2}}/install', function ($lang) {
      return Install::InstallView($lang);
    });
    $collector->get('/language/get/{name}', function ($name) {
      return Share::GetLanguage($name);
    });
    $dispatcher = new Dispatcher($collector->getData());
    try {
      return $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url(
        $_SERVER['REQUEST_URI'] == '' ? '/' : $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
      ));
    } catch (HttpRouteNotFoundException $e) {
      header("Location: /install");
      exit;
    }

    return 'No install';
  }
}
