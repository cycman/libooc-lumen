<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/9/13
 * Time: 下午8:16
 */

namespace App\Entity\Resource;


use App\Exceptions\CurlException;
use App\Tool\CArray;
use App\Tool\Curl;

class TransmissionResource
{
    private $apiUri;

    public function __construct($sessionId = null)
    {
        $this->apiUri = env('TRANSMISSION_API_URI', '');
    }


    /**
     * api请求
     * @param array $postData
     * @param int $timeOut
     * @return mixed
     * @throws CurlException
     */
    public function postTransmissionRpcApi($postData = [], $timeOut = 30)
    {
        $uri = $this->apiUri;

        $header[] = 'X-Transmission-Session-Id:' . $this->getSessionId();
        /** @var Curl $curl */
        $curl = app(Curl::class);
        $curl->setTimeOut($timeOut);

        $result = $curl->post($uri, $postData, Curl::JSON_DATA, $header);
        $result = json_decode($result, true);
        if (!isset($result['result']) || $result['result'] != 'success') {
            throw new CurlException('rpc接口调用失败:' . $result['result'] ?? '', CurlException::FILTER_CURL_ERROR);
        }

        return $result;
    }


    /**
     * 根据id获取torrent信息
     * @param int $id
     * @param array $fields
     * @return mixed
     * @throws CurlException
     */
    public function getTorrentById(int $id, $fields = [])
    {
        if (empty($fields)) {
            $fields = ['id', 'name', 'totalSize'];
        }
        $postData = [
            'arguments' => [
                "fields" => $fields,
                'ids' => [$id],
            ],
            'method' => 'torrent-get',
            'tag' => 'torrent-get'
        ];
        $result = $this->postTransmissionRpcApi($postData);
        return $result['arguments']['torrents'][0];
    }

    /**
     * 获取torrentids
     * @return array
     * @throws CurlException
     */
    public function getTorrentIds()
    {
        $postData = [
            'arguments' => [
                "fields" => ['id', 'name']
            ],
            'method' => 'torrent-get',
            'tag' => 'torrent-get'
        ];
        $result = $this->postTransmissionRpcApi($postData);
        return CArray::listDictData($result['arguments']['torrents'], 'name', 'id');
    }

    /**
     * 设置下载文件列表
     * @param int $id
     * @param array $fileIndexs
     * @return mixed
     * @throws CurlException
     */
    public function setFiles(int $id, array $fileIndexs)
    {
        $args = ['ids' => [$id]];
        !empty($fileIndexs['wanted']) ? $args['files-wanted'] = $fileIndexs['wanted'] : '';
        !empty($fileIndexs['unwanted']) ? $args['files-unwanted'] = $fileIndexs['unwanted'] : '';
        $postData = [
            'arguments' => $args,
            'method' => 'torrent-set',
            'tag' => 'torrent-set'
        ];
        $result = $this->postTransmissionRpcApi($postData);

        return true;
    }

    /**
     * @return null|string
     * @throws CurlException
     */
    public function getSessionId()
    {
        $uri = $this->apiUri;

        /** @var Curl $curl */

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $uri);
        curl_setopt($handler, CURLOPT_HEADER, true);
        curl_setopt($handler, CURLOPT_NOBODY, true);
        curl_setopt($handler, CURLOPT_POST, false);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($handler);

        $sessionId = null;
        $headArr = explode("\r\n", $content);
        foreach ($headArr as $loop) {
            if (strpos($loop, "X-Transmission-Session-Id") !== false) {
                $sessionId = trim(substr($loop, 27));
                break;
            }
        }
        if (empty($sessionId)) {
            throw new CurlException('rpc获取sessionid失败:' . $content ?? '', CurlException::FILTER_CURL_ERROR);
        }
        return $sessionId;
    }


}