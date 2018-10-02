<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/2
 * Time: 下午8:36
 */

namespace App\Jobs\File;


use App\Jobs\Job;
use App\Service\FileService;

class LoadFilesJob extends Job
{

    protected $fileDir;
    /**
     * @var FileService
     */
    protected $fileService;

    public function __construct(FileService $fileService, $fileDir)
    {
        parent::__construct();
        $this->fileService = $fileService;
        $this->fileDir = $fileDir;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        echo "加载{$this->fileDir}目录文件列表";
        echo $this->fileService->loadFilesFromDir($this->fileDir);
    }
}