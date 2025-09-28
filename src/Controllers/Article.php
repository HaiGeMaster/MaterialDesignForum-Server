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

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Article as ArticleModel;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Controllers\UserGroup as UserGroupController;
use MaterialDesignForum\Controllers\TopicAble as TopicAbleController;
use MaterialDesignForum\Controllers\Topic as TopicController;
use MaterialDesignForum\Controllers\Follow as FollowController;
use MaterialDesignForum\Controllers\Vote as VoteController;
use MaterialDesignForum\Controllers\Comment as CommentController;
use MaterialDesignForum\Controllers\Reply as ReplyController;
use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Controllers\Notification as NotificationController;
// use MaterialDesignForum\Config\Config;

use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Models\Follow;

class Article extends ArticleModel
{
  /**
   * 获取文章所有者用户id
   * @param int $article_id 文章ID
   * @return int|null 用户ID
   */
  public static function GetArticleOwnerId($article_id)
  {
    $article = self::find($article_id);
    if ($article != null) {
      return $article->user_id;
    }
    return null;
  }
  /**
   * 添加文章
   * @param string $title 标题
   * @param array $topics 话题ID数组
   * @param string $content_markdown 纯文本
   * @param string $content_rendered 渲染后的HTML
   * @param string $user_token 用户Token
   * @return array 
   */
  public static function AddArticle($title, $topics, $content_markdown, $content_rendered, $user_token)
  {
    $is_valid_content =
      $title != null &&
      $topics != null &&
      $content_markdown != null &&
      $content_rendered != null &&
      $user_token != '' &&
      $title != '' &&
      $topics != [] &&
      $content_markdown != '' &&
      $content_rendered != '' &&
      $user_token != '';
    $is_add = false;
    $article_id = null;
    $user_id = TokenController::GetUserId($user_token);
    if (
      $user_id != null
      && $is_valid_content
      && (
        UserGroupController::Ability($user_token, 'ability_create_article') ||
        UserGroupController::IsAdmin($user_token)
      )
    ) {
      $content_markdown = preg_replace('/\s+/', '', $content_markdown); //去除回车和空格

      $article = new ArticleModel;
      $article->user_id = $user_id;
      $article->title = $title;
      $article->content_markdown = $content_markdown;
      $article->content_rendered = $content_rendered;
      $article->create_time = Share::ServerTime();
      $article->update_time = Share::ServerTime();
      $is_add = $article->save();
      if ($is_add) {
        UserController::AddArticleCount($user_id);
        $following_id_array = FollowController::GetFollowingObjectUserIds('user', $user_id);
        if ($following_id_array != null) {
          //遍历$following_id_array数组，为每个用户添加关注的提问更新通知
          foreach ($following_id_array as $key => $value) {
            NotificationController::AddInteractionNotification(
              $value,
              $user_id,
              'follow_user_update',
              null,
              null,
              0,
              0,
              $article->article_id
            );
          }
        }
      }
      foreach ($topics as $topic_id) {
        if (TopicAbleController::AddTopicAble($topic_id, $article->article_id, 'article')) {
          TopicController::AddArticleCount($topic_id);
        }
      }
      //根据$topics查询话题的关注者
      $follower_id_array = FollowController::where('followable_type', '=', 'topic')
      ->whereIn('followable_id', $topics)
      ->get()->pluck('user_id')->toArray();
      if($follower_id_array != null){
        //遍历$follower_id_array数组，为每个用户添加关注的提问更新通知
        foreach ($follower_id_array as $key => $value) {
          NotificationController::AddInteractionNotification(
            $value,
            $user_id,
            'follow_topic_update',
            null,
            null,
            0,
            $topic_id,
            $article->article_id,
          );
        }
      }

      $article_id = $article->article_id;
    }
    return [
      'is_add' => $is_add,
      'article' => self::GetArticle($article_id, $user_token)['article'],
      // 'article_id' => $article_id
    ];
  }
  /**
   * 获取文章
   * @param int $question_id 问题ID
   * @return array is_get:是否获取 article:文章信息
   */
  public static function GetArticle($article_id, $user_token = '')
  {
    $article = self::where('article_id', '=', $article_id)->where('delete_time', '=', 0)->first();
    if ($article) {
      $article->topics = TopicController::GetAblesTopic($article_id, 'article');
      $article->user = UserController::GetUserInfo($article->user_id, $user_token)['user'];
      $article->is_follow = FollowController::IsFollow($user_token, 'article', $article_id, true);
      $article->vote = VoteController::GetVote($article_id, 'article', $user_token)['vote'];
    }
    return [
      'is_get' => $article != null,
      'article' => $article,
    ];
  }
  /**
   * 获取文章列表
   * @param string $order 排序
   * @param int $page 页数
   * @param bool $following 是否获取关注的文章
   * @param int $user_token 用户token
   * @param int $per_page 每页数量
   * @param string $search_keywords 搜索关键词 不可与$specify_topic_id同时使用
   * @param array $search_field 搜索字段
   * @param int $specify_topic_id 指定ID 不可与$search_keywords同时使用
   * @return array is_get:是否获取 data:文章列表
   */
  public static function GetArticles(
    $order,
    $page,
    $following = false,
    $user_token = '',
    $per_page = 20,
    $search_keywords = '',
    $search_field = [],
    $specify_topic_id = ''
  ) {
    if ($search_field == []) {
      $search_field = self::$search_field;
    }

    $data = Share::HandleDataAndPagination(null);
    $orders = Share::HandleArrayField($order);

    $field = $orders['field'];
    $sort = $orders['sort'];
    if ($following == 'false' || $following == false) {
      if ($specify_topic_id != '') {
        $article_ids = TopicAbleController::where('topic_id', '=', $specify_topic_id)
          ->where('topicable_type', '=', 'article')
          ->pluck('topicable_id'); //获取指定话题下的所有文章id
        $data = self::where('delete_time', '=', 0)
          ->whereIn('article_id', $article_ids) // 确保传递的是数组
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      } else if ($search_keywords != '') {
        $data = self::where('delete_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      } else {
        $data = self::where('delete_time', '=', 0)
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page);
      }
      $data = Share::HandleDataAndPagination($data);
    } else if ($following == 'true' || $following == true) {
      $user_id = TokenController::GetUserId($user_token);
      $following_id_object = FollowController::where('user_id', '=', $user_id)->where('followable_type', '=', 'article')->paginate($per_page, ['*'], 'page', $page);
      $following_id_array = [];
      foreach ($following_id_object->items() as $key => $value) {
        array_push($following_id_array, $value->followable_id);
      }
      if ($specify_topic_id != '') {
        $article_ids = TopicAbleController::where('topic_id', '=', $specify_topic_id)
          ->where('topicable_type', '=', 'article')
          ->pluck('topicable_id'); //获取指定话题下的所有文章id
        //将指定话题下的文章id与关注的文章id取交集，找出这两个数组中共同的元素，并将其存储到新的数组中
        $following_id_array = array_intersect($following_id_array, $article_ids->toArray());
        $data = self::where('delete_time', '=', 0)
          ->whereIn(
            'article_id',
            $following_id_array
          )
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page)
          ->items();
      } else if ($search_keywords != '') {
        $data = self::where('delete_time', '=', 0)
          //->where($search_field, 'like', '%' . $search_keywords . '%')
          ->where(function ($query) use ($search_field, $search_keywords) {
            foreach ($search_field as $key => $value) {
              $query->orWhere($value, 'like', '%' . $search_keywords . '%');
            }
          })
          ->whereIn(
            'article_id',
            $following_id_array
          )
          ->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page)
          ->items();
      } else {
        $data = self::where('delete_time', '=', 0)->whereIn(
          'article_id',
          $following_id_array
        )->orderBy($field, $sort)
          ->paginate($per_page, ['*'], 'page', $page)
          ->items();
      }
      $data = Share::HandleMergeDataAndPagination($data, $following_id_object);
    }
    if ($data['data'] != null) {
      foreach ($data['data'] as $key => $value) {
        $data['data'][$key]->topics = TopicController::GetAblesTopic($value->article_id, 'article');
        $data['data'][$key]->user = UserController::GetUserInfo($value->user_id, $user_token)['user'];
        $data['data'][$key]->is_follow = FollowController::IsFollow($user_token, 'article', $value->article_id, true);
        $data['data'][$key]->vote = VoteController::GetVote($value->article_id, 'article', $user_token)['vote'];
      }
    }
    return $data;
  }
  /**
   * 编辑文章
   * @param int $article_id 文章ID
   * @param string $title 标题
   * @param array $topics 话题ID数组
   * @param string $content_markdown 纯文本
   * @param string $content_rendered 渲染后的HTML
   * @param string $user_token 用户Token
   * @return
   */
  public static function EditArticle($article_id, $title, $topics, $content_markdown, $content_rendered, $user_token)
  {
    $is_valid_content =
      $article_id != null &&
      $title != null &&
      $topics != null &&
      $content_markdown != null &&
      $content_rendered != null &&
      $user_token != '' &&
      $article_id != '' &&
      $title != '' &&
      $topics != [] &&
      $content_markdown != '' &&
      $content_rendered != '' &&
      $user_token != '';
    $is_edit = false;
    $user_id = TokenController::GetUserId($user_token);
    $article = self::where('article_id', '=', $article_id)
      ->where('delete_time', '=', 0)
      ->first();
    if ($article != null && $is_valid_content && $user_id != null) {
      if (
        (
          TokenController::IsUserSelf($user_token, $article->user_id) &&
          UserGroupController::Ability($user_token, 'ability_edit_own_article') &&
          (
            UserGroupController::Ability($user_token, 'ability_edit_article_only_no_comment') ? ($article->comment_count == 0 ? true : false) : true
          ) &&
          UserGroupController::BeforeTime($user_token, 'time_before_edit_article', $article->create_time)
        )
        ||
        (UserGroupController::IsAdmin($user_token) && UserGroupController::Ability($user_token, 'ability_admin_manage_article'))
        // UserGroupController::IsAdmin($user_token)
      ) {
        $content_markdown = preg_replace('/\s+/', '', $content_markdown); //去除回车和空格

        $article->title = $title;
        $article->content_markdown = $content_markdown;
        $article->content_rendered = $content_rendered;
        $article->update_time = Share::ServerTime();
        $is_edit = $article->save();
        if ($is_edit) {
          // NotificationController::AddInteractionNotification()
          //从关注关系中获取所有关注此文章的用户id
          $following_id_array = FollowController::GetFollowingObjectUserIds('article', $article_id);
          if ($following_id_array != null) {
            //遍历$following_id_array数组，为每个用户添加关注的提问更新通知
            foreach ($following_id_array as $key => $value) {
              NotificationController::AddInteractionNotification(
                $value,
                $article->user_id,
                'follow_article_update',
                null,
                null,
                0,
                0,
                $article_id
              );
            }
          }
        }
        // TopicAbleController::where('topicable_id', '=', $article_id)->where('topicable_type', '=', 'article')->delete();

        //首先从TopicAbleController获取所有的topicable_id为$article_id的数据的topic_id数组
        $old_topics = TopicController::GetAblesTopic($article_id, 'article');
        // return $old_topics;
        // $old_topic_ids = [];
        if ($old_topics != null) {
          foreach ($old_topics as $old_topic) {
            TopicAbleController::DeleteTopicAble($old_topic->topic_id, $article_id, 'article');
            TopicController::SubArticleCount($old_topic->topic_id);
          }
        }

        foreach ($topics as $topic_id) {
          TopicAbleController::AddTopicAble($topic_id, $article->article_id, 'article');
          TopicController::AddArticleCount($topic_id);
        }
      }
    }
    return [
      'is_edit' => $is_edit,
      'article' => self::GetArticle($article_id, $user_token)['article'],
    ];
  }
  /**
   * 删除文章
   * @param array $article_ids 文章ID数组
   * @param string $user_token 用户token
   * @return array is_delete:是否删除
   */
  public static function DeleteArticles($article_ids, $user_token)
  {
    $is_valid_content =
      $article_ids != null &&
      $user_token != '' &&
      $article_ids != [] &&
      $user_token != '';
    $is_delete = false;
    $user_id = TokenController::GetUserId($user_token);
    $delete_ids = [];
    $articles = [];
    if (
      $user_id != null &&
      $is_valid_content
    ) {
      $articles = self::whereIn('article_id', $article_ids)->get();
      foreach ($articles as $key => $article) {
        if (
          (
            TokenController::IsUserSelf($user_token, $article->user_id) &&
            UserGroupController::Ability($user_token, 'ability_delete_own_article') &&
            (
              UserGroupController::Ability($user_token, 'ability_delete_article_only_no_comment') ? ($article->reply_count == 0 ? true : false) : true
            ) &&
            UserGroupController::BeforeTime($user_token, 'time_before_delete_article', $article->create_time)
          )
          ||
          (UserGroupController::IsAdmin($user_token) && UserGroupController::Ability($user_token, 'ability_admin_manage_article'))
          // UserGroupController::IsAdmin($user_token)
        ) {
          UserController::SubArticleCount($article->user_id);
          TopicController::SubArticleCount($article->topics);
          NotificationController::AddInteractionNotification(
            $article->user_id,
            $user_id,
            'article_delete',
            null,
            null,
            0,
            0,
            $article->article_id,
            0,
            0,
            0,
            0
          );

          //联动删除此文章下的所有评论和回复
          //将文章下的所有评论删除
          // $comments = CommentController::where('commentable_id', '=', $article->article_id)
          //   ->where('commentable_type', '=', 'article')
          //   ->get();
          // if($comments!=null){
          //   foreach ($comments as $key => $comment) {

          //     //将评论下的所有回复删除
          //     $replys = ReplyController::where('replyable_comment_id', '=', $comment->comment_id)
          //     ->get();
          //     if($replys!=null){
          //       foreach ($replys as $key => $reply) {
          //         $reply->delete_time = Share::ServerTime();
          //         $reply->save();

          //         //从用户的回复数中减去1
          //         UserController::SubReplyCount($reply->user_id);
          //       }
          //     }

          //     $comment->delete_time = Share::ServerTime();
          //     $comment->save();

          //     //从用户的评论数中减去1
          //     UserController::SubCommentCount($comment->user_id);
          //   }
          // }

          //减少对应话题的文章数量
          $topics = TopicController::GetAblesTopic($article->article_id, 'article');
          if ($topics != null) {
            foreach ($topics as $topic) {
              TopicController::SubArticleCount($topic->topic_id);
            }
          }
          //减少用户的文章数量
          UserController::SubArticleCount($article->user_id);
          //删除文章
          $article->delete_time = Share::ServerTime();

          $is_delete = $article->save();
          array_push($delete_ids, $article->article_id);
        }
      }
    }
    return [
      'is_delete' => $is_delete,
      'delete_ids' => $delete_ids,
      'data' => $articles,
    ];
    // if (
    //   $user_id != null
    //   && $is_valid_content
    //   && (
    //     UserGroupController::Ability($user_token, 'ability_delete_article') ||
    //     UserGroupController::IsAdmin($user_token)
    //   )
    // ) {
    //   $is_delete = self::whereIn('article_id', $article_ids)->update(['delete_time' => Share::ServerTime()]);
    // }
    // return [
    //   'is_delete' => $is_delete,
    // ];
  }
}
