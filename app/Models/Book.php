<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/7/7
 * Time: 下午11:10
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'updated';
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

    public function extBookDesc()
    {
        return $this->hasOne('App\Models\BookDesc', 'md5', 'MD5');
    }

    public function extZhImf()
    {
        return $this->hasOne('App\Models\ZhBookImf', 'md5', 'md5');
    }

    /**
     * 根据id列表查询book列表
     * @param array $ids
     * @param array $fields
     * @return array
     */
    public function findBooksByIds(array $ids, array $fields = [])
    {
        $query = self::query();
        $query->select($fields)
            ->whereIn('id', $ids);
        return $query->get()->toArray();
    }


    /**
     * 根据md5列表查询book列表
     * @param array $md5s
     * @param array $fields
     * @return array
     */
    public function findBooksByMd5s(array $md5s, array $fields = [])
    {
        $query = self::query();
        $query->select($fields)
            ->whereIn('md5', $md5s);
        return $query->get()->toArray();
    }

    public function pageQueryByIds(array $args, $pageSize = 15, $pageNum = 1)
    {
        $query = self::query();
        $query->whereIn('ID',$args['ID']??[]);
        $query->with('extBookDesc');
        return $query->paginate($pageSize, $this->fields, 'page', $pageNum);
    }

}