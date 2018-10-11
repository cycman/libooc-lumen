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
$router->get('/taobao/create_csv/{name}', function ($name){
    var_dump(app(\App\Service\TaoBaoCsvService::class)->createTaoBaoCsvByTopic(
        $name,
        $_GET['topic'],
        $_GET['offset'],
        $_GET['size']));
});

