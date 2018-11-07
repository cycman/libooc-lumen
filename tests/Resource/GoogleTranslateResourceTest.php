<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/7
 * Time: 下午2:23
 */

namespace Tests\Resource;

use App\Resource\GoogleTranslateResource;
use Tests\TestCase;

class GoogleTranslateResourceTest extends TestCase
{
    public function testTranslate()
    {
        $google = $this->app->make(GoogleTranslateResource::class);
        $result = $google->translateEnToZh(['test','apple']);
        $this->assertEquals(['测试','苹果'], $result,print_r($result,1));
    }
}
