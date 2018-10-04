<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/2
 * Time: 上午12:38
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    const FILE_EXTENSION_PDF = 'pdf';
    protected $table = 'b_file';
    protected $columns = ['id', 'name', 'md5'];


    public function extBook()
    {
        return $this->hasOne('App\Models\Book', 'ID', 'bid');
    }

    public function findFilesByMd5s(array $md5s, array $columns = []): array
    {
        $query = self::query();
        $query->select(empty($columns)?$this->columns:$columns)
            ->whereIn('md5', $md5s);
        return $query->get()->toArray();
    }

    public function batchSaveFiles(array $files =[])
    {
        return DB::table($this->table)->insert($files);
    }

    public function findFilesByArgs(array $args, array $columns = ['*']): array
    {
        $query = self::query();
        $query->select(empty($columns)?$this->columns:$columns)
            ->where($args);
        return $query->get()->toArray();
    }

}