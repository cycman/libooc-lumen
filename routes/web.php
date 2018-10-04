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
$router->get('/torrents', function () use ($router) {
    var_dump(app(\App\Entity\Resource\TransmissionResource::class)->getTorrentIds());

});
$router->get('/sessionId', function () use ($router) {
    var_dump(app(\App\Entity\Resource\TransmissionResource::class)->getSessionId());
});

$router->get('/test', function () use ($router) {
    var_dump(app(\App\Service\ForumService::class)->deployPost());
});

$router->get('/setfs/{id}', function ($id) use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->filterWantedFiles($id));
});

$router->get('/batch', function () use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->batchFilterWantedFilesJob());
});

$router->get('/example/show', 'ExampleController@show');
require_once('file_web.php');
require_once('forum_web.php');
