<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/9
 * Time: 下午6:46
 */

namespace App\Service;


use App\Models\Book;
use App\Models\BookTopic;
use App\Service\Taobao\ProductService;
use App\Tool\CArray;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaoBaoCsvService extends BaseService
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param $name
     * @param string $topic
     * @param int $offset
     * @param int $size
     * @return string
     * @throws \Exception
     */
    public function createTaoBaoCsvByTopic($name, $topic = '', $offset = 0, $size = 1000)
    {
        $query = Book::query();
        $query->rightJoin('b_file', 'updated.ID', '=', 'b_file.bid');
        $query->rightJoin('b_book_thread', 'updated.ID', '=', 'b_book_thread.bid');
        if (empty($topic)) {
            $query->where(['updated.topic' => $topic]);
        } else {
            $topics =  $this->app->make(BookTopic::class)->findByTopicIdHlId($topic);
            $tids = CArray::listData($topics, 'topic_id');
            $query->whereIn('updated.topic', $tids);
        }
        $query->where(['updated.language' => 'english']);
        $query->offset($offset);
        $query->limit($size);
        $books = $query->with('extBookDesc')->get()->toArray();
        return $this->createTaoBaoCsv($name, $books);
    }

    /**
     * @param $templateFile
     * @param $name
     * @return array
     */
    private function getCsvTemplate($templateFile, $name): array
    {
        $file = fopen($templateFile, 'r');
        $version = [];
        $enSvcTitles = [];
        $chSvcTitles = [];
        $template = [];
        while ($data = fgetcsv($file, 0, "\t")) {    //每次读取CSV里面的一行内容
            if (empty($version)) {
                $version = $data;
            } elseif (empty($enSvcTitles)) {
                $enSvcTitles = $data;
            } elseif (empty($chSvcTitles)) {
                $chSvcTitles = $data;
            } elseif (empty($template)) {
                $template = $data;
            }
        }
        fclose($file);
        return array($version, $enSvcTitles, $chSvcTitles, $template);
    }

    /**
     * @param $name
     * @param $books
     * @return string
     * @throws \Exception
     */
    public function createTaoBaoCsv($name, $books): string
    {
        $outName = sprintf('%s_%s.csv', $name, date('m_d', time()));
        $outDir = sprintf('%s/%s/', env('TAOBAO_CSV_OUTPUT_DIR'), $name . date('y_m_d_h_i_s', time()));
        if (!mkdir($outDir, 0777)) {
            throw new \Exception("{$outDir}文件夹创建失败");
        }
        $templateFilePath = sprintf('%s/%s_template.csv', env('TAOBAO_CSV_OUTPUT_DIR'), $name);
        if (!is_file($templateFilePath)) {
            throw new \Exception('模版 文件不存在');
        }
        list($version, $enSvcTitles, $chSvcTitles, $template) = $this->getCsvTemplate($templateFilePath, $name);

        $outCsvFile = sprintf('%s/%s', $outDir, $outName);
        $fp = fopen($outCsvFile, 'w');
        fputcsv($fp, $version, "\t");
        fputcsv($fp, $enSvcTitles, "\t");
        fputcsv($fp, $chSvcTitles, "\t");
        //设置商品信息
        foreach ($books as $book) {
            $productTemplate = $template;
            $productTemplate[0] = $this->productService->genProductTitle($book);//pro title
            $productTemplate[7] = 10;//pro price
            //一天后上架
            $productTemplate[19] = date('Y-M-D h:i:s', time() + 86400);//pro begin_time
            $productTemplate[20] = $this->productService->genProductDesc($book);//pro desc
            $productTemplate[33] = $book['ID'];//pro 商家编码
            $productTemplate[9] = 999;//pro 库存数量
            $productTemplate[47] = null;//pro 国家地区必填为空
            $productTemplate[53] = $this->productService->genWirelessDesc($book);//移动端描述

            $productTemplate[28] = sprintf('%s:1:0:', $book['MD5']);//pro images  imageFirst:1:0:|imageSecond:1:1:
            if (!$this->productService->genBookProductWatermarkImages($book, $outDir)) {
                Log::error("{$book['MD5']}生成水印图片失败!!");
                continue;
            }
            //输出
            fputcsv($fp, $productTemplate, "\t");
        }

        fclose($fp);
        return $outCsvFile;
    }

}