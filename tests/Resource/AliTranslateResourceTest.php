<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/7
 * Time: 下午2:23
 */

namespace Tests\Resource;

use App\Resource\AliTranslateResource;
use Tests\TestCase;

class AliTranslateResourceTest extends TestCase
{
    public function testTranslate()
    {
        $ali = $this->app->make(AliTranslateResource::class);
        $result = $ali->translateEnToZh(['test','apple']);
        $this->assertEquals(['测试','苹果'], $result,print_r($result,1));
    }
}
