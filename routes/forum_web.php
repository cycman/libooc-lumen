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
$router->get('/forum/create_post_jobs', 'ForumController@createPostJobs');
$router->get('/forum/delete_error_forums', 'ForumController@deleteErrorPost');

