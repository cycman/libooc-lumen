<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午10:43
 */

namespace Tests\Service;

use App\Service\KfCsvService;
use App\Service\ZhBookImfService;
use Tests\TestCase;

class KfzCsvServiceTest extends TestCase
{
    public function testGetCsvTemplate()
    {
        var_dump(app(KfCsvService::class)->getCsvTemplate());
    }
}
