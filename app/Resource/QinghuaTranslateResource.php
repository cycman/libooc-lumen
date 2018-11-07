<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/11/6
 * Time: 下午4:18
 */

namespace App\Resource;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class QinghuaTranslateResource
{
    private $url = "http://101.6.5.207:3893/translateapi";

    public function translateEnToZh($queries = [])
    {
        $query = implode("|||", $queries);
        $url = sprintf("%s/%s", $this->url, rawurlencode(rawurlencode($query)));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        $r= str_replace("\u3002", '', $r);
        $result = json_decode($r, true);
        return explode("|||", $result['trg']);
    }
}

?>
