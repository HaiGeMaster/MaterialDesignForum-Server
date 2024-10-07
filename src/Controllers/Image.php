<?php
/**
 * author HaiGeMaster
 * @package MaterialDesignForum
 * @link https://github.com/HaiGeMaster/MaterialDesignForum-Server
 */

namespace MaterialDesignForum\Controllers;

use Md\MDAvatars;
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
   * 保存图片
   * @param string $type 图片类型 user_avatar、user_cover、topic_cover、other、question、article、answer
   * @param string $base64data base64数据
   * @param string $user_id 用户id
   * @return array 图片路径 [original,small,middle,large] || other、question、article、answer：[original]
   */
  public static function SaveUploadImage($type, $base64data, $user_id = 'cache')
  {
    $path = '';
    $sizeArray = null;

    if (isset(self::$pathData[$type])) {
      $path = self::$pathData[$type]['path'] . $user_id;
      $sizeArray = self::$pathData[$type]['sizeArray'];
    } else {
      return false;
    }

    $img = $base64data; // 获取base64
    $img = str_replace('data:image/png;base64,', '', $img); //获取base64中图片数据
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);

    $file_name = md5(microtime(true)); // 图片名称

    $AvatarData = [
      'original' => '',
      'small' => '',
      'middle' => '',
      'large' => '',
    ];
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }

    $locale_img = $path . '/' . $file_name . '.png';

    $r = file_put_contents($locale_img, $data); // 先写出图片
    // 也可以使用 -1 来达到相同的效果 不显示任何错误
    // error_reporting(-1);

    try{

      if ($r) {
        // foreach ($sizeArray as $key => $size) {
        //   //读取图片，并生成不同分辨率的图片，不使用MDAvatars
        //   // 屏蔽警告
        //   error_reporting(E_ERROR | E_PARSE);
        //   $image = imagecreatefrompng($locale_img);
        //   // 恢复默认错误报告级别
        //   error_reporting(E_ALL);
        //   if ($size == null) {
        //     //直接保存
        //     $paths = $path . '/' . $key;
        //     if (!file_exists($paths)) {
        //       mkdir($paths, 0777, true);
        //     }
        //     $png = $path . '/' . $key . '/' . $file_name . '.png'; //public/static/upload/user/avatars/user_id/key/xxx.png
        //     imagepng($image, $png);
        //     //$AvatarData[$key] = $png;
        //     $AvatarData[$key] = '/' . $png;
        //   } else {
        //     $new_image = imagecreatetruecolor($size[0], $size[1]);
        //     $source_width = imagesx($image); // 获取原始图像的宽度
        //     $source_height = imagesy($image); // 获取原始图像的高度
        //     imagecopyresampled($new_image, $image, 0, 0, 0, 0, $size[0], $size[1], $source_width, $source_height);
        //     $paths = $path . '/' . $key;
        //     if (!file_exists($paths)) {
        //       mkdir($paths, 0777, true);
        //     }
        //     $png = $path . '/' . $key . '/' . $file_name . '.png'; //public/static/upload/user/avatars/user_id/key/xxx.png
        //     imagepng($new_image, $png);
        //     //$AvatarData[$key] = $png;
        //     $AvatarData[$key] = '/' . $png;
        //   }
        //   if($type=='question'||$type=='article'||$type=='answer'){

        //   }
        // }
        foreach ($sizeArray as $key => $size) {
          // 读取图片，并生成不同分辨率的图片，不使用MDAvatars
          // 屏蔽警告
          error_reporting(E_ERROR | E_PARSE);
          $image = imagecreatefrompng($locale_img);
          // 恢复默认错误报告级别
          error_reporting(E_ALL);
      
          if ($size == null) {
              // 直接保存
              $paths = $path . '/' . $key;
              if (!file_exists($paths)) {
                  mkdir($paths, 0777, true);
              }
              $png = $path . '/' . $key . '/' . $file_name . '.png'; // public/static/upload/user/avatars/user_id/key/xxx.png
              imagepng($image, $png);
              $AvatarData[$key] = '/' . $png;
          } else {
              $new_image = imagecreatetruecolor($size[0], $size[1]);//创建一个真彩色图像
      
              // 确保新图像的背景透明
              imagealphablending($new_image, false);//关闭混色模式
              imagesavealpha($new_image, true);//设置保存PNG时保留透明通道信息
              $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);//为一幅图像分配颜色
              imagefill($new_image, 0, 0, $transparent);//填充
      
              $source_width = imagesx($image); // 获取原始图像的宽度
              $source_height = imagesy($image); // 获取原始图像的高度
              imagecopyresampled($new_image, $image, 0, 0, 0, 0, $size[0], $size[1], $source_width-4, $source_height);//重采样拷贝部分图像并调整大小

              //如果原始大小和目标大小一样，那么直接拷贝
              // if($source_width==$size[0]&&$source_height==$size[1]){
              //   imagecopy($new_image, $image, 0, 0, 0, 0, $size[0], $size[1]);
              // }else{
              //   imagecopyresampled($new_image, $image, 0, 0, 0, 0, $size[0], $size[1], $source_width-2, $source_height-2);//重采样拷贝部分图像并调整大小
              // }

              // //检查是否存在透明色
              // $color = imagecolorat($new_image, 0, 0);
              // $colors = imagecolorsforindex($new_image, $color);
              // if ($colors['alpha'] == 127) {
              //     //如果存在透明色，那么将透明色设置为白色
              //     $white = imagecolorallocate($new_image, 255, 255, 255);
              //     imagecolortransparent($new_image, $white);
              // }
              
              $paths = $path . '/' . $key;
              if (!file_exists($paths)) {
                  mkdir($paths, 0777, true);
              }
              $png = $path . '/' . $key . '/' . $file_name . '.png'; // public/static/upload/user/avatars/user_id/key/xxx.png
              imagepng($new_image, $png);
              $AvatarData[$key] = '/' . $png;
      
              // 释放内存
              imagedestroy($new_image);
          }
          // 释放内存
          imagedestroy($image);
        }
      }
    }catch(\Exception $e){
      //删除$locale_img
      // unlink($locale_img);
      // return false;

      //不显示错误
      // return false;
        // 也可以使用 -1 来达到相同的效果 不显示任何错误
      // error_reporting(-1);
    }

    //删除$locale_img
    unlink($locale_img);

    return $AvatarData;
  }
  // public static function SaveUploadImage($type, $base64data, $user_id = 'cache')
  // {
  //   $path = '';
  //   $sizeArray = null;

  //   if (isset(self::$pathData[$type])) {
  //     $path = self::$pathData[$type]['path'] . $user_id;
  //     $sizeArray = self::$pathData[$type]['sizeArray'];
  //   } else {
  //     return false;
  //   }

  //   // Determine image format from base64 data
  //   $img_data = explode(',', $base64data);
  //   $img_header = $img_data[0]; // Example: data:image/png;base64
  //   $img_format = strtolower(str_replace('data:image/', '', $img_header)); // Extract image format

  //   // Validate image format (for security)
  //   $allowed_formats = ['png', 'jpeg', 'jpg', 'gif'];
  //   if (!in_array($img_format, $allowed_formats)) {
  //     return false; // Invalid format
  //   }

  //   // Extract base64 image data
  //   $img_data = base64_decode($img_data[1]);

  //   $file_name = md5(microtime(true)) . '.' . $img_format; // Generate unique filename with correct extension

  //   $AvatarData = [
  //     'original' => '',
  //     'small' => '',
  //     'middle' => '',
  //     'large' => '',
  //   ];

  //   if (!file_exists($path)) {
  //     mkdir($path, 0777, true);
  //   }

  //   $locale_img = $path . '/' . $file_name;

  //   $r = file_put_contents($locale_img, $img_data); // Write image data to file

  //   try {
  //     if ($r) {
  //       // 屏蔽警告
  //       error_reporting(E_ERROR | E_PARSE);
  //       foreach ($sizeArray as $key => $size) {
  //         // Initialize image resource based on format
  //         switch ($img_format) {
  //           case 'png':
  //             $image = imagecreatefrompng($locale_img);
  //             break;
  //           case 'jpeg':
  //           case 'jpg':
  //             $image = imagecreatefromjpeg($locale_img);
  //             break;
  //           case 'gif':
  //             $image = imagecreatefromgif($locale_img);
  //             break;
  //           default:
  //             continue 2; // Invalid format, skip processing
  //         }

  //         if ($size == null) {
  //           // Directly save original size
  //           $paths = $path . '/' . $key;
  //           if (!file_exists($paths)) {
  //             mkdir($paths, 0777, true);
  //           }
  //           $img_path = $paths . '/' . $file_name;
  //           imagepng($image, $img_path); // Save image
  //           $AvatarData[$key] = '/' . $img_path; // Store path
  //         } else {
  //           // Resize image
  //           $new_image = imagecreatetruecolor($size[0], $size[1]);
  //           $source_width = imagesx($image);
  //           $source_height = imagesy($image);
  //           imagecopyresampled($new_image, $image, 0, 0, 0, 0, $size[0], $size[1], $source_width, $source_height);

  //           $paths = $path . '/' . $key;
  //           if (!file_exists($paths)) {
  //             mkdir($paths, 0777, true);
  //           }
  //           $img_path = $paths . '/' . $file_name;
  //           switch ($img_format) {
  //             case 'png':
  //               imagepng($new_image, $img_path);
  //               break;
  //             case 'jpeg':
  //             case 'jpg':
  //               imagejpeg($new_image, $img_path);
  //               break;
  //             case 'gif':
  //               imagegif($new_image, $img_path);
  //               break;
  //           }
  //           $AvatarData[$key] = '/' . $img_path; // Store path
  //         }
  //       }
  //       // 恢复默认错误报告级别
  //       error_reporting(E_ALL);
  //     }
  //   } catch (\Exception $e) {
  //     // Handle exceptions here
  //     // Example: Log error, delete temporary files, return false
  //     // unlink($locale_img);
  //     // return false;
  //   }

  //   // Delete temporary uploaded image
  //   unlink($locale_img);

  //   return $AvatarData;
  // }

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
    if (strpos($original, 'default') !== false||strpos($small, 'default') !== false||strpos($middle, 'default') !== false||strpos($large, 'default') !== false) {
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
