<?php

namespace App\Http\Controllers;

use App\Service\FileService;

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
     * @param $md5
     * @return string
     * @throws \Exception
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function previewFile($md5)
    {
        set_time_limit(0);
        try {
            $preview = app(FileService::class)->previewFile($md5);
            $previewPath = $preview['path'];
            $previewName = $preview['name'];

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
        } catch (\Exception $e) {
            return '未知原因出错啦!!';
        }
    }

    //

    public function downloadFile($md5)
    {
        set_time_limit(0);
        try {
            $fileInfo = app(FileService::class)->downloadFile($md5);
            $filePath = $fileInfo['path'];
            $fileName = $fileInfo['name'];

            //r: 以只读方式打开，b: 强制使用二进制模式
            $fileHandle = fopen($filePath, "rb");

            Header("Content-type: application/octet-stream");
            Header("Content-Transfer-Encoding: binary");
            Header("Accept-Ranges: bytes");
            Header("Content-Length: " . filesize($filePath));
            Header("Content-Disposition: attachment; filename=\"{$fileName}\"");

            while (!feof($fileHandle)) {
                //从文件指针 handle 读取最多 length 个字节
                echo fread($fileHandle, 32768);
            }
            fclose($fileHandle);
        } catch (\Exception $e) {
            return '未知原因出错啦!!';
        }
    }
}
