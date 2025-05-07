<?php
/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
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
    // 'charset' => 'utf8',
    // 'collation' => 'utf8_general_ci',
    'prefix' => Config::GetMySqlPrefix(),
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
Config::$sql_is_connect = true;