<?php

namespace App\Http\Controllers;

use App\Service\FileService;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function batchLoadFiles()
    {
        set_time_limit(0);
        var_dump(app(FileService::class)->addLoadFilesJobs(env('BOOK_FILE_DIR', '')));
    }

    /**
     * 预览文件
     * @param $identity
     * @return string
     */
    public function previewFile($identity)
    {
        set_time_limit(0);
        try {
            if (is_numeric($identity)) {
                $fileInfo = app(FileService::class)->previewFileByBid($identity);
            }else{
                $fileInfo = app(FileService::class)->previewFile($identity);
            }
            $previewPath = $fileInfo['path'];
            $previewName = $fileInfo['name'];

            //r: 以只读方式打开，b: 强制使用二进制模式
            $fileHandle = fopen($previewPath, "rb");

            Header("Content-type: application/octet-stream");
            Header("Content-Transfer-Encoding: binary");
            Header("Accept-Ranges: bytes");
            Header("Content-Length: " . filesize($previewPath));
            Header("Content-Disposition: attachment; filename=\"{$previewName}\"");

            while (!feof($fileHandle)) {
                //从文件指针 handle 读取最多 length 个字节
                echo fread($fileHandle, 32768);
            }
            fclose($fileHandle);
            die;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return '未知原因出错啦!!';
        }
    }
    //

    public function downloadFile($identity)
    {
        set_time_limit(0);
        try {
            if (is_numeric($identity)) {
                $fileInfo = app(FileService::class)->downloadFileByBid($identity);
            } else {
                $fileInfo = app(FileService::class)->downloadFile($identity);
            }
            $filePath = $fileInfo['path'];
            $fileName = $fileInfo['name'];

            $pattern = "/\\d+\/.*/";
            $match = [];
            preg_match($pattern, $filePath,$match);
            if (empty($match)) {
                throw new \Exception('找不到该文件', 1);
            }
            $path = $match[0];
            return redirect("$path?file_name={$fileName}");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return '未知原因出错啦!!';
        }
    }

    public function imageFile($identity)
    {
        set_time_limit(0);
        try {
            if (is_numeric($identity)) {
                $fileInfo = app(FileService::class)->imageFileByBid($identity);
            } else {
                $fileInfo = app(FileService::class)->imageFile($identity);
            }
            $filePath = $fileInfo['path'];
            if (is_file($filePath)) {
                //r: 以只读方式打开，b: 强制使用二进制模式
                $fileHandle = fopen($filePath, "rb");

                Header("Content-type: application/octet-stream");
                Header("Content-Transfer-Encoding: binary");
                Header("Accept-Ranges: bytes");
                Header("Content-Length: " . filesize($filePath));

                while (!feof($fileHandle)) {
                    //从文件指针 handle 读取最多 length 个字节
                    echo fread($fileHandle, 32768);
                }
                fclose($fileHandle);
                exit();
            } else {
                $pattern = '/\\d+\/.*/';
                $matchs = [];
                preg_match($pattern, $filePath, $matchs);
                header('cache:cache');
                header('HTTP/1.1 301 Moved Permanently');
                $location = sprintf('%s/%s/%s', 'http://libgen.io/', 'covers', $matchs[0]);
                header("Location: $location");
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return '未知原因出错啦!!';
        }
    }
}
