<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/9/14
 * Time: 下午4:07
 */

namespace App\Service;
use Illuminate\Support\Facades\App;
use Laravel\Lumen\Application;

/**
 * Class BaseService
 * @package App\Service
 * @property Application $app
 */
class BaseService
{
    public function getApp()
    {
        return App::make('app');
    }

    public function __get($name)
    {
        if ($name == 'app') {
            return $this->getApp();
        }
    }
}