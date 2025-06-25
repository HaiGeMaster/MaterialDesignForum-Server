<?php

/**
 * Author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster
 * @copyright Copyright (c) 2023 HaiGeMaster
 * @start-date 2023/07/03-16:17:41
 */

namespace MaterialDesignForum\Controllers;

use Md\MDAvatars;
// use Intervention\Image\ImageManager;
use MaterialDesignForum\Models\Image as ImageModel;
use MaterialDesignForum\Plugins\Share;

class Image extends ImageModel
{
  public static $pathData = [
    'user_avatar' => [
      'path' => 'public/static/upload/user/avatars/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => [512, 512],
        'small' => [64, 64],
        'middle' => [128, 128],
        'large' => [256, 256],
      ],
    ],
    'user_cover' => [
      'path' => 'public/static/upload/user/covers/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => null,
        'small' => [600, 336],
        'middle' => [1050, 588],
        'large' => [1450, 812],
      ],
    ],
    'user_avatar_default' => [
      'path' => 'public/static/upload/user/avatars/default/',
      'needDeleteOld' => false,
      'sizeArray' => [
        'original' => [512, 512],
        'small' => [64, 64],
        'middle' => [128, 128],
        'large' => [256, 256],
      ],
    ],
    'topic_cover' => [
      'path' => 'public/static/upload/topic/covers/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => null,
        'small' => [360, 202],
        'middle' => [720, 404],
        'large' => [1080, 606],
      ],
    ],
    'other' => [
      'path' => 'public/static/upload/other/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => null,
      ],
    ],
    'question' => [
      'path' => 'public/static/upload/question/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => null,
      ],
    ],
    'article' => [
      'path' => 'public/static/upload/article/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => null,
      ],
    ],
    'answer' => [
      'path' => 'public/static/upload/answer/',
      'needDeleteOld' => true,
      'sizeArray' => [
        'original' => null,
      ],
    ],
  ];
  /**
   * 旧版本-保存图片
   * @param string $type 图片类型 user_avatar、user_cover、topic_cover、other、question、article、answer
   * @param string $base64data base64 图片数据
   * @param string $user_id 用户id
   * @return array 图片路径 [original,small,middle,large] || other、question、article、answer：[original]
   */
  // public static function SaveUploadImage($type, $base64data, $user_id = 'cache')
  // {
  //   $path = '';
  //   $sizeArray = null;


  //   // 'path' => 'public/static/upload/xxx'
  //   // 'sizeArray' => [
  //   //   'original' => null,
  //   //   'small' => [360, 202],
  //   //   'middle' => [720, 404],
  //   //   'large' => [1080, 606],
  //   // ],

  //   // 检查路径和尺寸数据是否存在
  //   if (isset(self::$pathData[$type])) {
  //     $path = self::$pathData[$type]['path'] . $user_id;
  //     $sizeArray = self::$pathData[$type]['sizeArray'];
  //   } else {
  //     return false;
  //   }

  //   // 获取并解码图片的base64数据
  //   $img = str_replace('data:image/png;base64,', '', $base64data);
  //   $img = str_replace(' ', '+', $img);
  //   $data = base64_decode($img);

  //   if (!$data) {
  //     return false; // 如果Base64解码失败
  //   }

  //   // 生成唯一的图片文件名
  //   $file_name = md5(microtime(true));

  //   // 初始化返回的数据
  //   $AvatarData = [
  //     'original' => '',
  //     'small' => '',
  //     'middle' => '',
  //     'large' => '',
  //   ];

  //   // 检查并创建保存路径
  //   if (!is_dir($path)) {
  //     mkdir($path, 0777, true);
  //   }

  //   // 本地存储图片路径
  //   $locale_img = $path . '/' . $file_name . '.png';

  //   // 写入原始图片
  //   $r = file_put_contents($locale_img, $data);
  //   if (!$r) {
  //     return false; // 如果写入失败
  //   }

  //   try {
  //     foreach ($sizeArray as $key => $size) {
  //       error_reporting(E_ALL & ~E_WARNING);
  //       ini_set('memory_limit', '256M');
  //       // 读取图片并生成不同大小的图片
  //       $image = imagecreatefrompng($locale_img);

  //       if (!$image) {
  //         continue; // 如果读取失败，跳过
  //       }

  //       if ($size == null) {
  //         // 如果没有指定大小，直接保存原图
  //         $paths = $path . '/' . $key;
  //         if (!is_dir($paths)) {
  //           mkdir($paths, 0777, true);
  //         }
  //         $png = $paths . '/' . $file_name . '.png';
  //         imagepng($image, $png);
  //         $AvatarData[$key] = '/' . $png;
  //       } else {
  //         // 如果指定了大小，创建新的图像并调整大小
  //         $new_image = imagecreatetruecolor($size[0], $size[1]);

  //         // 保证透明背景
  //         imagealphablending($new_image, false);
  //         imagesavealpha($new_image, true);
  //         $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
  //         imagefill($new_image, 0, 0, $transparent);

  //         $source_width = imagesx($image);
  //         $source_height = imagesy($image);
  //         imagecopyresampled($new_image, $image, 0, 0, 0, 0, $size[0], $size[1], $source_width - 4, $source_height);

  //         $paths = $path . '/' . $key;
  //         if (!is_dir($paths)) {
  //           mkdir($paths, 0777, true);
  //         }

  //         $png = $paths . '/' . $file_name . '.png';
  //         imagepng($new_image, $png);
  //         $AvatarData[$key] = '/' . $png;

  //         // 释放内存
  //         imagedestroy($new_image);
  //       }

  //       // 释放内存
  //       imagedestroy($image);
  //     }
  //   } catch (\Exception $e) {
  //     // 捕获异常并记录错误
  //     error_log($e->getMessage());
  //     return false;
  //   }

  //   // 删除临时文件
  //   unlink($locale_img);

  //   return $AvatarData;
  // }
  // /**
  //  * 保存图片
  //  * @param string $type 图片类型 user_avatar、user_cover、topic_cover、other、question、article、answer
  //  * @param string $base64data base64 图片数据
  //  * @param string $user_id 用户id
  //  * @return array 图片路径 [original,small,middle,large] || other、question、article、answer：[original]
  //  */
  // public static function SaveUploadImage($type, $base64data, $user_id = 'cache')
  // {
  //   $path = '';
  //   $sizeArray = null;

  //   // 检查路径和尺寸数据是否存在
  //   if (isset(self::$pathData[$type])) {
  //     $path = self::$pathData[$type]['path'] . $user_id;
  //     $sizeArray = self::$pathData[$type]['sizeArray'];
  //   } else {
  //     return false;
  //   }

  //   // 获取并解码图片的base64数据
  //   $img = str_replace('data:image/png;base64,', '', $base64data);
  //   $img = str_replace('data:image/jpeg;base64,', '', $img);
  //   $img = str_replace('data:image/jpg;base64,', '', $img);
  //   $img = str_replace(' ', '+', $img);
  //   $data = base64_decode($img);

  //   if (!$data) {
  //     return false; // 如果Base64解码失败
  //   }

  //   // 生成唯一的图片文件名
  //   $file_name = md5(microtime(true));

  //   // 初始化返回的数据
  //   $AvatarData = [
  //     'original' => '',
  //     'small' => '',
  //     'middle' => '',
  //     'large' => '',
  //   ];

  //   // 检查并创建保存路径
  //   if (!is_dir($path)) {
  //     mkdir($path, 0777, true);
  //   }

  //   // 检测图片类型
  //   $imageInfo = getimagesizefromstring($data);
  //   if ($imageInfo === false) {
  //     return false; // 不是有效的图片数据
  //   }

  //   $mime = $imageInfo['mime'];
  //   $extension = '';

  //   switch ($mime) {
  //     case 'image/jpeg':
  //     case 'image/jpg':
  //       $extension = 'jpg';
  //       break;
  //     case 'image/png':
  //       $extension = 'png';
  //       break;
  //     default:
  //       return false; // 不支持的图片格式
  //   }

  //   // 本地存储图片路径
  //   $locale_img = $path . '/' . $file_name . '.' . $extension;

  //   // 写入原始图片
  //   $r = file_put_contents($locale_img, $data);
  //   if (!$r) {
  //     return false; // 如果写入失败
  //   }

  //   try {
  //     foreach ($sizeArray as $key => $size) {
  //       error_reporting(E_ALL & ~E_WARNING);
  //       ini_set('memory_limit', '256M');
  //       // 根据图片类型选择不同的读取函数
  //       $image = null;
  //       switch ($mime) {
  //         case 'image/jpeg':
  //         case 'image/jpg':
  //           $image = imagecreatefromjpeg($locale_img);
  //           break;
  //         case 'image/png':
  //           $image = imagecreatefrompng($locale_img);
  //           // 启用 alpha 通道（透明度）
  //           imagealphablending($image, false); // 关闭混合模式
  //           imagesavealpha($image, true);      // 保存 alpha 通道
  //           break;
  //       }

  //       if (!$image) {
  //         continue; // 如果读取失败，跳过
  //       }

  //       if ($size == null) {
  //         // 如果没有指定大小，直接保存原图
  //         $paths = $path . '/' . $key;
  //         if (!is_dir($paths)) {
  //           mkdir($paths, 0777, true);
  //         }
  //         $save_img = $paths . '/' . $file_name . '.' . $extension;
  //         switch ($mime) {
  //           case 'image/jpeg':
  //           case 'image/jpg':
  //             imagejpeg($image, $save_img);
  //             break;
  //           case 'image/png':
  //             // 保存 PNG 时确保 alpha 通道不丢失
  //             imagepng($image, $save_img);
  //             break;
  //         }
  //         $AvatarData[$key] = '/' . $save_img;
  //       } else {
  //         // 如果指定了大小，创建新的图像并调整大小
  //         $new_image = imagecreatetruecolor($size[0], $size[1]);

  //         if ($mime == 'image/png') {
  //           // 对于 PNG 图片，启用 alpha 通道
  //           imagealphablending($new_image, false); // 关闭混合模式
  //           imagesavealpha($new_image, true);      // 保存 alpha 通道
  //           $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
  //           imagefill($new_image, 0, 0, $transparent);
  //         } else {
  //           // 对于 JPEG 图片，填充白色背景（因为 JPEG 不支持透明）
  //           $white = imagecolorallocate($new_image, 255, 255, 255);
  //           imagefill($new_image, 0, 0, $white);
  //         }

  //         $source_width = imagesx($image);
  //         $source_height = imagesy($image);
  //         // 修正了图像缩放计算（原代码有误）
  //         imagecopyresampled(
  //           $new_image,
  //           $image,
  //           0,
  //           0,
  //           0,
  //           0,
  //           $size[0],
  //           $size[1],
  //           $source_width,
  //           $source_height
  //         );

  //         $paths = $path . '/' . $key;
  //         if (!is_dir($paths)) {
  //           mkdir($paths, 0777, true);
  //         }

  //         $save_img = $paths . '/' . $file_name . '.' . $extension;
  //         switch ($mime) {
  //           case 'image/jpeg':
  //           case 'image/jpg':
  //             imagejpeg($new_image, $save_img);
  //             break;
  //           case 'image/png':
  //             imagepng($new_image, $save_img);
  //             break;
  //         }
  //         $AvatarData[$key] = '/' . $save_img;

  //         // 释放内存
  //         imagedestroy($new_image);
  //       }

  //       // 释放内存
  //       imagedestroy($image);
  //     }
  //   } catch (\Exception $e) {
  //     // 捕获异常并记录错误
  //     error_log($e->getMessage());
  //     return false;
  //   }

  //   // 删除临时文件
  //   unlink($locale_img);

  //   return $AvatarData;
  // }
  /**
   * 保存图片
   * @param string $type 图片类型 user_avatar、user_cover、topic_cover、other、question、article、answer
   * @param string $base64data base64 图片数据
   * @param string $user_id 用户id
   * @return array 图片路径 [original,small,middle,large] || other、question、article、answer：[original]
   */
  public static function SaveUploadImage($type, $base64data, $user_id = 'cache')
  {
    $path = '';
    $sizeArray = null;

    // 检查路径和尺寸数据是否存在
    if (isset(self::$pathData[$type])) {
      $path = self::$pathData[$type]['path'] . $user_id;
      $sizeArray = self::$pathData[$type]['sizeArray'];
    } else {
      return false;
    }

    // 获取并解码图片的base64数据
    $img = str_replace('data:image/png;base64,', '', $base64data);
    $img = str_replace('data:image/jpeg;base64,', '', $img);
    $img = str_replace('data:image/jpg;base64,', '', $img);
    $img = str_replace('data:image/gif;base64,', '', $img); // 添加GIF的base64前缀处理
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);

    if (!$data) {
      return false; // 如果Base64解码失败
    }

    // 生成唯一的图片文件名
    $file_name = md5(microtime(true));

    // 初始化返回的数据
    $AvatarData = [
      'original' => '',
      'small' => '',
      'middle' => '',
      'large' => '',
    ];

    // 检查并创建保存路径
    if (!is_dir($path)) {
      mkdir($path, 0777, true);
    }

    // 检测图片类型
    $imageInfo = getimagesizefromstring($data);
    if ($imageInfo === false) {
      return false; // 不是有效的图片数据
    }

    $mime = $imageInfo['mime'];
    $extension = '';

    switch ($mime) {
      case 'image/jpeg':
      case 'image/jpg':
        $extension = 'jpg';
        break;
      case 'image/png':
        $extension = 'png';
        break;
      case 'image/gif':
        $extension = 'gif';
        break;
      default:
        return false; // 不支持的图片格式
    }

    // 本地存储图片路径
    $locale_img = $path . '/' . $file_name . '.' . $extension;

    // 写入原始图片
    $r = file_put_contents($locale_img, $data);
    if (!$r) {
      return false; // 如果写入失败
    }

    try {
      foreach ($sizeArray as $key => $size) {
        error_reporting(E_ALL & ~E_WARNING);
        ini_set('memory_limit', '256M');
        // 根据图片类型选择不同的读取函数
        $image = null;
        switch ($mime) {
          case 'image/jpeg':
          case 'image/jpg':
            $image = imagecreatefromjpeg($locale_img);
            break;
          case 'image/png':
            $image = imagecreatefrompng($locale_img);
            // 启用 alpha 通道（透明度）
            imagealphablending($image, false); // 关闭混合模式
            imagesavealpha($image, true);      // 保存 alpha 通道
            break;
          case 'image/gif':
            $image = imagecreatefromgif($locale_img);
            // GIF可能包含透明通道，需要保留
            if ($image && imagecolortransparent($image) != -1) {
              // 如果有透明色，保留透明通道
              imagealphablending($image, false);
              imagesavealpha($image, true);
            }
            break;
        }

        if (!$image) {
          continue; // 如果读取失败，跳过
        }

        if ($size == null) {
          // 如果没有指定大小，直接保存原图
          $paths = $path . '/' . $key;
          if (!is_dir($paths)) {
            mkdir($paths, 0777, true);
          }
          $save_img = $paths . '/' . $file_name . '.' . $extension;
          switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
              imagejpeg($image, $save_img);
              break;
            case 'image/png':
              // 保存 PNG 时确保 alpha 通道不丢失
              imagepng($image, $save_img);
              break;
            case 'image/gif':
              imagegif($image, $save_img);
              break;
          }
          $AvatarData[$key] = '/' . $save_img;
        } else {
          // 如果指定了大小，创建新的图像并调整大小
          $new_image = imagecreatetruecolor($size[0], $size[1]);

          if ($mime == 'image/png' || $mime == 'image/gif') {
            // 对于 PNG 和 GIF 图片，启用 alpha 通道
            imagealphablending($new_image, false); // 关闭混合模式
            imagesavealpha($new_image, true);      // 保存 alpha 通道
            $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
            imagefill($new_image, 0, 0, $transparent);
          } else {
            // 对于 JPEG 图片，填充白色背景（因为 JPEG 不支持透明）
            $white = imagecolorallocate($new_image, 255, 255, 255);
            imagefill($new_image, 0, 0, $white);
          }

          $source_width = imagesx($image);
          $source_height = imagesy($image);
          // 修正了图像缩放计算（原代码有误）
          imagecopyresampled(
            $new_image,
            $image,
            0,
            0,
            0,
            0,
            $size[0],
            $size[1],
            $source_width,
            $source_height
          );

          $paths = $path . '/' . $key;
          if (!is_dir($paths)) {
            mkdir($paths, 0777, true);
          }

          $save_img = $paths . '/' . $file_name . '.' . $extension;
          switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
              imagejpeg($new_image, $save_img);
              break;
            case 'image/png':
              imagepng($new_image, $save_img);
              break;
            case 'image/gif':
              imagegif($new_image, $save_img);
              break;
          }
          $AvatarData[$key] = '/' . $save_img;

          // 释放内存
          imagedestroy($new_image);
        }

        // 释放内存
        imagedestroy($image);
      }
    } catch (\Exception $e) {
      // 捕获异常并记录错误
      error_log($e->getMessage());
      return false;
    }

    // 删除临时文件
    unlink($locale_img);

    return $AvatarData;
  }
  /**
   * 创建用户默认头像
   * @param string $name 用户名
   * @param string $user_id 用户id
   * @return array 图片路径[original,small,middle,large]
   */
  public static function CreateUserDefaultAvatar($name, $user_id = 'cache')
  {
    // Generate user_id if it's not provided
    if ($user_id == 'cache') {
      $user_id = 'cache_' . md5($name) . '_' . Share::ServerTime();
    }

    $file_name = md5(microtime(true)); // 图片名称
    $Avatar = new MDAvatars($name, 512);
    $AvatarData = [];

    foreach (self::$pathData['user_avatar_default']['sizeArray'] as $key => $size) {
      $path = self::$pathData['user_avatar_default']['path'] . $user_id . '/' . $key;

      // 创建路径
      if (!file_exists($path)) {
        mkdir($path, 0777, true);
      }

      $png = $path . '/' . $file_name . '.png';
      if ($Avatar->Save($png, $size[0])) {
        $AvatarData[$key] = '/' . $png;
      }
    }

    $Avatar->Free();
    return $AvatarData;
  }
  /**
   * 创建用户默认封面
   * @return array 图片路径[original,small,middle,large]
   */
  public static function CreateUserDefaultCover()
  {
    $pathArr = [
      'original' => '/public/static/default/user/covers/1/original/default.png',
      'small' => '/public/static/default/user/covers/1/small/default.png',
      'middle' => '/public/static/default/user/covers/1/middle/default.png',
      'large' => '/public/static/default/user/covers/1/large/default.png',
    ];
    return $pathArr;
  }
  public static function GetUploadImage($path, $size = 'original')
  {
    $images_path = './.' . base64_decode($path);
    if ($size != 0) {
      $images_path = str_replace('{size}', $size, $images_path);
    }
    //从本地获取图片，根据图片路径
    $image = file_get_contents($images_path); //假设/static/xxx/xxx/1.jpg是已存在的图片
    header('Content-Type: image/png'); //设置头信息
    echo $image; //输出图片
  }
  /**
   * 删除上传的图片
   * @param array $pathArr 图片路径数组
   * @return bool 是否删除成功
   */
  public static function DeleteUploadImage($pathArr)
  {
    $original = $pathArr['original'];
    $small = $pathArr['small'];
    $middle = $pathArr['middle'];
    $large = $pathArr['large'];
    //将$original中的/public替换成public
    $original = str_replace('/public', 'public', $original);
    $small = str_replace('/public', 'public', $small);
    $middle = str_replace('/public', 'public', $middle);
    $large = str_replace('/public', 'public', $large);


    //如果包含default，那么直接返回true
    if (strpos($original, 'default') !== false || strpos($small, 'default') !== false || strpos($middle, 'default') !== false || strpos($large, 'default') !== false) {
      return true;
    }

    //如果文件都不存在，那么直接返回true
    if (!file_exists($original) && !file_exists($small) && !file_exists($middle) && !file_exists($large)) {
      return true;
    }
    try {
      if (unlink($original) && unlink($small) && unlink($middle) && unlink($large)) {
        //删除文件和文件夹
        $dir = dirname($original);
        if (is_dir($dir) && count(glob("$dir/*")) === 0) {
          rmdir($dir);
        }
        $dir = dirname($small);
        if (is_dir($dir) && count(glob("$dir/*")) === 0) {
          rmdir($dir);
        }
        $dir = dirname($middle);
        if (is_dir($dir) && count(glob("$dir/*")) === 0) {
          rmdir($dir);
        }
        $dir = dirname($large);
        if (is_dir($dir) && count(glob("$dir/*")) === 0) {
          rmdir($dir);
          //然后删除上一级文件夹
          // $dir = dirname($dir);
          // if (is_dir($dir) && count(glob("$dir/*")) === 0) {
          //   rmdir($dir);
          // }
        }
        return true;
      } else {
        return false;
      }
    } catch (\Exception $e) {
      //不发出错误
      return true;
    }
  }
  /**
   * 添加图片记录到数据库
   * @param string $type 图片类型 question、article、answer
   * @param string $item_id 问题id、文章id、回答id
   * @param string $user_id 用户id
   * @param string $url 图片路径
   * @return bool 是否添加成功
   */
  public static function AddImageRecord($type, $item_id, $user_id, $url)
  {
    $image = new self();
    $image->key = md5($url);
    $image->filename = $url;
    $image->width = 0;
    $image->height = 0;
    $image->create_time = Share::ServerTime();
    $image->item_type = $type;
    $image->item_id = $item_id;
    $image->user_id = $user_id;
    return $image->save();
  }
  /**
   * 根据$url查找数据并给item_id赋值
   * @param string $url 图片路径
   * @param string $item_id 问题id、文章id、回答id
   * @return bool 是否添加成功
   */
  public static function UpdateImageItemID($url, $item_id)
  {
    $image = self::where('filename', $url)->first();
    if ($image) {
      $image->item_id = $item_id;
      return $image->save();
    }
    return false;
  }
}
