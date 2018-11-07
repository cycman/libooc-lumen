<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/7
 * Time: 下午2:21
 */

namespace App\Resource;


use Statickidz\GoogleTranslate;

class GoogleTranslateResource
{
    public function translateEnToZh($queries = [])
    {
        $trans = new GoogleTranslate();
        $result = $trans->translate('en', 'zh', implode("\n",$queries));
        return explode("\n", $result);
    }
}