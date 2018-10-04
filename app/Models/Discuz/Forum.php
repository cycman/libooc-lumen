<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/4
 * Time: 上午9:35
 */

namespace App\Models\Discuz;

class Forum extends DiscuzModel
{
    protected $table = 'forum_forum';

    protected $fields = ['*',];

    public function findAll()
    {
        $query = self::query();
        $query->select($this->fields);
        return $query->get()->toArray();
    }

}