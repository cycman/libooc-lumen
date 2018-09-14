<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/9/14
 * Time: 上午11:35
 */

namespace App\Service;


class FileService extends BaseService
{

    public function deleteFiles($files)
    {
        foreach ($files as $file) {
            unlink($file);
        }
    }

}