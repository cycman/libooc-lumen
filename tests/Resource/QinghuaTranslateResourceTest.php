<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/7
 * Time: 下午2:23
 */

namespace Tests\Resource;

use App\Resource\GoogleTranslateResource;
use App\Resource\QinghuaTranslateResource;
use Tests\TestCase;

class QinghuaTranslateResourceTest extends TestCase
{
    public function testTranslate()
    {
        $qignhua = $this->app->make(QinghuaTranslateResource::class);
        $result = $qignhua->translateEnToZh(['The NEC 3 Engineering and Construction Contract: A Commentary, Second Edition','apple']);
        $this->assertEquals(['测试','苹果'], $result,print_r($result,1));
    }
}
