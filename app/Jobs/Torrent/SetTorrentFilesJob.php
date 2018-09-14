<?php

namespace App\Jobs\Torrent;

use App\Jobs\Job;
use App\Service\TorrentService;

class SetTorrentFilesJob extends Job
{

    public $queue = 'test';
    public $id;
    public $torrentService;


    public function __construct(TorrentService $torrentService,$id = '')
    {
        $this->torrentService = $torrentService;
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
        echo "设置{$this->id}种子可下载文件列表";
        echo $this->torrentService->filterWantedFiles($this->id);
    }
}
