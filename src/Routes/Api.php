<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Routes;

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Config\Config;
use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use MaterialDesignForum\Config\Install;

class Api
{
  public static function HandleAPI()
  {
    $collector = new RouteCollector();
    //Test API↓
    $collector->get('/api/test/Client_AddInbox', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Inbox::Client_AddInbox(
          'dc6db7e87bfb32206b7af2b782ad51fa',
          'user_to_user',
          2,
          '测试内容'.Share::ServerTime(),
          '测试内容'.Share::ServerTime()
        )
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
    $collector->post('/api/c/rd/{domain_name_base64}/{renewal_key_base64}', 
    function ($domain_name_base64, $renewal_key_base64) {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\ServerDataCenter\DomainData::RenewalDomain(
          $domain_name_base64,
          $renewal_key_base64,
          $data['renewal_email_base64'],
        )
      );
    });
    //仅限创建者使用↑
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
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetImageCaptcha($time)
      );
    });
    $collector->post('/api/option/{name}', function ($name) {
      if (strpos($name, 'site_') !== false) {
        return Share::HandleArrayToJSON(
          ['value' => \MaterialDesignForum\Models\Option::Get($name)]
        );
      } else {
        return '';
      }
    });
    // $collector->post('/api/option/set/site_activation_key', function () {
    //   $data = Share::GetRequestData();
    //   return Share::HandleArrayToJSON(
    //     \MaterialDesignForum\Controllers\Option::SetActivationKey(
    //       $data['user_token']??'',
    //       $data['site_activation_key']
    //     )
    //   );
    // });
    // $collector->post('/api/option/get/site_activation_key', function () {
    //   $data = Share::GetRequestData();
    //   return Share::HandleArrayToJSON(
    //     \MaterialDesignForum\Controllers\Option::GetActivationKey(
    //       $data['user_token']??''
    //     )
    //   );
    // });
    $collector->post('/api/options/info/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetInfoData(
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/options/info/set', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetInfoData(
          $data['form_data'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/options/mail/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetMailData(
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/options/mail/set', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetMailData(
          $data['form_data'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/options/theme/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeData(
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/options/theme/get/current', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetCurrentTheme()
      );
    });
    $collector->post('/api/options/theme/set/current', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetCurrentTheme(
          $data['theme_name'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user/avatar/reset', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::ResetAvatar(
          $data['user_id'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user/cover/reset', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::ResetCover(
          $data['user_id'],
          $data['user_token']??''
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
          $data['language']??Config::GetWebDefaultLanguage()||'en_US'
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user/follow', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Follow::Follow(
          $data['user_token']??'',
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
          $data['user_token']??'',
          $data['page']??1,
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
          $data['user_token']??'',
          $data['edit_target_user_id']
        )
      );
    });
    $collector->post('/api/user/avatar/upload', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::UploadAvatar(
          $data['user_token']??'',
          $data['avatar']
        )
      );
    });
    $collector->post('/api/user/cover/upload', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::UploadCover(
          $data['user_token']??'',
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
          $data['page']??1,
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user/answers/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUserAnswers(
          $data['user_id'],
          $data['order'],
          $data['page']??1,
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user/articles/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUserArticles(
          $data['user_id'],
          $data['order'],
          $data['page']??1,
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/users/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::GetUsers(
          $data['order'],
          $data['page']??1,
          $data['type'],
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
          $data['is_admin']??false
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
          $data['page']??1,
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/topic/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::AddTopic(
          $data['title'],
          $data['description'],
          $data['cover'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/topic/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::EditTopic(
          $data['topic_id']??null,
          $data['title'],
          $data['description'],
          $data['cover'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/topic/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::GetTopic(
          $data['topic_id']??null,
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/topics/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::GetTopics(
          $data['order'],
          $data['page']??1,
          $data['following']??false,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[]
        )
      );
    });
    $collector->post('/api/topics/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Topic::DeleteTopics(
          $data['topic_ids'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/question/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::GetQuestion(
          $data['question_id'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/questions/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::GetQuestions(
          $data['order'],
          $data['page']??1,
          $data['following']??false,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
          $data['specify_topic_id']??null
        )
      );
    });
    $collector->post('/api/questions/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Question::DeleteQuestions(
          $data['question_ids'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/article/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::GetArticle(
          $data['article_id'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/articles/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::GetArticles(
          $data['order'],
          $data['page']??1,
          $data['following']??false,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['specify_topic_id']??null
        )
      );
    });
    $collector->post('/api/articles/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Article::DeleteArticles(
          $data['article_ids'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/answer/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::GetAnswer(
          $data['answer_id'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/answers/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::GetAnswers(
          $data['question_id'],
          $data['order'],
          $data['page']??1,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
        )
      );
    });
    $collector->post('/api/answers/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Answer::DeleteAnswers(
          $data['answer_ids'],
          $data['user_token']??''
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/comment/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Comment::EditComment(
          $data['comment_id'],
          $data['content'],
          $data['user_token']??''
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
          $data['page']??1,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
        )
      );
    });
    $collector->post('/api/comments/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Comment::DeleteComments(
          $data['comment_ids'],
          $data['user_token']??''
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
          $data['user_token']??'',
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/replys/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Reply::GetReplys(
          $data['replyable_comment_id'],
          $data['order'],
          $data['page']??1,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
        )
      );
    });
    $collector->post('/api/replys/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Reply::DeleteReplys(
          $data['reply_ids'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/vote', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Vote::Vote(
          $data['user_token']??'',
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
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/admin/data/between_timestamps', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Server::GetDataBetweenTimestamps(
          $data['user_token']??'',
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
          $data['user_token']??'',
          $data['time_type'],
          // $data['model_type'],
          // $data['start_timestamp'],
          // $data['end_timestamp']
        )
      );
    });
    $collector->post('/api/user_group/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::AddUserGroup(
          $data['user_group_data'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user_group/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::GetUserGroup(
          $data['user_group_id'],
          $data['user_token']??'',
        )
      );
    });
    $collector->post('/api/user_groups/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::GetUserGroups(
          $data['order'],
          $data['page']??1,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
        )
      );
    });
    $collector->post('/api/user_group/edit', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::EditUserGroup(
          $data['user_group_id'],
          $data['user_group_data'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user_groups/delete', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\UserGroup::DeleteUserGroups(
          $data['user_group_ids'],
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/report/add', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Report::AddReport(
          $data['reportable_id'],
          $data['reportable_type'],
          $data['user_token']??'',
          $data['reason']
        )
      );
    });
    $collector->post('/api/reports/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Report::GetReports(
          $data['order'],
          $data['page']??1,
          $data['user_token']??'',
          $data['per_page'] ?? Config::GetMySQLMaxQuery(),
          $data['search_keywords']??'',
          $data['search_field']??[],
        )
      );
    });
    $collector->post('/api/lang/get/locale/info/list', function () {
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Plugins\i18n::GetLocaleInfoList()
      );
    });
    $collector->post('/api/user/upload/image', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\User::UploadImage(
          $data['user_token']??'',
          $data['type'],
          $data['image']
        )
      );
    });
    $collector->post('/api/option/set/theme_color_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetThemeColorParamJson(
          $data['user_token']??'',
          $data['json_text']
        )
      );
    });
    $collector->post('/api/option/get/theme_color_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeColorParamJson(
          $data['user_token']??''
        )
      );
    });
    
    $collector->post('/api/option/set/theme_typed_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::SetThemeTypedParamJson(
          $data['user_token']??'',
          $data['json_text']
        )
      );
    });
    $collector->post('/api/option/get/theme_typed_param', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Option::GetThemeTypedParamJson(
          $data['user_token']??''
        )
      );
    });
    $collector->post('/api/user/notifications/get', function () {
      $data = Share::GetRequestData();
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Controllers\Notification::GetUserNotifications(
          $data['user_token']??'',
          $data['order'],
          $data['page']??1,
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
        ));
    });
    $dispatcher = new Dispatcher($collector->getData());
    try {
      return $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url(
        $_SERVER['REQUEST_URI'] == '' ? '/' : $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
      ));
    } catch (HttpRouteNotFoundException $e) {
      //header("Location: /");
      echo 'Api Is Not Found,PHP_EOL:' . PHP_EOL . ',$e:' . $e;
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

    $collector->post('/api/lang/get/locale/Info/list', function () {
      return Share::HandleArrayToJSON(
        \MaterialDesignForum\Plugins\i18n::GetLocaleInfoList()
      );
    });
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
  }
}
