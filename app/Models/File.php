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

}