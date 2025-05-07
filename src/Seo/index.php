<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

// require_once './header.php';
// require_once './main.php';
// require_once './footer.php';

namespace MaterialDesignForum\Seo;

class Seo
{
  /**
   * Render 渲染页面
   * @param  string $page_name 页面名称 index、topics、questions、articles、users
   * @return string||false   返回渲染后的SEO页面内容
   */
  public static function Render($page_name)
  {
    ob_start();
    require_once './header.php';
    switch ($page_name) {
      case 'index':
        require_once './View/home.php';
        break;
      case 'topics':
        require_once './View/topics.php';
        break;
      case 'topic':
        require_once './View/topic.php';
        break;
      case 'questions':
        require_once './View/questions.php';
        break;
      case 'question':
        require_once './View/question.php';
        break;
      case 'articles':
        require_once './View/articles.php';
        break;
      case 'article':
        require_once './View/article.php';
        break;
      case 'users':
        require_once './View/users.php';
        break;
      case 'user':
        require_once './View/user.php';
        break;
      default:
        require_once './View/home.php';
        break;
    }
    require_once './footer.php';
    $content = ob_get_clean();
    return $content? $content: '';
  }
}
