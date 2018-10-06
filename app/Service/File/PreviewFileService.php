<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/3
 * Time: 下午5:56
 */

namespace App\Service\File;


use App\Service\BaseService;
use setasign\Fpdi\Fpdi;

class PreviewFileService extends BaseService
{
    /**
     * 生成pdf预览文件
     * @param $file
     * @param string $perviewDestDir
     * @param int $previewPageSize
     * @return string
     * @throws \Exception
     */
    public function makePreviewPdfFile($file, $perviewDestDir = '', $previewPageSize = 10)
    {
        $destPreviewDir = $perviewDestDir ?? env('PREVIEW_FILE_DIR', '');
        $outputPdf = $destPreviewDir . md5($file) . '.pdf';
        $inputPdf = $file;
        $pythonCmd = env('PYTHON_CMD', '');
        if (!is_file($file)) {
            throw  new \Exception("{$file} 不存在", 1);
        }
        exec("{$pythonCmd} ../bin/preview.py 1 2 {$inputPdf} {$outputPdf}");
        return $outputPdf;
    }

}