<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Option extends Eloquent
{
  protected $table = 'option';
  protected $primaryKey = 'name';
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
