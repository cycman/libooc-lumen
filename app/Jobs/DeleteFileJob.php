<?php

namespace App\Jobs;

use App\Exceptions\CurlException;
use App\Service\FileService;

class DeleteFileJob extends Job
{

    public $queue = 'file';
    public $id;
    public $name;
    public $fileService;

    public function __construct(FileService $fileService, $id, $name)
    {
        parent::__construct();
        $this->fileService = $fileService;
        $this->id = $id;
        $this->name = $name;
    }


    public function handle()
    {
        try {
            if ($this->fileService->deleteFile($this->id, $this->name)) {
                echo "删除成功{$this->id},==={$this->name}";
            } else {
                echo "删除失败{$this->id},==={$this->name}";
            }
        } catch (CurlException $e) {
        }
    }
}
