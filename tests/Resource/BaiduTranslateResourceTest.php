<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午5:50
 */

namespace Tests\Resource;


use App\Resource\BaiduTranslateResource;
use Tests\TestCase;

class BaiduTranslateResourceTest extends TestCase
{

    public function testTranslate()
    {
        $baidu = $this->app->make(BaiduTranslateResource::class);
        $result = $baidu->translateEnToZh(['test','apple']);
        $this->assertEquals(['测试','苹果'], $result,print_r($result,1));
    }
}
