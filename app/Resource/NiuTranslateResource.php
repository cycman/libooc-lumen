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

class NiuTranslateResource
{
    private $url = "http://127.0.0.1:1517/niutrans/translation";

    public function translateEnToZh($queries = [])
    {
        $query = implode(" lvbs ", $queries);
        $url = sprintf("%s", $this->url);
        $post_data = [
            'from'=>'en',
            'src_text' => $query,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);

        $r = curl_exec($ch);
        curl_close($ch);
        $r = str_replace("\n", '', $r);
        $r = str_replace(" ", '', $r);
        $result = json_decode($r, true);
        if (isset($result['error_code'])) {
            Log::error(json_encode([$result,$url]));
            throw new \Exception('error', $result['error_code']);
        }
        return explode("lvbs", $result['tgt_text']);
    }
}

?>
