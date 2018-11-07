<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/9/13
 * Time: 下午7:59
 */

namespace App\Service;

use App\Jobs\InsertZhBookImfJob;
use App\Models\Book;
use App\Models\ZhBookImf;
use App\Resource\QinghuaTranslateResource;


class ZhBookImfService extends BaseService
{
    protected $translateResource;

    public function __construct(QinghuaTranslateResource $translateResource)
    {
        $this->translateResource = $translateResource;
    }


    public function genInsertImfJobs()
    {
        $maxSize = Book::query()->count('*');
        $offset = 0;
        while ($maxSize > $offset) {
            $query = Book::query();
            $query->rightJoin('b_file', 'updated.ID', '=', 'b_file.bid');
            $query->where(['updated.language' => 'english']);
            $query->offset($offset);
            $query->limit(1);
            $books = $query->with('extZhImf')->get()->toArray();
            $bookIds = [];
            foreach ($books as $book) {
                if (!isset($book['ext_zh_imf'])) {
                    $bookIds[] = $book['ID'];
                }
            }
            $offset += 10;
            if (!empty($bookIds)) {
                dispatch(app()->makeWith(InsertZhBookImfJob::class, ['ids' => $bookIds,]));
            }
        }
    }

    /**
     * 批量插入书籍中文拓展信息
     * @param array $bids
     */
    public function batchInsertZhBookImfs(array $bids = [])
    {
        ZhBookImf::query()->whereIn('bid', $bids)->delete();
        $books = Book::query()->whereIn('id', $bids)->with('extBookDesc')->get()->toArray();
        $enTitles = [];
        $enDescs = [];
        foreach ($books as $book) {
            $enDescs[] = !isset($book['ext_book_desc']['descr']) || empty($book['ext_book_desc']['descr']) ? 'empty':$book['ext_book_desc']['descr'];
            $enTitles[] = empty($book['Title']) ? 'empty' : $book['Title'];
        }
        $chTitles = $this->translateResource->translateEnToZh($enTitles);
        $chDescs = $this->translateResource->translateEnToZh($enDescs);
        foreach ($books as $index => $book) {
            $zhBookimf = ['bid' => $book['ID'], 'md5' => $book['MD5'], 'descr' => $chDescs[$index]??'', 'title' => $chTitles[$index]??'',];
            $zhBookimfModel = new ZhBookImf();
            $zhBookimfModel->setRawAttributes($zhBookimf);
            $zhBookimfModel->save();
        }
    }


}
