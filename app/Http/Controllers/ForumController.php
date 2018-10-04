<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/4
 * Time: 下午4:03
 */

namespace App\Http\Controllers;


use App\Service\ForumService;

class ForumController extends Controller
{

    /**
     * 创建增加帖子的任务
     */
    public function createPostJobs()
    {
        $conditions = [];
        var_dump(app(ForumService::class)->createPostJobsFromFile($conditions));
    }

}