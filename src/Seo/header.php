<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

use MaterialDesignForum\Plugins\i18n;
use MaterialDesignForum\Config\Config;
?>
<header>
  <div class="logo"><img src="./favicon.svg" alt="<?= Config::GetWebSiteName() ?>"></div>
  <nav>
    <ul>
      <?php foreach (i18n::getLocaleList() as $locale) : ?>
        <li>
          <a href="<?= '/' . $locale ?>/">
            <?= i18n::t('Message.Components.DrawerNavigation.Home', $locale) ?>
          </a>
        </li>
        <li>
          <a href="<?= '/' . $locale ?>/topics">
            <?= i18n::t('Message.Components.DrawerNavigation.Topics', $locale) ?>
          </a>
        </li>
        <li>
          <a href="<?= '/' . $locale ?>/questions">
            <?= i18n::t('Message.Components.DrawerNavigation.Questions', $locale) ?>
          </a>
        </li>
        <li>
          <a href="<?= '/' . $locale ?>/articles">
            <?= i18n::t('Message.Components.DrawerNavigation.Articles', $locale) ?>
          </a>
        </li>
        <li>
          <a href="<?= '/' . $locale ?>/users">
            <?= i18n::t('Message.Components.DrawerNavigation.Users', $locale) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <!-- <div class="search">顶部右边可能会放一个搜索框之类的</div> -->
</header>