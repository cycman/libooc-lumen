<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2019/1/19
 * Time: 2:19 PM
 */

namespace App\Service;


use Illuminate\Support\Facades\DB;

class BookService extends BaseService
{
    public function addQueryRecord($ids)
    {
        $input = [];
        foreach ($ids as $id) {
            $input[] = ['bid' => $id,];
        }
        Db::table("b_book_query_record")->insert($input);

    }
}
