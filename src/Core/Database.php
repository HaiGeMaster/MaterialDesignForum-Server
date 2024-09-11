<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://demo.xbedorck.com
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use MaterialDesignForum\Config\Config;

$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => Config::GetMySqlHostname(),
    'database' => Config::GetMySqlDatabase(),
    'username' => Config::GetMySqlUsername(),
    'password' => Config::GetMySqlPassword(),
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix' => Config::GetMySqlPrefix(),
]);
$capsule->bootEloquent();
Config::$sql_is_connect = true;