<?php

namespace MaterialDesignForum\Core;

use MaterialDesignForum\Controllers\UserGroup as UserGroupController;

class Update
{
    private static $remoteUrl;
    private static $localPackageFile;
    private static $downloadFolder;
    private static $websiteRoot;

    // 修改构造函数为静态方法，初始化静态变量
    public static function init(
        // $remoteUrl = 'http://localhost:83/composer.json', 
        $remoteUrl = 'https://mdf.xbedrock.com/composer.json', 
        $localPackageFile = 'composer.json', 
        $downloadFolder = 'public/update_download'
    )
    {
        self::$remoteUrl = $remoteUrl;
        self::$localPackageFile = $localPackageFile;
        self::$downloadFolder = $downloadFolder;
        self::$websiteRoot = $_SERVER['DOCUMENT_ROOT'];
    }

    // 获取远程 package.json 内容
    private static function getRemotePackageJson()
    {
        $packageJson = file_get_contents(self::$remoteUrl);
        if ($packageJson === false) {
            throw new \Exception("Update:getRemotePackageJson error");
        }
        return json_decode($packageJson, true);
    }

    /**
     * 获取本地 composer.json 内容
     */
    private static function getLocalPackageJson()
    {
        if (!file_exists(self::$localPackageFile)) {
            return null;
        }

        $packageJson = file_get_contents(self::$localPackageFile);
        return json_decode($packageJson, true);
    }

    /**
     * 下载压缩包
     * @param string $zipLink 压缩包链接
     * @return string 返回下载的压缩包文件路径
     */
    private static function downloadZip($zipLink)
    {
        $zipFile = self::$downloadFolder . '/update.zip';
        $zipContent = file_get_contents($zipLink);
        if ($zipContent === false) {
            return false; // 下载失败
            // throw new \Exception("下载压缩包失败。");
        }

        if (!file_exists(self::$downloadFolder)) {
            mkdir(self::$downloadFolder, 0777, true);  // 如果文件夹不存在，创建文件夹
        }

        file_put_contents($zipFile, $zipContent);
        return $zipFile;
    }

    /**
     * 解压压缩包
     * @param string $zipFile 压缩包文件路径
     */
    private static function extractZip($zipFile,$path = null)
    {
        $zip = new \ZipArchive();
        if ($path === null) {
            $path = self::$websiteRoot; // 默认解压到网站根目录
        }
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($path);
            $zip->close();
        } else {
            throw new \Exception("Update:extractZip error");
        }
    }

    // 更新本地 package.json 中的版本号
    // private static function updateLocalVersion($version)
    // {
    //     $localPackageData = self::getLocalPackageJson();
    //     if ($localPackageData === null) {
    //         $localPackageData = [];
    //     }
    //     $localPackageData['version'] = $version;

    //     file_put_contents(self::$localPackageFile, json_encode($localPackageData, JSON_PRETTY_PRINT));
    // }

    /**
     * 执行更新
     * @param string|null $user_token 用户令牌
     * @return array
     */
    public static function update($user_token = null)
    {
        if(UserGroupController::IsAdmin($user_token) === false) {
            return [
                'is_update' => false,
                'new_version' => null,
                'current_version' => null,
                'error_message' => '没有权限执行更新。'
            ];
        }

        try {
            self::init(); // 确保初始化静态变量
            $packageData = self::getRemotePackageJson();
            $remoteVersion = $packageData['version'];
            $zipLink = $packageData['zip_link'];

            $localPackageData = self::getLocalPackageJson();
            $localVersion = $localPackageData['version'] ?? null;

            // 如果版本号相同，不需要更新
            if ($localVersion === $remoteVersion) {
                // echo "本地版本已是最新，版本号：$remoteVersion\n";
                return [
                    'is_update' => false,
                    'new_version' => null,
                    'current_version' => $localVersion
                ];
                // return;
            }

            // 下载并解压更新包
            // echo "开始下载压缩包：$zipLink\n";
            $zipFile = self::downloadZip($zipLink);

            if( $zipFile === false) {
                return [
                    'is_update' => false,
                    'new_version' => $remoteVersion,
                    'current_version' => $localVersion
                ];
                // throw new \Exception("下载压缩包失败。");
            }

            //首先复制self::$websiteRoot./src/Config/Config.php文件为 Config.php.bak
            $configBackupPath = self::$websiteRoot . '/src/Config/Config.php.bak';
            if (file_exists(self::$websiteRoot . '/src/Config/Config.php')) {
                copy(self::$websiteRoot . '/src/Config/Config.php', $configBackupPath);
            }

            // echo "解压压缩包到 " . self::$websiteRoot . "\n";
            self::extractZip($zipFile);

            //然后将self::$websiteRoot./src/Config/Config.php重命名为Config.php.new
            $configNewPath = self::$websiteRoot . '/src/Config/Config.php.new';
            if (file_exists(self::$websiteRoot . '/src/Config/Config.php')) {
                rename(self::$websiteRoot . '/src/Config/Config.php', $configNewPath);
            }

            //然后再将self::$websiteRoot./src/Config/Config.php.bak重命名为Config.php
            $configBackupFilePath = self::$websiteRoot . '/src/Config/Config.php.bak';
            if (file_exists($configBackupFilePath)) {
                rename($configBackupFilePath, self::$websiteRoot . '/src/Config/Config.php');
            }

            //解压到downloadFolder
            // self::extractZip($zipFile,self::$websiteRoot .'/' . self::$downloadFolder);

            //获取self::$websiteRoot .'/' . self::$downloadFolder目录下的目录树


            return [
                'is_update' => true,
                'new_version' => $remoteVersion,
                'current_version' => $localVersion
            ];
        } catch (\Exception $e) {
            // echo '错误：' . $e->getMessage() . "\n";
            return [
                'is_update' => false,
                'new_version' => $remoteVersion,
                'current_version' => $localVersion,
                'error_message' => $e->getMessage()
            ];
        }
    }

    /**
     * 检查是否有更新
     * @return array
     */
    public static function checkUpdate($user_token = null)
    {
        if(UserGroupController::IsAdmin($user_token) === false) {
            return [
                'is_has_update' => false,
                'new_version' => null,
                'current_version' => null,
                'error_message' => '没有权限执行检查更新。'
            ];
        }
        try {
            self::init(); // 确保初始化静态变量
            $packageData = self::getRemotePackageJson();
            $remoteVersion = $packageData['version'];
            $localPackageData = self::getLocalPackageJson();
            $localVersion = $localPackageData['version'] ?? null;

            $remoteZipLink = $packageData['zip_link'] ?? null;

            // 如果版本号相同，不需要更新
            if ($localVersion === $remoteVersion) {
                return [
                    'is_has_update' => false,
                    'new_version' => $remoteVersion,
                    'current_version' => $localVersion
                ];
            }

            return [
                'is_has_update' => true,
                'new_version' => $remoteVersion,
                'current_version' => $localVersion,
                'remote_zip_link' => $remoteZipLink
            ];
        } catch (\Exception $e) {
            // echo '错误：' . $e->getMessage() . "\n";
            
            $localPackageData = self::getLocalPackageJson();
            $localVersion = $localPackageData['version'] ?? null;
            return [
                'is_has_update' => false,
                'new_version' => null,
                'current_version' => $localVersion,
                'error_message' => $e->getMessage()
            ];
        }
    }
}
