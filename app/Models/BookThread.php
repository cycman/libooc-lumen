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
}