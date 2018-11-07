<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/7
 * Time: 上午10:10
 */

namespace App\Jobs;


use App\Service\ZhBookImfService;

class InsertZhBookImfJob extends Job
{
    public $queue = 'imf';
    public $ids;
    public $service;

    public function __construct(ZhBookImfService $service, $ids)
    {
        parent::__construct();
        $this->service = $service;
        $this->ids = $ids;
    }


    public function handle()
    {
        $this->service->batchInsertZhBookImfs($this->ids);
        var_dump("插入成功");
    }
}