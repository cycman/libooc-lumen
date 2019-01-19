<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/10
 * Time: 下午6:30
 */

namespace App\Service\Taobao;


use App\Service\BaseService;
use App\Service\ForumService;
use Illuminate\Support\Facades\Log;

class ProductService extends BaseService
{


    private $forumService;
    private $font;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
        $this->font = env('FONT', '').'simsun.ttc';
    }

    public function genProductTitle( $book = []): string
    {
        $titile = substr($book['Title'], 0, 60);
        return $titile;
    }

    public function genProductDesc( $book = []): string
    {
        $previewUrl = $this->forumService->getThreadUrlByMd5($book['MD5']);
        $descTemplate = <<<html
     <br />
  <strong><font color="red">书籍信息</font></strong>
  <br />
  <strong>标题：%s</strong>
  <br />
  <strong>语言：%s</strong>
  <br />
  <strong>页数：%s</strong>
  <br />
  <strong>日期：%s</strong>
  <br />
  <strong>作者：%s</strong>
  <br />
  <strong>版本：%s</strong>
  <br />
  <strong>出版社：%s</strong>
  <br /> 
  <strong>ID：%s</strong>
  <br />
  <div class="quote">
   <blockquote class="quote">
    <strong><font color="red">简介</font></strong>
    <br />
    %s
   </blockquote>
  </div>
  <br />
  <strong><font color="red">预览地址</font></strong>
  %s
  <br />
 
html;
        return sprintf(
            $descTemplate,
            $book['Title'],
            $book['Language'],
            $book['Pages'],
            $book['Year'],
            $book['Author'],
            $book['Edition'],
            $book['Publisher'],
            $book['ID'],
            $book['ext_book_desc']['descr'],
            $previewUrl
        );
    }

    /**
     * 生成无线描述
     * @param  $book
     * @return string
     */
    public function genWirelessDesc( $book = []): string
    {
        $previewUrl = $this->forumService->getThreadUrlByMd5($book['MD5']);
        $descTemplate = <<<html
<wapDesc><txt>书籍信息</txt><txt>标题:%s</txt><txt>语言：%s</txt><txt>页数:%s</txt><txt>日期:%s</txt><txt>作者:%s</txt><txt>版本:%s</txt><txt>出版社:%s</txt><txt>ID:%s</txt><txt>预览地址:%s</txt></wapDesc>
html;
        return sprintf(
            $descTemplate,
            $book['Title'],
            $book['Language'],
            $book['Pages'],
            $book['Year'],
            $book['Author'],
            $book['Edition'],
            $book['Publisher'],
            $book['ID'],
            $previewUrl
        );
    }


    public function genBookProductWatermarkImages( $book, $dest,$extension='tbi',$rwidth=800,$rheight=800): bool
    {
        try{
            $originFile = env('BOOK_IMAGE_DIR') . $book['Coverurl'];
            $destFile = $dest . $book['MD5'] . ".{$extension}";

            /*打开图片*/
            //1.配置图片路径
            $src = $originFile;
            //2.获取图片的信息（得到图片的基本信息）
            $info = getimagesize($src);
            //3.通过获取图片类型
            $type = image_type_to_extension($info[2], false);
            //4.在内存中创建一个图片类型一样的图像
            $fun = "imagecreatefrom{$type}";
            //5.图片复制到内存中
            $image = $fun($src);
            list($width, $height) = getimagesize($src);
            $new = imagecreatetruecolor($rwidth, $rheight);
            imagecopyresized($new, $image, 0, 0, 0, 0, $rwidth, $rheight, $width, $height);

            /*操作图片*/
            //3.设置字体的颜色rgb和透明度
            $black = imagecolorallocatealpha($new, 255, 0, 30, 0);
            //4.写入文字
            $contents = [
                ['免费在线预览30页,免费在线预览30页,免费在线预览30页,免费在线预览30页,免费在线预览30页',10,400],
                ['免费在线预览30页,免费在线预览30页,免费在线预览30页,免费在线预览30页,免费在线预览30页',10,450],
                ['下单自动发货,下单自动发货,下单自动发货,下单自动发货,下单自动发货',10,500],
                ['下单自动发货,下单自动发货,下单自动发货,下单自动发货,下单自动发货',10,550],
                ['支持在线检索更多书籍',10,600],
                ['可转换任意格式 pdf mobi epub...',10,650],
                ['请认准 libooc专业英文电子书',10,700],
            ];
            foreach ($contents as $content) {
                imagefttext($new, 35, 0, $content[1], $content[2], $black, $this->font, $content[0]);
            }

            /*输出图片*/
            $func = "image{$type}";
            //保存图片
            $func($new, $destFile);
            return $destFile;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }

    }
}