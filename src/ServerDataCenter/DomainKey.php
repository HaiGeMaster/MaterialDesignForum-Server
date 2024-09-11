<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */
//请注意 ActivationKey 文件不开放给用户使用，仅供创建者使用

namespace MaterialDesignForum\ServerDataCenter;
use MaterialDesignForum\Plugins\Share;
use MaterialDesignForum\Config\Config;

use Illuminate\Database\Eloquent\Model as Eloquent;

class DomainKey extends Eloquent
{
    protected $table = 'domain_key';
    public $timestamps = false;
    protected $primaryKey = 'index_id';
    protected $fillable = [
        'index_id', // 这个字段不需要
        'renewal_key', //产品续费序列号
        'renewal_days', //能续费的天数
        'renewal_domain', //续费域名
        'renewal_email', //续费者邮箱
        'use_time' //使用时间
    ];
}