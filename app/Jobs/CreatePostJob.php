<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/4
 * Time: 下午4:14
 */

namespace App\Jobs;


use App\Service\ForumService;

class CreatePostJob extends Job
{
    public $ids = [];
    public $fid;

    public function __construct($ids, $fid)
    {
        parent::__construct();
        $this->ids = $ids;
        $this->fid = $fid;
    }

    public function handle()
    {
        $ids = implode(',', $this->ids);
        echo "为{$ids}书籍增加,post。";
        echo app(ForumService::class)->deployPost($this->fid, ['ID' => $this->ids,]);
    }
}