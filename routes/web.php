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
    $model = $_GET['models']??'';
    $md5 = $_GET['md5']??'';
    if ($model == 'books_preview') {
        return redirect("file/preview_file/{$md5}");
    } elseif ($model == 'books') {
        return redirect("file/download_file/{$md5}");
    } elseif ($model == 'covers') {
        $location = $_GET['location'] ??'';
        $infos = explode('/', $location);
        $infos = explode('-', $infos['1']);
        $md5 = $infos[0];
        return redirect("file/image_file/{$md5}");
    }
    exit();
});
$router->get('/torrents', function () use ($router) {
    var_dump(app(\App\Entity\Resource\TransmissionResource::class)->getTorrentIds());

});
$router->get('/sessionId', function () use ($router) {
    var_dump(app(\App\Entity\Resource\TransmissionResource::class)->getSessionId());
});

$router->get('/test', function () use ($router) {
    $file = fopen(env('KONGFU_CSV_OUT_DIR').'template.csv', 'r');
    $version = [];
    $enSvcTitles = [];
    $chSvcTitles = [];
    $template = [];
    while ($data = fgetcsv($file, 0, ",")) {    //每次读取CSV里面的一行内容
        if (empty($version)) {
            $version = $data;
        } elseif (empty($enSvcTitles)) {
            $enSvcTitles = $data;
        } elseif (empty($chSvcTitles)) {
            $chSvcTitles = $data;
        } elseif (empty($template)) {
            $template = $data;
        }
    }
    fclose($file);
    var_dump($version);
    var_dump($enSvcTitles);
    var_dump($chSvcTitles);
});

$router->get('/setfs/{id}', function ($id) use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->filterWantedFiles($id));
});

$router->get('/batch', function () use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->batchFilterWantedFilesJob());
});

$router->get('/example/show', 'ExampleController@show');
require_once('file_web.php');
require_once('books.php');
require_once('forum_web.php');
require_once('taobao_web.php');
