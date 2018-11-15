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
use App\Resource\AliTranslateResource;
use App\Resource\NiuTranslateResource;
use App\Resource\QinghuaTranslateResource;


class ZhBookImfService extends BaseService
{
    protected $translateResource;

    public function __construct(AliTranslateResource $aliTranslateResource)
    {
        $this->translateResource = $aliTranslateResource;
    }


    public function genInsertImfJobs()
    {
        $maxSize = File::query()->count('*');
        $offset = 0;
        while ($maxSize > $offset) {
            $query = File::query();
            $query->select(['b_file.bid', 'b_book_zh_imf.descr','b_book_zh_imf.title']);
            $query->offset($offset);
            $query->limit(1000);
            $books = $query->leftJoin('b_book_zh_imf', 'b_book_zh_imf.bid', '=', 'b_file.bid')->get()->toArray();
            $bookIds = [];
            foreach ($books as $book) {
                if (isset($book['bid']) && empty($book['descr']) && empty($book['title'])) {
                    $bookIds[] = $book['bid'];
                }
                if (count($bookIds)>=10) {
                    dispatch(app()->makeWith(InsertZhBookImfJob::class, ['ids' => $bookIds,]));
                    $bookIds = [];
                }
            }
            if (!empty($bookIds)) {
                dispatch(app()->makeWith(InsertZhBookImfJob::class, ['ids' => $bookIds,]));
            }
            $offset += 1000;
        }
    }

    /**
     * 批量插入书籍中文拓展信息
     * @param array $bids
     */
    public function batchInsertZhBookImfs(array $bids = [])
    {
        ZhBookImf::query()->whereIn('bid', $bids)->delete();
        $books = Book::query()
            ->where('updated.language','=','english')
            ->whereIn('updated.ID', $bids)
            ->leftJoin((new BookDesc())->getTable(), 'updated.MD5', '=', 'description.md5')
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
