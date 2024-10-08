<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 */

namespace MaterialDesignForum\Controllers;

use MaterialDesignForum\Models\Follow as FollowModel;
use MaterialDesignForum\Controllers\User as UserController;
use MaterialDesignForum\Controllers\Token as TokenController;
use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Config\Config;
use MaterialDesignForum\Controllers\Topic as TopicController;
use MaterialDesignForum\Controllers\Question as QuestionController;
use MaterialDesignForum\Controllers\Article as ArticleController;

class Follow extends FollowModel
{
  /**
   * 关注
   * @param int $user 用户ID或token
   * @param string $followable_type 关注类型
   * @param int $followable_id 关注ID
   * @param bool $user_is_token 用户是否是token
   * @return array [is_follow:是否关注 json]
   */
  public static function IsFollow($user, $followable_type, $followable_id, $user_is_token = false): bool
  {
    $user_id = !$user_is_token ? $user : TokenController::GetUserId($user);
    $follow = self::where('user_id', $user_id)->where('followable_type', $followable_type)->where('followable_id', $followable_id)->first();
    if ($follow) {
      return true;
    } else {
      return false;
    }
  }
  /**
   * 关注或取消关注
   * @param int $user_token 用户ID或token
   * @param string $followable_type 关注类型
   * @param int $followable_id 关注ID
   * @return array [is_follow:是否关注 json]
   */
  public static function Follow($user_token, $followable_type, $followable_id): array
  {
    // $user_id = !$user_is_token ? $user : TokenController::GetUserId($user);
    $user_id = TokenController::GetUserId($user_token);
    $follow = self::where('user_id', $user_id)->where('followable_type', $followable_type)->where('followable_id', $followable_id)->first();
    if ($follow) {
      // $delete = self::where('user_id', $user_id)->where('followable_type', $followable_type)->where('followable_id', $followable_id);
      $followable_object = null;
      $is_follow = $follow->delete()||false;
      if ($is_follow) {
        switch ($followable_type) {
          case 'user':
            UserController::SubFollowerCount($followable_id);
            UserController::SubFolloweeCount($user_id);
            $followable_object = UserController::GetUserInfo($followable_id)['user'];
            break;
          case 'topic':
            TopicController::SubFollowerCount($followable_id);
            UserController::SubFollowingTopicCount($user_id);
            $followable_object = TopicController::GetTopic($followable_id, $user_token)['topic'];
            break;
          case 'question':
            QuestionController::SubFollowerCount($followable_id);
            UserController::SubFollowingQuestionCount($user_id);
            $followable_object = QuestionController::GetQuestion($followable_id, $user_token)['question'];
            break;
          case 'article':
            ArticleController::SubFollowerCount($followable_id);
            UserController::SubFollowingArticleCount($user_id);
            $followable_object = ArticleController::GetArticle($followable_id, $user_token)['article'];
            break;
        }
      }
      if($followable_object!=null){
        //更正is_follow状态
        $followable_object['is_follow'] = !$is_follow;
      }
      // $data = array('is_follow' => !$delete);
      $data = [
        'is_follow' => !$is_follow,
        'followable_type' => $followable_type,
        'followable_id' => $followable_id,
        'followable_object' => $followable_object,
      ];
      return $data;
    } else {
      $follow = new self();
      $follow->user_id = $user_id;
      $follow->followable_type = $followable_type;
      $follow->followable_id = $followable_id;
      $follow->create_time = Share::ServerTime();
      $followable_object = null;
      $is_follow = $follow->save()||false;
      if ($is_follow) {
        switch ($followable_type) {
          case 'user':
            UserController::AddFollowerCount($followable_id);
            UserController::AddFolloweeCount($user_id);
            $followable_object = UserController::GetUserInfo($followable_id)['user'];
            break;
          case 'topic':
            TopicController::AddFollowerCount($followable_id);
            UserController::AddFollowingTopicCount($user_id);
            $followable_object = TopicController::GetTopic($followable_id, $user_token)['topic'];
            break;
          case 'question':
            QuestionController::AddFollowerCount($followable_id);
            UserController::AddFollowingQuestionCount($user_id);
            $followable_object = QuestionController::GetQuestion($followable_id, $user_token)['question'];
            break;
          case 'article':
            ArticleController::AddFollowerCount($followable_id);
            UserController::AddFollowingArticleCount($user_id);
            $followable_object = ArticleController::GetArticle($followable_id, $user_token)['article'];
            break;
        }
      }
      if($followable_object!=null){
        //更正is_follow状态
        $followable_object['is_follow'] = $is_follow;
      }
      // $data = array('is_follow' => $follow->save());
      $data = [
        'is_follow' => $is_follow,
        'followable_type' => $followable_type,
        'followable_id' => $followable_id,
        'followable_object' => $followable_object,
      ];
      return $data;
    }
  }
  /**
   * 获取 关注对象 的 N个用户对象的列表 或 获取 用户对象 关注的 N个对象的列表
   * @param string $modes 模式 followers:获取关注对象的关注者 followees:获取用户对象的关注对象
   * @param string $followable_type 关注类型 user，topic，question，articles
   * @param int $followable_id 关注对象ID
   * @param int $page 页数
   * @param string $user_token 用户token
   * @param int $per_page 每页显示数量
   * @param bool $is_admin 是否是管理员
   * @return array||null $[data,pagination] data:用户对象列表 pagination:分页信息
   */
  public static function GetFollows($modes, $followable_type, $followable_id, $page, $user_token = '', $per_page = 20, $is_admin = false)
  {
    // if ($per_page > Config::GetMySQLMaxQuery() || $per_page = '') {
    //   $per_page = Config::GetMySQLMaxQuery();
    // }
    $data = Share::HandleDataAndPagination(null);
    switch ($followable_type) {
      case 'user':
      case 'question':
      case 'article':
        if ($modes == 'followers' && $user_token == '') {
          $data = self::GetFollowersListObject($followable_id, $followable_type, $page, $per_page, null, $is_admin);
        } else if ($modes == 'followers' && $user_token != '') {
          $data = self::GetFollowersListObject($followable_id, $followable_type, $page, $per_page, $user_token, $is_admin);
        } else if ($modes == 'followees' && $user_token == '') {
          $data = self::GetFolloweesListObject($followable_id, $followable_type, $page, $per_page, null, $is_admin);
        } else if ($modes == 'followees' && $user_token != '') {
          $data = self::GetFolloweesListObject($followable_id, $followable_type, $page, $per_page, $user_token, $is_admin);
        }
        break;
      case 'topic':
        if ($modes == 'followers') {
          $data = self::GetFollowersListObject($followable_id, $followable_type, $page, $per_page, $user_token, false);
        }
        break;
    }
    return $data;
  }
  /**
   * 获取 用户对象 关注的 N个对象的列表
   * @param int $user_id 用户对象ID
   * @param string $followable_type 关注类型 user，topic，question，articles
   * @param int $page 页数
   * @param int $per_page 每页显示数量
   * @param bool $user_id_is_token user_id是否是token
   * @return array||null $[data,pagination] data:用户对象列表 pagination:分页信息
   */
  private static function GetFolloweesListObject($user_id, $followable_type, $page = 1, $per_page = 20, $user_token = '', $user_is_admin = false)
  {
    // $user_token = ''; //用户token
    // if ($user_id_is_token) {
    //   $user_token = $user_id;
    //   $user_id = TokenController::GetUserId($user_id);
    // }
    $follow_list = self::where('user_id', '=', $user_id)
      ->where('followable_type', '=', $followable_type)
      ->paginate($per_page, ['*'], 'page', $page);
    $data = null;
    $followable_id_list = array();
    if ($follow_list) {
      $follow_list_data = $follow_list->items();
      foreach ($follow_list_data as $key => $value) {
        array_push($followable_id_list, $value['followable_id']); //关注的对象ID
      }
      switch ($followable_type) {
        case 'user':
          $data = UserController::whereIn('user_id', $followable_id_list)->get();
          foreach ($data as $key => $value) {
            $data[$key] = UserController::GetUserInfo($value['user_id'],$user_token)['user'];
          }
          break;
        case 'topic':
          $data = TopicController::whereIn('topic_id', $followable_id_list)->get();
          foreach ($data as $key => $value) {
            // if ($user_id_is_token && $user_token) {
              $data[$key]['is_follow'] = self::IsFollow($user_token, 'topic', $value['topic_id'], true);
            // }
          }
          break;
        case 'question':
          $data = QuestionController::whereIn('question_id', $followable_id_list)->get();
          foreach ($data as $key => $value) {
            // if ($user_id_is_token && $user_token) {
              $data[$key]['is_follow'] = self::IsFollow($user_token, 'question', $value['question_id'], true);
            // }
          }
          break;
        case 'article':
          $data = ArticleController::whereIn('article_id', $followable_id_list)->get();
          foreach ($data as $key => $value) {
            // if ($user_id_is_token && $user_token) {
              $data[$key]['is_follow'] = self::IsFollow($user_token, 'article', $value['article_id'], true);
            // }
          }
          break;
      }
    }
    return Share::HandleMergeDataAndPagination($data, $follow_list);
  }
  // private static function GetFolloweesListObject($user_id, $followable_type, $page = 1, $per_page = 20, $user_id_is_token = false, $user_is_admin = false)
  // {
  //   $user_token = ''; //用户token
  //   if ($user_id_is_token) {
  //     $user_token = $user_id;
  //     $user_id = TokenController::GetUserId($user_id);
  //   }
  //   $follow_list = self::where('user_id', '=', $user_id)
  //     ->where('followable_type', '=', $followable_type)
  //     ->paginate($per_page, ['*'], 'page', $page);
  //   $data = null;
  //   $followable_id_list = array();
  //   if ($follow_list) {
  //     $follow_list_data = $follow_list->items();
  //     foreach ($follow_list_data as $key => $value) {
  //       array_push($followable_id_list, $value['followable_id']); //关注的对象ID
  //     }
  //     switch ($followable_type) {
  //       case 'user':
  //         $data = UserController::whereIn('user_id', $followable_id_list)->get();
  //         foreach ($data as $key => $value) {
  //           $data[$key] = UserController::GetUserInfo($value['user_id'],$user_token)['user'];
  //         }
  //         break;
  //       case 'topic':
  //         $data = TopicController::whereIn('topic_id', $followable_id_list)->get();
  //         foreach ($data as $key => $value) {
  //           if ($user_id_is_token && $user_token) {
  //             $data[$key]['is_follow'] = self::IsFollow($user_token, 'topic', $value['topic_id'], true);
  //           }
  //         }
  //         break;
  //       case 'question':
  //         $data = QuestionController::whereIn('question_id', $followable_id_list)->get();
  //         foreach ($data as $key => $value) {
  //           if ($user_id_is_token && $user_token) {
  //             $data[$key]['is_follow'] = self::IsFollow($user_token, 'question', $value['question_id'], true);
  //           }
  //         }
  //         break;
  //       case 'article':
  //         $data = ArticleController::whereIn('article_id', $followable_id_list)->get();
  //         foreach ($data as $key => $value) {
  //           if ($user_id_is_token && $user_token) {
  //             $data[$key]['is_follow'] = self::IsFollow($user_token, 'article', $value['article_id'], true);
  //           }
  //         }
  //         break;
  //     }
  //   }
  //   return Share::HandleMergeDataAndPagination($data, $follow_list);
  // }
  /**
   * 获取 被关注的对象 的 N个用户对象的列表
   * @param int $$followable_id 被关注的对象ID
   * @param string $followable_type 关注类型 user，topic，question，articles
   * @param int $page 页数
   * @param int $per_page 每页显示数量
   * @param bool $user_token 请求者用户token
   * @return array||null $[data,pagination] data:用户对象列表 pagination:分页信息
   */
  private static function GetFollowersListObject($followable_id, $followable_type, $page = 1, $per_page = 20, $user_token = '', $user_is_admin = false)
  {
    $follow_list = self::where('followable_id', '=', $followable_id)
      ->where('followable_type', '=', $followable_type)
      ->paginate($per_page, ['*'], 'page', $page);
    $data = null;
    $user_id_list = array();
    if ($follow_list) {
      $follow_list_data = $follow_list->items();
      foreach ($follow_list_data as $key => $value) {
        array_push($user_id_list, $value['user_id']); //关注者ID
      }
      foreach($user_id_list as $key => $value){
        $data[$key] = UserController::GetUserInfo($value,$user_token)['user'];
        // $data[$key]['is_follow'] = self::IsFollow($user_token, 'user', $value, true);
      }
      // $data = UserController::whereIn('user_id', $user_id_list)->get();
      // foreach ($data as $key => $value) {
      //   if ($user_token != '') {
      //     $data[$key]['is_follow'] = self::IsFollow($user_token, 'user', $value['user_id'], true);
      //   }
      //   if (!$user_is_admin) {
      //     $data[$key]['password'] = '';
      //     $data[$key]['email'] = '';
      //   }
      // }
    }
    return Share::HandleMergeDataAndPagination($data, $follow_list);
  }
  /**
   * 获取某用户的互相关注的用户列表 为联系人列表服务
   * @param int $user_token 用户token
   * @param int $page 页数
   * @param int $per_page 每页显示数量
   * @return array||null $[data,pagination] data:用户对象列表 pagination:分页信息
   */
  public static function GetFollowMutualAttentionList($user_token, $page = 1, $per_page = 20)
  {
    //首先获取用户id
    $user_id = TokenController::GetUserId($user_token);
    //获取用户关注的用户id列表
    $followees_id_list = self::where('user_id', '=', $user_id)
      ->where('followable_type', '=', 'user')
      ->paginate($per_page, ['*'], 'page', $page)->pluck('followable_id');
    $data = null;
    //查找$followees_id_list中的用户是否关注了用户$user_id
    $followees_id_list = self::whereIn('user_id', $followees_id_list)
      ->where('followable_type', '=', 'user')
      ->where('followable_id', '=', $user_id)
      ->paginate($per_page, ['*'], 'page', $page);
    $followees_user_list = [];
    if ($followees_id_list) {
      // foreach ($followees_id_list as $value) {
      //   array_push($followees_user_list,UserController::GetUserInfo($value));
      // }
      $followees_id_list_data = $followees_id_list->items();
      foreach ($followees_id_list_data as $key => $value) {
        array_push($followees_user_list, UserController::GetUserInfo($value['user_id'])['user']);
      }
    }
    return Share::HandleMergeDataAndPagination($followees_user_list, $followees_id_list);
  }
}
