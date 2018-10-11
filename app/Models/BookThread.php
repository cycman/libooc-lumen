<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/4
 * Time: 下午6:01
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BookThread extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $table = 'b_book_thread';

    public function existsByMd5AndFid($md5, $fid)
    {
        $query = self::query();
        $query->where(['md5' => $md5, 'fid' => $fid,]);
        return $query->exists();
    }

    public function deleteByTid($tid)
    {
        $query = self::query();
        $query->where(['tid' => $tid]);
        return $query->delete();
    }

    public function findByMd5($md5)
    {
        $query = self::query();
        $query->where(['md5' => $md5]);
        return $query->first();
    }

    public function findAll()
    {
        $query = self::query();
        return $query->get()->toArray();
    }
}