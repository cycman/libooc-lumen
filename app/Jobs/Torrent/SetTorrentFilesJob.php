<?php

namespace App\Jobs\Torrent;

use App\Jobs\Job;
use App\Service\TorrentService;

class SetTorrentFilesJob extends Job
{
    public $id;
    public $name;
    public $torrentService;


    public function __construct(TorrentService $torrentService,$id = '',$name='')
    {
        parent::__construct();
        $this->torrentService = $torrentService;
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \App\Exceptions\CurlException
     * @throws \Exception
     */
    public function handle()
    {
        echo "设置id:{$this->id},name:{$this->name}种子可下载文件列表";
        echo $this->torrentService->filterWantedFiles($this->id);
    }
}
