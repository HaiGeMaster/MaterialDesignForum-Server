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

use MaterialDesignForum\Config\Install;
use MaterialDesignForum\Config\Config;
use MaterialDesignForum\Plugins\Share;

class Api
{
  public static function HandleAPI()
  {
    $collector = new RouteCollector();
    //Test API↓
    $collector->get('/api/test/GetUserOptionNotificationValue', function () {
      $a = \MaterialDesignForum\Controllers\Notification::GetUserOptionNotificationValue(1);
      print_r($a);
      return;
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Notification::GetUserOptionNotificationValue(1)
      );
    });
    $collector->get('/api/test/GetServerInfo', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Server::GetServerInfo(0)
      );
    });
    //Test API↑
    //仅限创建者使用↓
    $collector->post('/api/c/aa/{domain_name_base64}', function ($domain_name_base64) {
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\ServerDataCenter\DomainData::AddActivity(
          $domain_name_base64
        )
      );
    });
    $collector->post(
      '/api/c/rd/{domain_name_base64}/{renewal_key_base64}',
      function ($domain_name_base64, $renewal_key_base64) {
        $data = Share::GetRequestData();
        return Share::HandleArrayToJSON(
          \MaterialDesignForum\ServerDataCenter\DomainData::RenewalDomain(
            $domain_name_base64,
            $renewal_key_base64,
            $data['renewal_email_base64'],
          )
        );
      }
    );
    //仅限创建者使用↑
    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////

    $collector->post('/api/core/update/check', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Core\Update::checkUpdate($data['user_token'] ?? null)
      );
    });
    $collector->post('/api/core/update/start', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Core\Update::update($data['user_token'] ?? null)
      );
    });
    $collector->get('/api/dev/DEV_AllUserAvatarReset', function () {
      if (!Config::Dev()) {
        return Share::HandleArrayToJSON(
          ['is_reset' => false, 'message' => '!Config::Dev()']
        );
      }
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::DEV_AllUserAvatarReset()
      );
    });
    $collector->get('/api/user/image_captcha/{time}?', function ($time) {
      // $time = Share::GetRequestData()['time'];
      if ($time == null) {
        $time = Share::ServerTime();
      }
      //响应头为图片
      header('Content-Type: image/png');
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetImageCaptcha($time)
      );
    });
    $collector->post('/api/option/set/site_activation_key', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetActivationKey(
          $data['user_token'] ?? '',
          $data['site_activation_key']
        )
      );
    });
    $collector->post('/api/option/get/site_activation_key', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetActivationKey(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get/info', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetInfoData(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/set/info', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetInfoData(
          $data['form_data'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get/mail', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetMailData(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/set/mail', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetMailData(
          $data['form_data'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get/theme', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeData(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get/theme_current', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetCurrentTheme()
      );
    });
    $collector->post('/api/option/set/theme_current', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetCurrentTheme(
          $data['theme_name'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get/all', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetAllOptions(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetOption(
          $data['name'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/options/get/oauth', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetOauthOptions(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/options/set/oauth', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetOauthOptions(
          $data['form_data'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/get/oauth/client_id', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetOauthClientId(
          $data['oauth_name']
        )
      );
    });
    $collector->post('/api/option/set', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetOption(
          $data['name'],
          $data['value'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/set/theme_color_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetThemeColorParamJson(
          $data['user_token'] ?? '',
          $data['json_text']
        )
      );
    });
    $collector->post('/api/option/get/theme_color_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeColorParamJson(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/set/theme_typed_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetThemeTypedParamJson(
          $data['user_token'] ?? '',
          $data['json_text']
        )
      );
    });
    $collector->post('/api/option/get/theme_typed_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeTypedParamJson(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/option/set/theme_carousel_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetThemeCarouselParamJson(
          $data['user_token'] ?? '',
          $data['json_text']
        )
      );
    });
    $collector->post('/api/option/get/theme_carousel_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeCarouselParamJson(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/avatar/reset', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::ResetAvatar(
          $data['user_id'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/cover/reset', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::ResetCover(
          $data['user_id'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/email_captcha', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetEmailCaptcha(
          $data['email'],
          $data['lang']
        )
      );
    });
    $collector->post('/api/user/auto_login', function () {
      $data = Share::GetRequestData()['user_token'];
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::Auto_Login($data)
      );
    });
    $collector->post('/api/user/register', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::AddUser(
          $data['email'],
          $data['password'],
          $data['email_captcha'],
          $data['username'],
          $data['language'] ?? Config::GetWebDefaultLanguage() || 'en_US'
        )
      );
    });
    $collector->post('/api/user/login', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::Login(
          $data['username_or_email'],
          $data['password'],
          $data['image_capthca']
        )
      );
    });
    $collector->post('/api/user/reset', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::Reset(
          $data['email'],
          $data['password'],
          $data['email_captcha']
        )
      );
    });
    $collector->post('/api/user/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUser(
          $data['user_id'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/follow', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Follow::Follow(
          $data['user_token'] ?? '',
          $data['followable_type'],
          $data['followable_id'],
          true
        )
      );
    });
    $collector->post('/api/user/follow/contacts', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Follow::GetFollowMutualAttentionList(
          $data['user_token'] ?? '',
          $data['page'] ?? 1,
          $data['per_page'] ?? Config::GetMySQLMaxQuery()
        )
      );
    });
    $collector->post('/api/user/editinfo', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::EditInfo(
          $data['email'],
          $data['username'],
          $data['user_group_id'],
          $data['headline'],
          $data['blog'],
          $data['company'],
          $data['location'],
          $data['bio'],
          $data['user_token'] ?? '',
          $data['edit_target_user_id']
        )
      );
    });
    $collector->post('/api/user/avatar/upload', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::UploadAvatar(
          $data['user_token'] ?? '',
          $data['avatar']
        )
      );
    });
    $collector->post('/api/user/cover/upload', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::UploadCover(
          $data['user_token'] ?? '',
          $data['cover']
        )
      );
    });
    $collector->post('/api/user/questions/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUserQuestions(
          $data['user_id'],
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/answers/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUserAnswers(
          $data['user_id'],
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/articles/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUserArticles(
          $data['user_id'],
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user/upload/image', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::UploadImage(
          $data['user_token'] ?? '',
          $data['type'],
          $data['image']
        )
      );
    });
    $collector->post('/api/user/notifications/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Notification::GetUserInteractionNotifications(
          $data['user_token'] ?? '',
          $data['order'],
          $data['page'] ?? 1,
          $data['per_page'] ?? Config::GetMySQLMaxQuery()
        )
      );
    });
    $collector->post('/api/user/set/language', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::SetUserLanguage(
          $data['user_token'],
          $data['language']
        )
      );
    });
    $collector->post('/api/users/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUsers(
          $data['order'],
          $data['page'] ?? 1,
          $data['type'],
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
          $data['is_admin'] ?? false
        )
      );
    });
    $collector->post('/api/users/user_group/set', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::SetUsersUserGroup(
          $data['user_token'] ?? '',
          $data['user_group_id'],
          $data['old_user_group_id'] ?? null,
          $data['user_ids'] ?? []
        )
      );
    });
    $collector->post('/api/users/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::SetUsersDisableTime(
          $data['user_token'] ?? '',
          $data['user_ids'],
          $data['disable_time'] ?? 0
        )
      );
    });
    $collector->post('/api/follows/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Follow::GetFollows(
          $data['modes'],
          $data['type'],
          $data['followable_id'],
          $data['page'] ?? 1,
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/topic/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::AddTopic(
          $data['title'],
          $data['description'],
          $data['cover'] ?? '',
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/topic/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::EditTopic(
          $data['topic_id'] ?? null,
          $data['title'],
          $data['description'],
          $data['cover'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/topic/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::GetTopic(
          $data['topic_id'] ?? null,
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/topics/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::GetTopics(
          $data['order'],
          $data['page'] ?? 1,
          $data['following'] ?? false,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? []
        )
      );
    });
    $collector->post('/api/topics/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::DeleteTopics(
          $data['topic_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/question/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::AddQuestion(
          $data['title'],
          $data['topics'],
          $data['content_markdown'],
          $data['content_rendered'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/question/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::GetQuestion(
          $data['question_id'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/question/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::EditQuestion(
          $data['question_id'],
          $data['title'],
          $data['topics'],
          $data['content_markdown'],
          $data['content_rendered'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/questions/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::GetQuestions(
          $data['order'],
          $data['page'] ?? 1,
          $data['following'] ?? false,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
          $data['specify_topic_id'] ?? null
        )
      );
    });
    $collector->post('/api/questions/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::DeleteQuestions(
          $data['question_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/article/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::AddArticle(
          $data['title'],
          $data['topics'],
          $data['content_markdown'],
          $data['content_rendered'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/article/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::GetArticle(
          $data['article_id'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/article/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::EditArticle(
          $data['article_id'],
          $data['title'],
          $data['topics'],
          $data['content_markdown'],
          $data['content_rendered'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/articles/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::GetArticles(
          $data['order'],
          $data['page'] ?? 1,
          $data['following'] ?? false,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
          $data['specify_topic_id'] ?? null
        )
      );
    });
    $collector->post('/api/articles/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::DeleteArticles(
          $data['article_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/answer/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::AddAnswer(
          $data['question_id'],
          $data['content_markdown'],
          $data['content_rendered'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/answer/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::GetAnswer(
          $data['answer_id'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/answer/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::EditAnswer(
          $data['answer_id'],
          $data['content_markdown'],
          $data['content_rendered'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/answers/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::GetAnswers(
          $data['question_id'],
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
        )
      );
    });
    $collector->post('/api/answers/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::DeleteAnswers(
          $data['answer_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/comment/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Comment::AddComment(
          $data['commentable_id'],
          $data['commentable_type'],
          $data['content'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/comment/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Comment::EditComment(
          $data['comment_id'],
          $data['content'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/comments/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Comment::GetComments(
          $data['commentable_id'],
          $data['commentable_type'],
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
        )
      );
    });
    $collector->post('/api/comments/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Comment::DeleteComments(
          $data['comment_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/reply/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Reply::AddReply(
          $data['replyable_id'],
          $data['replyable_type'],
          $data['replyable_comment_id'],
          $data['content'],
          $data['user_token'] ?? '',
          $data['replyable_user_id'],
        )
      );
    });
    $collector->post('/api/reply/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Reply::EditReply(
          $data['reply_id'],
          $data['content'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/replys/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Reply::GetReplys(
          $data['replyable_comment_id'],
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
        )
      );
    });
    $collector->post('/api/replys/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Reply::DeleteReplys(
          $data['reply_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/vote', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Vote::Vote(
          $data['user_token'] ?? '',
          $data['votable_id'],
          $data['votable_type'],
          $data['type']
        )
      );
    });
    $collector->post('/api/admin/data/count', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Server::GetDataCount(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/admin/data/between_timestamps', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Server::GetDataBetweenTimestamps(
          $data['user_token'] ?? '',
          $data['time_type'],
          $data['model_type'],
          // $data['start_timestamp'],
          // $data['end_timestamp']
        )
      );
    });
    $collector->post('/api/admin/data/between_timestamps_all', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Server::GetDataBetweenTimestampsAll(
          $data['user_token'] ?? '',
          $data['time_type'],
          // $data['model_type'],
          // $data['start_timestamp'],
          // $data['end_timestamp']
        )
      );
    });
    $collector->post('/api/admin/data/server_info', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Server::GetServerInfo(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user_group/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::AddUserGroup(
          $data['user_group_data'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user_group/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::GetUserGroup(
          $data['user_group_id'],
          $data['user_token'] ?? '',
        )
      );
    });
    $collector->post('/api/user_groups/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::GetUserGroups(
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
        )
      );
    });
    $collector->post('/api/user_group/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::EditUserGroup(
          $data['user_group_id'],
          $data['user_group_data'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/user_groups/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::DeleteUserGroups(
          $data['user_group_ids'],
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/report/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Report::AddReport(
          $data['reportable_id'],
          $data['reportable_type'],
          $data['user_token'] ?? '',
          $data['reason']
        )
      );
    });
    $collector->post('/api/reports/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Report::GetReports(
          $data['order'],
          $data['page'] ?? 1,
          $data['user_token'] ?? '',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords'] ?? '',
          $data['search_field'] ?? [],
        )
      );
    });
    $collector->post('/api/lang/get/locale/info/list', function () {
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Plugins\i18n::GetLocaleInfoList()
      );
    });
    //回调路由
    $collector->get('/api/oauth/redirect/{oauth_name}', function ($oauth_name) {
      //oauth_name为github、microsoft、sso等平台名称
      //第三方登录完成后需要设置回调到此地址：/api/oauth/redirect/{oauth_name}
      //然后需要获取到code参数，示例：/api/oauth/redirect/github?code=123456

      //获取当前路径?后面的字符
      $str = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
      //将其转换为变量
      parse_str($str, $params); //获得一个数组$params
      $code = $params['code'] ?? null; //成功获取到code
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Plugins\Oauth::ExecuteOAuthFlow(
          $oauth_name,
          $code,
        )
      );
    });
    $collector->get('/api/oauth/go/link/{oauth_name}', function ($oauth_name) {
      return \MaterialDesignForum\Controllers\Option::GetOauthClientLink(
        $oauth_name
      );
    });
    $collector->post('/api/oauths/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Oauth::GetOauths(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/oauth/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Oauth::DeleteOauth(
          $data['user_token'] ?? '',
          $data['oauth_id'] ?? null
        )
      );
    });
    $collector->get('/api/sso/authorize', function () {//由b.a.com调用
      $data = Share::GetRequestData();
      // 接收来自 b.a.com 的 SSO 登录请求，参数包括：
      // client_id：客户端标识（由您分配，比如固定值或数据库存储）
      // redirect_uri：固定为 http://b.a.com/api/oauth/redirect/sso
      // response_type=code：表示使用授权码模式
      // scope=openid profile email：请求用户基础信息

      // 这里你自定义处理逻辑，比如验证 client_id、redirect_uri、scope 等参数

      // 你的后端处理完后，应该携带 code 参数重定向到 redirect_uri
      $client_id = $_GET['client_id'] ?? null;
      $redirect_uri = $_GET['redirect_uri'] ?? null;

      //请注意！！如果你的主站点用户没有登录，必须先跳转登录授权再回来生成code

      // 返回示例：http://b.a.com/api/oauth/redirect/sso?code=123456
      // header('Location: http://b.a.com/api/oauth/redirect/sso?code=123456');
      // header('Location: http://localhost:83/api/oauth/redirect/sso?code=123456');
      // header('Location: ' . $redirect_uri . '?code=123456');

      //echo三秒后重定向
      // echo '三秒后重定向到：' . $redirect_uri . '?code=123456';
      //设置响应类型为html
      header('Content-Type: text/html; charset=utf-8');
      echo '三秒后重定向到：' . $redirect_uri . '?code=123456';
      echo '<script>setTimeout(function(){window.location.href="' . $redirect_uri . '?code=123456";},3000);</script>';
    });
    $collector->post('/api/sso/token', function () {
      $data = Share::GetRequestData();
      // 接收来自 b.a.com 的请求，参数如下：
      // client_id：客户端 ID（如您分配给 b.a.com 的固定值）
      // client_secret：客户端密钥（请妥善保管，建议通过 POST body 传参更安全）
      // code：上一步生成的授权码
      // grant_type=authorization_code：固定值
      // scope=openid profile email：固定值

      $client_id = $data['client_id'] ?? null;
      $client_secret = $data['client_secret'] ?? null;
      $code = $data['code'] ?? null;
      $grant_type = $data['grant_type'] ?? null;
      $scope = $data['scope'] ?? null;

      // $client_id = $_GET['client_id'] ?? null;
      // $client_secret = $_GET['client_secret'] ?? null;
      // $code = $_GET['code'] ?? null;
      // $grant_type = $_GET['grant_type'] ?? null;
      // $scope = $_GET['scope'] ?? null;

      if (
        $client_id == 'test_client_id'
        &&
        $client_secret == 'test_client_secret'
        &&
        $code == '123456'
        &&
        $grant_type == 'authorization_code'
        &&
        $scope == 'openid profile email'
      ) {
        // 您的后端需要：
        // 校验 client_id和 client_secret是否匹配（即 b.a.com 的凭据是否正确）
        // 校验 code是否有效、未过期、未被使用过
        // 若全部校验通过，则签发一个 ​​access_token​​（通常是 JWT 或随机字符串），并返回如下 JSON：
        return Share::HandleArrayToJSON(
          [
            'access_token' => 'test_access_token',
            'token_type' => 'Bearer',
            'scope' => 'openid profile email',
          ]
        );
      }
      return Share::HandleArrayToJSON(
        [
          'access_token' => 'error',
          'token_type' => 'Bearer',
          'scope' => 'openid profile email',
        ]
      );
    });
    $collector->post('/api/sso/user', function () {
      $data = Share::GetRequestData();
      // 该接口用于 b.a.com 获取当前授权用户的详细信息，以实现自动登录或注册绑定。
      // b.a.com 会在请求头中自动携带：
      // Authorization: Bearer ACCESS_TOKEN

      // 您的后端需要：
      // 从 HTTP Header 的 Authorization: Bearer {access_token}中解析出 access_token
      // ​​校验 access_token 是否合法、是否过期、是否被撤销​​
      // 如果 token 合法，则根据该 token 查找到对应的用户身份，返回如下结构：

      $access_token = $data['access_token'] ?? null;

      if ($access_token == 'test_access_token') {
        return Share::HandleArrayToJSON([//这里返回示例的用户
          'id' => 9999,
          'name' => 'test_user',
          'email' => 'test@example.com',
        ]);
      }
      return Share::HandleArrayToJSON([
        'error' => 'access_token_error',
      ]);
    });
    $collector->post('/api/user/option/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserOption::GetUserOption(
          $data['user_token'] ?? '',
          $data['name'],
        )
      );
    });
    $collector->post('/api/user/option/set', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserOption::SetUserOption(
          $data['user_token'] ?? '',
          $data['name'],
          $data['value'],
        )
      );
    });
    $collector->post('/api/user/option/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserOption::DeleteUserOption(
          $data['user_token'] ?? '',
          $data['name'],
        )
      );
    });

    if (!Install::GetInstallInfoJson()["install"]) {
      $collector->post('/api/install/get_install_info_json', function () {
        return Share::HandleArrayToJSON(
          Install::GetInstallInfoJson()
        );
      });
      $collector->post('/api/install/set_config', function () {
        $data = Share::GetRequestData();
        return Share::HandleArrayToJSON(
          Install::SetConfigPHP(
            $data['mysql_hostname'],
            $data['mysql_username'],
            $data['mysql_password'],
            $data['mysql_database'],
            $data['mysql_prefix']
          )
        );
      });
      $collector->post('/api/install/test_mail', function () {
        $data = Share::GetRequestData();
        return Share::HandleArrayToJSON(
          Install::TestMail(
            $data['smtp_email'],
            $data['smtp_username'],
            $data['smtp_password'],
            $data['smtp_send_name'],
            $data['smtp_host'],
            $data['smtp_port'],
            $data['smtp_secure']
          )
        );
      });
      $collector->post('/api/install/mail/set', function () {
        $data = Share::GetRequestData();
        return Share::HandleArrayToJSON(
          Install::SetSqlMail(
            $data['smtp_username'],
            $data['smtp_password'],
            $data['smtp_send_name'],
            $data['smtp_host'],
            $data['smtp_port'],
            $data['smtp_secure']
          )
        );
      });
      $collector->post('/api/install/set_web_info', function () {
        $data = Share::GetRequestData();
        return Share::HandleArrayToJSON(
          Install::SetWebInfo(
            $data['site_name'],
            $data['default_language']
          )
        );
      });
      $collector->post('/api/install/set_web_change', function () {
        return Share::HandleArrayToJSON(
          Install::SetWebInstallChange()
        );
      });
    }

    $dispatcher = new Dispatcher($collector->getData());
    try {
      $request_data =  $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url(
        $_SERVER['REQUEST_URI'] == '' ? '/' : $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
      ));
      Share::SaveRequest($request_data);
      return $request_data;
    } catch (HttpRouteNotFoundException $e) {
      //header("Location: /");
      // echo 'Api Is Not Found,PHP_EOL:' . PHP_EOL . ',$e:' . $e;
      $error = [
        'error' => 'HandleAPI The route or interface is undefined',
        'PHP_EOL' => PHP_EOL,
        'request_uri' => $_SERVER['REQUEST_URI'],
        '$e' => $e
      ];

      //如果是非API请求，返回错误信息
      // if (!self::IsApi()) {
      // return '';//Share::HandleArrayToJSON($error);

      // return $_SERVER['REQUEST_URI'];
      // }

      return Share::HandleArrayToJSON($error);
      exit;
    }
  }
  public static function IsApi()
  {
    return strpos(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 'api') !== false;
  }
  public static function HandleInstallAPI()
  {
    $collector = new RouteCollector();

    $collector->post('/api/lang/get/locale/info/list', function () {
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Plugins\i18n::GetLocaleInfoList()
      );
    });
    $collector->post('/api/option/get/theme_color_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeColorParamJson(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/install/get_install_info_json', function () {
      return Share::HandleArrayToJSON(
        Install::GetInstallInfoJson()
      );
    });
    $collector->post('/api/option/get/info', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetInfoData(
          $data['user_token'] ?? ''
        )
      );
    });
    $collector->post('/api/install/set_config', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        Install::SetConfigPHP(
          $data['mysql_hostname'],
          $data['mysql_username'],
          $data['mysql_password'],
          $data['mysql_database'],
          $data['mysql_prefix']
        )
      );
    });
    $collector->post('/api/install/test_mail', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        Install::TestMail(
          $data['smtp_email'],
          $data['smtp_username'],
          $data['smtp_password'],
          $data['smtp_send_name'],
          $data['smtp_host'],
          $data['smtp_port'],
          $data['smtp_secure']
        )
      );
    });
    $collector->post('/api/install/mail/set', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        Install::SetSqlMail(
          $data['smtp_username'],
          $data['smtp_password'],
          $data['smtp_send_name'],
          $data['smtp_host'],
          $data['smtp_port'],
          $data['smtp_secure']
        )
      );
    });
    $collector->post('/api/install/set_web_info', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        Install::SetWebInfo(
          $data['site_name'],
          $data['default_language']
        )
      );
    });
    $collector->post('/api/install/set_web_change', function () {
      return Share::HandleArrayToJSON(
        Install::SetWebInstallChange()
      );
    });
    $dispatcher = new Dispatcher($collector->getData());
    try {
      $request_data = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url(
        $_SERVER['REQUEST_URI'] == '' ? '/' : $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
      ));
      Share::SaveRequest($request_data);
      return $request_data;
    } catch (HttpRouteNotFoundException $e) {
      header("Location: /install");
      exit;
    }
  }
}
