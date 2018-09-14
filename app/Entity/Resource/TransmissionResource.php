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
    private $sessionId;

    public function __construct($sessionId = null)
    {
        $this->apiUri = env('TRANSMISSION_API_URI', '');
        $this->sessionId = $sessionId ?? env('TRANSMISSION_API_SESSION_ID', '');
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

        $header[] = 'X-Transmission-Session-Id:' . $this->sessionId;
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
                "fields" => $fields
            ],
            'id' => [$id],
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
                "fields" => ['id']
            ],
            'method' => 'torrent-get',
            'tag' => 'torrent-get'
        ];
        $result = $this->postTransmissionRpcApi($postData);
        return CArray::listData($result['arguments']['torrents'], 'id');
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
        $postData = [
            'arguments' => [
                "files-unwanted" => $fileIndexs['unwanted'],
                "files-wanted" => $fileIndexs['wanted'],
            ],
            'ids' => [$id],
            'method' => 'torrent-set',
            'tag' => 'torrent-set'
        ];
        $result = $this->postTransmissionRpcApi($postData);
        return true;
    }


}