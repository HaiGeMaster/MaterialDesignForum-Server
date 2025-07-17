<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Oauth extends Eloquent
{
  protected $table = 'oauth';
  public $timestamps = false;
  protected $primaryKey = 'oauth_id';

  /**
   * @typedef OauthModel 第三方登录模型
   * @property int $oauth_id 主键
   * @property string $oauth_name 第三方平台标识符 如 github, microsoft 等
   * @property string $oauth_user_id 第三方平台用户ID
   * @property string $oauth_user_name 第三方平台用户名
   * @property int $user_id 对应用户ID
   */
  protected $fillable = [
    'oauth_id',
    'oauth_name',
    'oauth_user_id',
    'oauth_user_name',
    'user_id'
  ];
}
