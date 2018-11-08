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
use App\Models\BookDesc;
use App\Models\File;
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
        $maxSize = File::query()->count('*');
        $offset = 0;
        while ($maxSize > $offset) {
            $query = File::query();
            $query->leftJoin('updated', 'updated.ID', '=', 'b_file.bid');
            $query->where(['updated.language' => 'english']);
            $query->offset($offset);
            $query->limit(1);
            $books = $query->leftJoin('b_book_zh_imf', 'b_book_zh_imf.md5', '=', 'b_file.md5')->get()->toArray();
            $bookIds = [];
            foreach ($books as $book) {
                if (isset($book['ID']) && empty($book['descr']) && empty($book['title'])) {
                    $bookIds[] = $book['ID'];
                }
            }
            $offset += 1;
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
        $books = Book::query()->whereIn('updated.ID', $bids)->leftJoin((new BookDesc())->getTable(), 'updated.MD5', '=', 'description.md5')
            ->get()
            ->toArray();
        $enTitles = [];
        $enDescs = [];
        foreach ($books as $book) {
            $enDescs[] = (!isset($book['descr']) || empty($book['descr'])) ? 'empty' : $book['descr'];
            $enTitles[] = empty($book['Title']) ? 'empty' : $book['Title'];
        }
        $chTitles = $this->translateResource->translateEnToZh($enTitles);
        $chDescs = $this->translateResource->translateEnToZh($enDescs);
        foreach ($books as $index => $book) {
            $zhBookimf = ['bid' => $book['ID'], 'md5' => $book['MD5'], 'descr' => $chDescs[$index] ?? '', 'title' => $chTitles[$index] ?? '',];
            $zhBookimfModel = new ZhBookImf();
            $zhBookimfModel->setRawAttributes($zhBookimf);
            $zhBookimfModel->save();
        }
    }


}
