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
     * @param $sourceFile
     * @param string $previewDestDir
     * @param int $previewPageSize
     * @return string
     * @throws \Exception
     */
    public function makePreviewPdfFile($sourceFile, $previewDestDir = '', $previewPageSize = 30)
    {
        $destPreviewDir = empty($previewDestDir) ? env('PREVIEW_FILE_DIR', '') : $previewDestDir;
        $outputPdf = $destPreviewDir . md5($sourceFile) . '.pdf';
        $inputPdf = $sourceFile;
        $pythonCmd = env('PYTHON_CMD', '');
        if (!is_file($sourceFile)) {
            throw  new \Exception("{$sourceFile} 不存在", 1);
        }
        $outArr = [];
        exec("{$pythonCmd} ../bin/preview.py 1 {$previewPageSize} {$inputPdf} {$outputPdf}",$outArr);
        return $outputPdf;
    }

}