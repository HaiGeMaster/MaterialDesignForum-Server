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

class Option extends Eloquent
{
  protected $table = 'option';
  protected $primaryKey = 'name';
  /**
   * @typedef OptionModel 配置项
   * @property string $name 配置项名称
   * @property string $value 配置项值
   */
  protected $fillable = [
    'name', // 这个字段不需要
    'value',
  ];
  public $timestamps = false;
  /**
   * 获取配置项
   * @param string $name 配置项名称
   * @return string|null 配置项值
   */
  public static function Get($name)
  {
    try {
      if ($name == 'site_activation_key') {
        return null;
      }
      $option = self::find($name);
      if ($option) {
        return $option->value;
      } else {
        return null;
      }
    } catch (\Exception $e) {
      return null;
    }
  }
  public static function Set($name, $value)
  {
    $option = self::find($name);
    if ($option) {
      $option->value = $value;
      return $option->save();
    } else {
      $option = new Option;
      $option->name = $name;
      $option->value = $value;
      return $option->save();
    }
  }
}
