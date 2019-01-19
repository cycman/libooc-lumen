<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/12
 * Time: 下午7:23
 */

namespace App\Service;

use App\GraphQL\Query\BookQuery;
use App\Models\Book;
use App\Models\BookTopic;
use App\Service\Taobao\ProductService;
use App\Tool\CArray;
use Illuminate\Support\Facades\Log;

class KfCsvService extends BaseService
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }



    public function createCsv(array $input = [])
    {
        $topic = app(BookTopic::class)->findByTopicId($input['topic'] ?? '');
        $template = [
            18=> 99,//定价
            22=>$input['price']??10,//售价
            23=> $input['transport']??'free',//运费模版
            26=> $topic['topic_descr']??'',//本店分类
        ];
        $books = app(BookQueryService::class)->lists($input);
        return $this->createCsvByBooks($template, $books);
    }

    /**
     * @param $template
     * @param $books
     * @return string
     * @throws \Exception
     */
    public function createCsvByBooks($template, $books)
    {
        list($title,$default) = $this->getCsvTemplate();
        $outName = sprintf('%s.csv', date('m_d', time()));
        $outDir = sprintf('%s/%s/', env('KONGFU_CSV_OUT_DIR'), date('y_m_d_h_i_s', time()));
        if (!mkdir($outDir, 0777)) {
            throw new \Exception("{$outDir}文件夹创建失败");
        }
        $outCsvFile = sprintf('%s/%s', $outDir, $outName);
        $fp = fopen($outCsvFile, 'w');
        fputcsv($fp, $title);
        //设置商品信息
        $template = array_map(function ($val, $key) use ($template) {
            return $template[$key] ?? $val;
        }, $default, array_keys($default));
        foreach ($books as $book) {
            $productTemplate = $template;
            $productTemplate[1] = $this->productService->genProductTitle($book);//pro title
            $productTemplate[4] = $book['Author'];//作者
            $productTemplate[5] = $book['Publisher'];//出版社
            $productTemplate[6] = date('Y/m/01', strtotime($book['TimeAdded']));//出版时间
            $productTemplate[7] = 1;//版次
            $productTemplate[8] = $book['City'];//出版地
            $productTemplate[9] = date('Y/m/d', strtotime($book['TimeAdded']));//印刷时间
            $productTemplate[19] = $book['Language'];//正文语种
            $productTemplate[20] = '全新';//品相
            $productTemplate[28] = $this->productService->genProductDesc($book);//描述

            $productTemplate[27] = $book['ID'];//pro 商家编码
            $productTemplate[29] = $book['MD5'] . ".jpg";//图片1
            $productTemplate[30] = $book['MD5'] . ".jpg";
            ;//图片2

            if (!$this->productService->genBookProductWatermarkImages($book, $outDir, 'jpg')) {
                Log::error("{$book['MD5']}生成水印图片失败!!");
                continue;
            }
            //输出
            fputcsv($fp, $productTemplate);
        }

        fclose($fp);
        return $outCsvFile;
    }

    public function getCsvTemplate(): array
    {
        $templateFilePath = sprintf('%s/template.csv', env('KONGFU_CSV_OUT_DIR'));
        $file = fopen($templateFilePath, 'r');
        $chSvcTitles = [];
        $template = [];
        while ($data = fgetcsv($file, 0)) {    //每次读取CSV里面的一行内容
            if (empty($chSvcTitles)) {
                $chSvcTitles = $data;
            } elseif (empty($template)) {
                $template = $data;
            }
        }
        fclose($file);
        return array($chSvcTitles, $template);
    }
}
