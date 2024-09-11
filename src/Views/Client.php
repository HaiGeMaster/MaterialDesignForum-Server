<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

namespace MaterialDesignForum\Views;

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Models\Option;
use MaterialDesignForum\Config\Config;
// use MaterialDesignForum\Seo\Seo;

class Client
{
  public static string $lang; //语言 从外部设置
  public static $title;
  public static $description;
  public static $keywords;
  public static $content = '';
  public static $script;
  public static $theme;
  public static function Index()
  {
    $user_token = Share::GetClientUserToken();

    self::$title = Config::GetWebTitleName(i18n::t('Message.Client.Index.WebSubTitle'));
    self::$description = Config::GetWebDescription(i18n::t('Message.Client.Index.WebSubTitle'));
    self::$keywords = Config::GetWebKeywords(i18n::t('Message.Client.Index.WebSubTitle'));

    if (Config::SEO()) {
      $text_players = \MaterialDesignForum\Controllers\Option::GetThemeTypedParamJson($user_token);
      $topics = \MaterialDesignForum\Controllers\Topic::GetTopics('-update_time', 1, null, 10);
      $questions_recent = \MaterialDesignForum\Controllers\Question::GetQuestions('-update_time', 1, false, $user_token, 5);
      $questions_popular = \MaterialDesignForum\Controllers\Question::GetQuestions('-follower_count', 1, false, $user_token, 5);
      $articles_recent = \MaterialDesignForum\Controllers\Article::GetArticles('-update_time', 1, false, $user_token, 5);
      $articles_popular = \MaterialDesignForum\Controllers\Article::GetArticles('-follower_count', 1, false, $user_token, 5);
      $user_recent = \MaterialDesignForum\Controllers\User::GetUsers('-create_time', 1, 'recommended', $user_token, 5);
      $user_popular = \MaterialDesignForum\Controllers\User::GetUsers('-follower_count', 1, 'recommended', $user_token, 5);

      $GLOBALS['G_SEO_DATA'] = [
        'topics' => $topics,
        'questions_recent' => $questions_recent,
        'questions_popular' => $questions_popular,
        'articles_recent' => $articles_recent,
        'articles_popular' => $articles_popular,
        'user_recent' => $user_recent,
        'user_popular' => $user_popular
      ];

      // self::$content = Seo::Render('index');

      self::$script = '
        window.G_INDEX_TEXT_PLAYERS = ' . (Share::HandleArrayToJSON($text_players)) . ';
        window.G_INDEX_TOPICS = ' . (Share::HandleArrayToJSON($topics)) . ';
        window.G_INDEX_QUESTIONS_RECENT = ' . (Share::HandleArrayToJSON($questions_recent)) . ';
        window.G_INDEX_QUESTIONS_POPULAR = ' . (Share::HandleArrayToJSON($questions_popular)) . ';
        window.G_INDEX_ARTICLES_RECENT = ' . (Share::HandleArrayToJSON($articles_recent)) . ';
        window.G_INDEX_ARTICLES_POPULAR = ' . (Share::HandleArrayToJSON($articles_popular)) . ';
        window.G_INDEX_USERS_RECENT = ' . (Share::HandleArrayToJSON($user_recent)) . ';
        window.G_INDEX_USERS_POPULAR = ' . (Share::HandleArrayToJSON($user_popular)) . ';
      ';
    }
    return self::ReturnView();
  }
  public static function Topics()
  {
    $user_token = Share::GetClientUserToken();

    self::$title = Config::GetWebTitleName(i18n::t('Message.Client.Topics.WebSubTitle'));
    self::$description = Config::GetWebDescription(i18n::t('Message.Client.Topics.WebSubTitle'));
    self::$keywords = Config::GetWebKeywords(i18n::t('Message.Client.Topics.WebSubTitle'));

    if (Config::SEO()) {
      $topics_recommended = \MaterialDesignForum\Controllers\Topic::GetTopics('-follower_count', 1, false, $user_token);
      $GLOBALS['G_SEO_DATA'] = [
        'topics_recommended' => $topics_recommended
      ];
      self::$script .= '
        window.G_TOPICS_RECOMMENDED = ' . (Share::HandleArrayToJSON($topics_recommended)) . ';
      ';
    }
    return self::ReturnView();
  }
  public static function Topic($topic_id)
  {
    $user_token = Share::GetClientUserToken();

    $data = \MaterialDesignForum\Controllers\Topic::GetTopic($topic_id, $user_token);
    $topic = $data['topic'];

    self::$title = Config::GetWebTitleName($topic['name'] . ' - ' . i18n::t('Message.Client.Topic.WebSubTitle'));
    self::$description = Config::GetWebDescription(
      $topic->name . ' - ' .
        $topic->description . ' - ' .
        i18n::t('Message.Client.Topic.WebSubTitle')
    );
    self::$keywords = Config::GetWebDescription(
      $topic->name . ' - ' .
        i18n::t('Message.Client.Topic.WebSubTitle')
    );

    if (Config::SEO()) {
      $GLOBALS['G_SEO_DATA'] = [
        'topic' => $data
      ];
      self::$script = '
        window.G_TOPIC = ' . (Share::HandleArrayToJSON($data)) . ';
      ';
    }

    return self::ReturnView();
  }
  public static function Questions()
  {
    $user_token = Share::GetClientUserToken();

    self::$title = Config::GetWebTitleName(i18n::t('Message.Client.Questions.WebSubTitle'));
    self::$description = Config::GetWebDescription(i18n::t('Message.Client.Questions.WebSubTitle'));
    self::$keywords = Config::GetWebKeywords(i18n::t('Message.Client.Questions.WebSubTitle'));

    if (Config::SEO()) {
      $questions_recent = \MaterialDesignForum\Controllers\Question::GetQuestions('-update_time', 1, false, $user_token);
      $questions_popular = \MaterialDesignForum\Controllers\Question::GetQuestions('-follower_count', 1, false, $user_token);
      $GLOBALS['G_SEO_DATA'] = [
        'questions_recent' => $questions_recent,
        'questions_popular' => $questions_popular
      ];
      self::$script = '
        window.G_QUESTIONS_RECENT = ' . (Share::HandleArrayToJSON($questions_recent)) . ';
        window.G_QUESTIONS_POPULAR = ' . (Share::HandleArrayToJSON($questions_popular)) . ';
      ';
    }

    return self::ReturnView();
  }
  public static function Question($question_id)
  {
    $user_token = Share::GetClientUserToken();

    $question = \MaterialDesignForum\Controllers\Question::GetQuestion($question_id, $user_token);
    $question_answers = \MaterialDesignForum\Controllers\Answer::GetAnswers($question_id, '-update_time', 1, $user_token);

    self::$title = Config::GetWebTitleName($question['question']['title'] . ' - ' . i18n::t('Message.Client.Question.WebSubTitle'));
    self::$description = Config::GetWebDescription(
      $question['question']['title'] . ' - ' .
        $question['question']['content'] . ' - ' .
        i18n::t('Message.Client.Question.WebSubTitle')
    );
    self::$keywords = Config::GetWebDescription(
      $question['question']['title'] . ' - ' .
        i18n::t('Message.Client.Question.WebSubTitle')
    );

    if (Config::SEO()) {
      $GLOBALS['G_SEO_DATA'] = [
        'question' => $question,
        'question_answers' => $question_answers
      ];
      self::$script = '
        window.G_QUESTION = ' . (Share::HandleArrayToJSON($question)) . ';
        window.G_QUESTION_ANSWERS = ' . (Share::HandleArrayToJSON($question_answers)) . ';
      ';
    }

    return self::ReturnView();
  }
  public static function Question_And_Answers($question_id, $answer_id = 0)
  {
    $user_token = Share::GetClientUserToken();

    $question = null;
    $question_answers = null;

    $question = \MaterialDesignForum\Controllers\Question::GetQuestion($question_id, $user_token);

    if ($answer_id != 0) {
      $question_answers = \MaterialDesignForum\Controllers\Answer::GetAnswer($answer_id, $user_token);
      if ($question_answers != null) {
        $answer = $question_answers['answer'];
      }
    }

    self::$title = Config::GetWebTitleName($question['question']['title'] . ' - ' . i18n::t('Message.Client.Question.WebSubTitle'));
    self::$description = Config::GetWebDescription(
      $question_answers != null ? $answer['content'] . ' - ' : '' .
        $question['question']['title'] . ' - ' .
        $question['question']['content'] . ' - ' .
        i18n::t('Message.Client.Question.WebSubTitle')
    );
    self::$keywords = Config::GetWebDescription(
      $question_answers != null ? $answer['content'] . ' - ' : '' .
        $question['question']['title'] . ' - ' .
        i18n::t('Message.Client.Question.WebSubTitle')
    );

    if (Config::SEO()) {
      $GLOBALS['G_SEO_DATA'] = [
        'question' => $question,
        'question_answers' => $question_answers
      ];
      self::$script = '
        window.G_QUESTION = ' . (Share::HandleArrayToJSON($question)) . ';
        window.G_QUESTION_ANSWERS = ' . (Share::HandleArrayToJSON($question_answers)) . ';
      ';
    }

    return self::ReturnView();
  }
  public static function Articles()
  {
    $user_token = Share::GetClientUserToken();

    self::$title = Config::GetWebTitleName(i18n::t('Message.Client.Articles.WebSubTitle'));
    self::$description = Config::GetWebDescription(i18n::t('Message.Client.Articles.WebSubTitle'));
    self::$keywords = Config::GetWebKeywords(i18n::t('Message.Client.Articles.WebSubTitle'));

    if (Config::SEO()) {
      $articles_recent = \MaterialDesignForum\Controllers\Article::GetArticles('-update_time', 1, false, $user_token);
      $articles_popular = \MaterialDesignForum\Controllers\Article::GetArticles('-follower_count', 1, false, $user_token);

      $GLOBALS['G_SEO_DATA'] = [
        'articles_recent' => $articles_recent,
        'articles_popular' => $articles_popular
      ];

      self::$script = '
        window.G_ARTICLES_RECENT = ' . (Share::HandleArrayToJSON($articles_recent)) . ';
        window.G_ARTICLES_POPULAR = ' . (Share::HandleArrayToJSON($articles_popular)) . ';
      ';
    }

    return self::ReturnView();
  }
  public static function Article($article_id)
  {
    $user_token = Share::GetClientUserToken();

    $article = \MaterialDesignForum\Controllers\Article::GetArticle($article_id, $user_token);
    $article_comments = \MaterialDesignForum\Controllers\Comment::GetComments($article_id, 'article', '-update_time', 1, $user_token);

    self::$title = Config::GetWebTitleName($article['article']['title'] . ' - ' . i18n::t('Message.Client.Article.WebSubTitle'));
    self::$description = Config::GetWebDescription(
      $article['article']['title'] . ' - ' .
        $article['article']['content'] . ' - ' .
        i18n::t('Message.Client.Article.WebSubTitle')
    );
    self::$keywords = Config::GetWebDescription(
      $article['article']['title'] . ' - ' .
        i18n::t('Message.Client.Article.WebSubTitle')
    );

    if (Config::SEO()) {
      $GLOBALS['G_SEO_DATA'] = [
        'article' => $article,
        'article_comments' => $article_comments
      ];
      self::$script = '
        window.G_ARTICLE = ' . (Share::HandleArrayToJSON($article)) . ';
        window.G_ARTICLE_COMMENTS = ' . (Share::HandleArrayToJSON($article_comments)) . ';
      ';
    }

    return self::ReturnView();
  }
  public static function User($user_id)
  {
    $data = \MaterialDesignForum\Controllers\User::GetUserInfo($user_id);
    $user = $data['user'];

    self::$title = Config::GetWebTitleName(str_replace(
      "{value}",
      $user['username'],
      i18n::t('Message.Client.User.NPersonalHomepage')
    ));
    self::$description = Config::GetWebDescription(
      $user['username'] . ' - ' .
        $user['headline'] . ' - ' .
        i18n::t('Message.Client.User.NPersonalHomepage')
    );
    self::$keywords = Config::GetWebDescription(
      $user['username'] . ' - ' .
        i18n::t('Message.Client.User.NPersonalHomepage')
    );

    if (Config::SEO()) {

      $user_questions = \MaterialDesignForum\Controllers\User::GetUserQuestions($user_id, '-update_time', 1, '', 5);
      $user_answers = \MaterialDesignForum\Controllers\User::GetUserAnswers($user_id, '-update_time', 1, '', 5);
      $user_articles = \MaterialDesignForum\Controllers\User::GetUserArticles($user_id, '-update_time', 1, '', 5);

      $GLOBALS['G_SEO_DATA'] = [
        'user' => $data,
        'user_questions' => $user_questions,
        'user_answers' => $user_answers,
        'user_articles' => $user_articles
      ];

      self::$script = '
        window.G_USER = ' . (Share::HandleArrayToJSON($data)) . ';
        window.G_USER_QUESTIONS = ' . (Share::HandleArrayToJSON($user_questions)) . ';
        window.G_USER_ANSWERS = ' . (Share::HandleArrayToJSON($user_answers)) . ';
        window.G_USER_ARTICLES = ' . (Share::HandleArrayToJSON($user_articles)) . ';
      ';
    }
    return self::ReturnView();
  }
  public static function Users()
  {
    $user_token = Share::GetClientUserToken();


    self::$title = Config::GetWebTitleName(i18n::t('Message.Client.Users.WebSubTitle'));
    self::$description = Config::GetWebDescription(i18n::t('Message.Client.Users.WebSubTitle'));
    self::$keywords = Config::GetWebKeywords(i18n::t('Message.Client.Users.WebSubTitle'));

    if (Config::SEO()) {
      $users_recommended = \MaterialDesignForum\Controllers\User::GetUsers('+user_id', 1, 'recommended', $user_token);

      $GLOBALS['G_SEO_DATA'] = [
        'users_recommended' => $users_recommended
      ];

      self::$script = '
        window.G_USERS_RECOMMENDED = ' . (Share::HandleArrayToJSON($users_recommended)) . ';
      ';
    }
    return self::ReturnView();
  }
  private static function ReturnView()
  {
    self::$theme = Option::Get('theme');
    // self::$description = Config::GetWebDescription();
    // self::$keywords = Config::GetWebKeywords();
    // self::$content = '';
    return Share::HandleThemePage(
      self::$lang,
      self::$title,
      self::$description,
      self::$keywords,
      self::$content,
      self::$script, //Config::SEO()?self::$script:'',//
      self::$theme
    );
  }
}
