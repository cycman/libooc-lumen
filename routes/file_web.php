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
$router->get('/file/batch_load_files', 'FileController@batchLoadFiles');
$router->get('/file/preview_file/{md5}', 'FileController@previewFile');
$router->get('/file/download_file/{md5}', 'FileController@downloadFile');

