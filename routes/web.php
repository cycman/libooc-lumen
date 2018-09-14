<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->loadTorrent(1));

});

$router->get('/files', function () use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->setFiles(1,['unwanted'=>[1,2],'wanted'=>[0]]));

});
$router->get('/torrents', function () use ($router) {
    var_dump(app(\App\Entity\Resource\TransmissionResource::class)->getTorrentIds());

});

$router->get('/setfs/{id}', function ($id) use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->filterWantedFiles($id));
});

$router->get('/batch', function () use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->batchFilterWantedFilesJob());
});

$router->get('/abc', function () use ($router) {
    $book = \App\Models\Book::query()->find(9282);
    var_dump($book);
});

$router->get('/example/show', 'ExampleController@show');

function rpc(): void
{
    $url = 'http://' . env('TRANSMISSION_HOST', '') . ':' . env('TRANSMISSION_PORT', '9091') . '/transmission/rpc';
    // 1. 初始化
    $ch = curl_init();
    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Transmission-Session-Id:ri1UxrZaLdPdNeI8cj2PmHT7krHv67bbHTpZMei5p1mTmdUw']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['method' => 'session-get']));

    // 3. 执行并获取HTML文档内容
    $output = curl_exec($ch);
    if ($output === FALSE) {
        echo "CURL Error:" . curl_error($ch);
    }
    var_dump(json_decode($output, true));

    // 4. 释放curl句柄
    curl_close($ch);
}