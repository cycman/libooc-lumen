<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/7/23
 * Time: 下午11:28
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BookTopic extends Model
{

    protected $table = 'topics';
    /*
         * 数据库表主键
         *
         * @var string
         */
    protected $primaryKey = 'id';
    /*
     * 取消自动维护create_at,update_at字段
     *
     * @var string
     */
    public $timestamps = false;

    public function findByTopicIdHlId($id)
    {
        $query = self::query();
        $query->where(['topic_id_hl' => $id,]);
        return $query->get()->toArray();
    }
}