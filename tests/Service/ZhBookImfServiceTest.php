<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午10:43
 */

namespace Tests\Service;

use App\Service\ZhBookImfService;
use Tests\TestCase;

class ZhBookImfServiceTest extends TestCase
{

    public function testBatchInsertZhBookImfs()
    {
       // $this->app->make(ZhBookImfService::class)->batchInsertZhBookImfs([59,60]);
    }

    public function testGenInsertImfJobs()
    {
        $this->app->make(ZhBookImfService::class)->genInsertImfJobs();
    }
}
