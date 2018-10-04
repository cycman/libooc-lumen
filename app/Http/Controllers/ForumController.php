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
        set_time_limit(0);
        $conditions = [];
        var_dump(app(ForumService::class)->createPostJobsFromFile($conditions));
    }

}