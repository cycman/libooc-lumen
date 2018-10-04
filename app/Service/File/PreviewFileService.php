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
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function makePreviewPdfFile($file, $perviewDestDir = '', $previewPageSize = 10)
    {
        $destPreviewDir = $perviewDestDir ?? env('PREVIEW_FILE_DIR', '');
        if (!is_file($file)) {
            throw  new \Exception("{$file} 不存在", 1);
        }
        // 建立 FPDI 物件
        $pdf = new FPDI();
        // 載入現在 PDF 檔案
        $page_count = $pdf->setSourceFile($file);
        $previewPageSize = $page_count >= $previewPageSize ? $previewPageSize : $page_count - 1;
        // 匯入現在 PDF 檔案的第一頁
        for ($page = 1; $page <= $previewPageSize; $page++) {
            $tpl = $pdf->importPage($page);
            // 在新的 PDF 上新增一頁
            $pdf->addPage();
            // 在新增的頁面上使用匯入的第一頁
            $pdf->useTemplate($tpl);
        }
        // 輸出成本地端 PDF 檔案
        $pdf->output($destPreviewDir . md5($file) . '.pdf', 'F');
        return $destPreviewDir . md5($file) . '.pdf';
    }

}