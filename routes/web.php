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
$router->get('/sessionId', function () use ($router) {
    var_dump(app(\App\Entity\Resource\TransmissionResource::class)->getSessionId());
});

$router->get('/test', function () use ($router) {
    var_dump(app(\App\Service\FileService::class)->addLoadFilesJobs(env('BOOK_FILE_DIR','')));
});

$router->get('/setfs/{id}', function ($id) use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->filterWantedFiles($id));
});

$router->get('/batch', function () use ($router) {
    var_dump(app(\App\Service\TorrentService::class)->batchFilterWantedFilesJob());
});

$router->get('/batch_load_files', function () use ($router) {
    var_dump(app(\App\Service\FileService::class)->addLoadFilesJobs(env('BOOK_FILE_DIR','')));
});

$router->get('/abc', function () use ($router) {
    $book = \App\Models\Book::query()->find(9282);
    var_dump($book);
});

$router->get('/example/show', 'ExampleController@show');
