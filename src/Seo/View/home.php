<?php 
use MaterialDesignForum\Plugins\i18n;

// $GLOBALS['G_SEO_DATA'] = [
//   'topics' => $topics,
//   'questions_recent' => $questions_recent,
//   'questions_popular' => $questions_popular,
//   'articles_recent' => $articles_recent,
//   'articles_popular' => $articles_popular,
//   'user_recent' => $user_recent,
//   'user_popular' => $user_popular
// ];
?>
<main>
  <div>
    <span><?= i18n::t('Message.Client.Index.LatestQuestions') ?></span>
    <ul>
      <?php foreach ($GLOBALS['G_SEO_DATA']['questions_recent'] as $question) : ?>
        <li>
          <a href="/<?= i18n::i18n()->locale ?>/questions/<?= $question['question_id'] ?>">
            <?= $question['title'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  
  <div>
    <span><?= i18n::t('Message.Client.Index.RecentlyPopularQuestions') ?></span>
    <ul>
      <?php foreach ($GLOBALS['G_SEO_DATA']['questions_popular'] as $question) : ?>
        <li>
          <a href="/<?= i18n::i18n()->locale ?>/questions/<?= $question['question_id'] ?>">
            <?= $question['title'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  
  <div>
    <span><?= i18n::t('Message.Client.Index.LatestArticles') ?></span>
    <ul>
      <?php foreach ($GLOBALS['G_SEO_DATA']['articles_recent'] as $article) : ?>
        <li>
          <a href="/<?= i18n::i18n()->locale ?>/articles/<?= $article['article_id'] ?>">
            <?= $article['title'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  
  <div>
    <span><?= i18n::t('Message.Client.Index.RecentlyPopularArticles') ?></span>
    <ul>
      <?php foreach ($GLOBALS['G_SEO_DATA']['articles_popular'] as $article) : ?>
        <li>
          <a href="/<?= i18n::i18n()->locale ?>/articles/<?= $article['article_id'] ?>">
            <?= $article['title'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  
  <div>
    <span><?= i18n::t('Message.Client.Index.NewlyJoinedPeople') ?></span>
    <ul>
      <?php foreach ($GLOBALS['G_SEO_DATA']['user_recent'] as $user) : ?>
        <li>
          <a href="/<?= i18n::i18n()->locale ?>/users/<?= $user['user_id'] ?>">
            <?= $user['username'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  
  <div>
    <span><?= i18n::t('Message.Client.Index.TheMostPopularPerson') ?></span>
    <ul>
      <?php foreach ($GLOBALS['G_SEO_DATA']['user_popular'] as $user) : ?>
        <li>
          <a href="/<?= i18n::i18n()->locale ?>/users/<?= $user['user_id'] ?>">
            <?= $user['username'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  
</main>