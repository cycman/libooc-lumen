<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/9/13
 * Time: 下午7:59
 */

namespace App\Service;


use App\Entity\Resource\TransmissionResource;
use App\Jobs\Torrent\SetTorrentFilesJob;
use App\Models\Book;
use App\Tool\CArray;

class TorrentService extends BaseService
{
    protected $transmissionResource;

    public function __construct(TransmissionResource $transmissionResource)
    {
        $this->transmissionResource = $transmissionResource;
    }


    /**
     * 根据id加载torrent文件
     * @param int $id
     * @return bool
     * @throws \App\Exceptions\CurlException
     * @throws \App\Exceptions\CurlException
     * @throws \Exception
     */
    public function loadTorrent(int $id)
    {
        $torrent = app(TransmissionResource::class)->getTorrentById($id, ['files']);
        try {
            app('db')->beginTransaction();
//            $torrentModel = new Torrent();
//            $torrentModel->setRawAttributes($torrent)->save();
            app('db')->commit();
        } catch (\Exception $e) {
            app('db')->rollBack();
            throw  $e;
        }
        return true;
    }

    /**
     * 根据条件过滤出种子文件中符合条件的文件
     * @param $id
     * @param array $conditions
     * @return mixed
     * @throws \App\Exceptions\CurlException
     */
    public function filterWantedFiles($id, array $conditions = [])
    {
        $conditions = array_merge(
            [
                'extension' => 'pdf',
                'language' => 'english',
                'fileSize' => 1024 * 1024 * 10
            ], $conditions);

        $torrent = app(TransmissionResource::class)->getTorrentById($id, ['files']);
        $files = $torrent['files'];
        $md5Arr = array_map(function ($file) {
            $name = $file['name'];
            return explode('/', $name)[1];
        }, $files);
        $books = app(Book::class)->findBooksByMd5s($md5Arr, ['md5', 'fileSize', 'extension', 'language']);
        foreach ($books as &$book) {
            $book['md5'] = strtoupper($book['md5']);
        }
        $books = CArray::setArrayKey($books, 'md5');
        $fileIndex = [];
        $wanted = [];
        $unwanted = [];
        foreach ($files as $index => $file) {
            $md5 = strtoupper(explode('/', $file['name'])[1]);
            if (isset($books[$md5])) {
                $book = $books[$md5];
                if (
                    strcasecmp($book['extension'], $conditions['extension']) == 0 &&
                    strcasecmp($book['language'], $conditions['language']) == 0 &&
                    $book['fileSize'] <= $conditions['fileSize']) {
                    $wanted[] = $index;
                    continue;
                }
            }
            $unwanted[] = $index;
        }

        $fileIndex['wanted'] = $wanted;
        $fileIndex['unwanted'] = $unwanted;
        return app(TransmissionResource::class)->setFiles($id, $fileIndex);
    }


    /**
     * 生成种子文件设置file任务
     * @throws \App\Exceptions\CurlException
     */
    public function batchFilterWantedFilesJob()
    {
        $ids = $this->transmissionResource->getTorrentIds();
        foreach ($ids as $name => $id) {
            dispatch($this->app->makeWith(SetTorrentFilesJob::class, ['id' => $id, 'name' => $name]));
        }
    }

}
