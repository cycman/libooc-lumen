<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/7/7
 * Time: 下午11:10
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BookQueryRecord extends Model
{
    protected $table = 'b_book_query_record';
    /*
         * 数据库表主键
         *
         * @var string
         */
    protected $primaryKey = 'ID';

    /*
     * 取消自动维护create_at,update_at字段
     *
     * @var string
     */
    public $timestamps = false;
    public $fields = ['*'];

}