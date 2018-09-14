<?php
namespace App\Tool;

use App\Exceptions\CurlException;

class Curl
{
    protected $timeout = 3;
    const JSON_DATA = 'json';
    const URL_DATA = 'URL';
    protected $throwError = false;
    /**
     * 执行psot请求
     * @param $timeout
     * @return mixed
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 设置是否抛异常
     * @param bool $throwError
     * @return mixed
     */
    public function setThrowError(bool $throwError)
    {
        $this->throwError = $throwError;
    }

    /**
     * 执行psot请求
     *
     * @param        $uri
     * @param        $params
     * @param string $type
     * @param array  $header
     *
     * @return mixed
     * @throws CurlException
     */
    public function post($uri, $params = [], $type = self::URL_DATA, $header = [])
    {
        $handle = curl_init($uri);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $header);

        if ($type == self::JSON_DATA) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($params));
        } else {
            curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $result = curl_exec($handle);
        if ($this->throwError && $result === false) {
            $error = curl_error($handle);
            curl_close($handle);
            throw new CurlException($error);
        }
        curl_close($handle);

        return $result;
    }

    /**
     * 执行get请求
     * @param $uri
     * @param array $params
     * @param array $header
     * @return mixed
     * @throws CurlException
     */
    public function get($uri, $params = [], $header = [])
    {
        if (strpos($uri, "?") !== false) {
            $uri .= "&".http_build_query($params);
        } else {
            $uri .= "?".http_build_query($params);
        }

        $handle = curl_init($uri);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($handle);
        if ($this->throwError && $result === false) {
            $error = curl_error($handle);
            curl_close($handle);
            throw new CurlException($error);
        }
        curl_close($handle);

        return $result;
    }
}
