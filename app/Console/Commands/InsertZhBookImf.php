<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午7:01
 */

namespace App\Console\Commands;


use App\Models\BookTopic;
use App\Resource\BaiduTranslateResource;
use App\Service\ZhBookImfService;
use Illuminate\Console\Command;

class InsertZhBookImf extends Command
{
    protected $name = 'book:insert_zh_book_imf_job';
    protected $description = '执行生成书籍中文信息任务';
    public function handle()
    {
        app()->make(ZhBookImfService::class)->genInsertImfJobs();
        var_dump('生成任务成功');
    }

}