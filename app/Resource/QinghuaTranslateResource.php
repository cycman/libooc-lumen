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
        $query = implode(" lvbs ", $queries);
        $url = sprintf("%s/%s", $this->url, rawurlencode(rawurlencode($query)));
        $ch = curl_init();
        $headers = [
            'Host: 101.6.5.207:3893',
            'Origin: http://101.6.5.207:3892',
            'Referer: http://101.6.5.207:3892/',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36'
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $r = curl_exec($ch);
        curl_close($ch);
        $r = str_replace("\u3002", '', $r);
        $result = json_decode($r, true);
        return explode("lvbs", $result['trg']);
    }
}

?>
