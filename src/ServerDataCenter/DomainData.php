<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */
//请注意 ActivationKey 文件不开放给用户使用，仅供创建者使用

namespace MaterialDesignForum\ServerDataCenter;

use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Config\Config;
use MaterialDesignForum\ServerDataCenter\DomainKey;

use Illuminate\Database\Eloquent\Model as Eloquent;

class DomainData extends Eloquent
{
  protected $table = 'domain_data';
  public $timestamps = false;
  protected $primaryKey = 'index_id';
  protected $fillable = [
    'index_id', // 这个字段不需要
    'domain_name', //域名
    'first_activity_time', //首次活动时间
    'recent_activity_time', //最近活动时间
    'number_activities', //活动次数
    'allow_use', //允许使用
    'renewal_expiration_date', //续费到期时间 //考虑废弃，开放软件免费使用
    'recent_use_keys', //最近使用的产品序列号 //考虑废弃，开放软件免费使用
    'allow_use_langpack' //允许使用的语言包
  ];

  protected $casts = [
    'allow_use' => 'boolean',
    'allow_use_langpack' => 'array'
  ];

  /**
   * 添加域名活动记录
   * @param string $domain_name_base64 域名
   * @return {v:bool,t:time} v返回假则域名已经被封禁，不允许使用。t返回续费到期时间
   */
  public static function AddActivity($domain_name_base64)//这个函数是客户端自动调用的
  {
    $domain_name = base64_decode($domain_name_base64);
    $item = self::where('domain_name', '=', $domain_name)->first();
    $v = false;
    $t = 0;
    $l = [];
    if ($item == null) {
      $item = new self();
      $item->domain_name = $domain_name;
      $item->first_activity_time = Share::ServerTime();
      $item->recent_activity_time = Share::ServerTime();
      $item->number_activities = 1;
      $item->allow_use = true;
      //renewal_expiration_date默认设置为 现在时间+3天试用期
      $item->renewal_expiration_date = Share::ServerTime() + 3 * 24 * 60 * 60;
      $item->allow_use_langpack = ['zh_CN'];//默认设置为中文和英文
      $item->save();
      $v = $item->allow_use;
      $t = $item->renewal_expiration_date;
      $l = $item->allow_use_langpack;
    } else {
      $item->recent_activity_time = Share::ServerTime();
      $item->number_activities += 1;
      if($item->allow_use_langpack==null){
        $item->allow_use_langpack = ['zh_CN'];//默认设置为中文和英文
      }
      $item->save();
      $v = $item->allow_use;
      $t = $item->renewal_expiration_date;
      $l = $item->allow_use_langpack;
    }
    return [
      // 'v' => base64_encode(1),//($v),
      // 't' => base64_encode(43017420813),//($t),
      // 'l' => base64_encode(json_encode(["zh_CN","en_US","zh_TW","en_GB","ru_RU","fr_FR","de_DE","ja_JP","ko_KR"])),//($l)),
      'v' => base64_encode(1),
      't' => base64_encode(43017420813),
      'l' => base64_encode(json_encode($l)),
    ];
  }
  /**
   * 续费域名
   * @param string $domain_name_base64 域名
   * @param string $renewal_key_base64 续费序列号
   * @param string $renewal_email_base64 续费者邮箱
   * @return {v:bool,t:time} v返回假则续费失败，t返回续费到期时间
   */
  public static function RenewalDomain(
    $domain_name_base64, 
    $renewal_key_base64, 
    $renewal_email_base64
  )
  {
    $domain_name = base64_decode($domain_name_base64);
    $renewal_key = base64_decode($renewal_key_base64);
    $renewal_email = base64_decode($renewal_email_base64);
    //从DomainKey中查找renewal_key=renewal_key 且 renewal_domain=null 且 use_time=0 的记录
    $item = DomainKey::where('renewal_key', '=', $renewal_key)
      ->where('renewal_domain', '=', '')
      ->where('use_time', '=', 0)
      ->first();
    $v = false;
    $t = 0;
    $l = [];
    if ($item != null) {
      //开始续费
      $item->renewal_domain = $domain_name;
      $item->renewal_email = $renewal_email;
      $item->use_time = Share::ServerTime();
      if($item->save()){
        //获取能续费的天数
        $renewal_days = $item->renewal_days;
        //从DomainData中查找domain_name=domain_name的记录
        $domain = self::where('domain_name', '=', $domain_name)->first();
        if ($domain != null) {
          //续费到期时间=当前$domain中的renewal_expiration_date+renewal_days*24*60*60
          $domain->renewal_expiration_date = $domain->renewal_expiration_date + $renewal_days * 24 * 60 * 60;
          $domain->recent_use_keys = $renewal_key;
          if($domain->save()){
            $v = $domain->allow_use;
            $t = $domain->renewal_expiration_date;
            $l = $domain->allow_use_langpack;
          }
        }
      }
    }else{
      //续费失败，返回查询其的原数据
      $domain = self::where('domain_name', '=', $domain_name)->first();
      if ($domain != null) {
        $v = $domain->allow_use;
        $t = $domain->renewal_expiration_date;
        $l = $domain->allow_use_langpack;
      }
    }
    return [
      'v' => base64_encode($v),
      't' => base64_encode($t),
      'l' => base64_encode(json_encode($l)),
      // 'v' => $v,
      // 't' => $t,
    ];
  }
}
