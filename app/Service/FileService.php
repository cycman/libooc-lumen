<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/9/14
 * Time: 上午11:35
 */

namespace App\Service;


use App\Entity\Resource\TransmissionResource;
use App\Jobs\DeleteFileJob;
use App\Jobs\File\LoadFilesJob;
use App\Models\Book;
use App\Models\File;
use App\Service\File\PreviewFileService;
use App\Tool\CArray;

class FileService extends BaseService
{
    protected $transmissionResource;
    protected $fileDir;

    public function __construct(TransmissionResource $transmissionResource, $fileDir = '')
    {
        $this->transmissionResource = $transmissionResource;
        $this->fileDir = empty($fileDir) ? env('TRANSMISSION_DOWNLOAD_DIR', './') : $fileDir;
    }

    /**
     * @param array $conditions
     * @throws \App\Exceptions\CurlException
     */
    public function addDeleteJobs(array $conditions = [])
    {
        $conditions = array_merge(
            [
                'extension' => 'pdf',
                'language' => 'english',
                'fileSize' => 1024 * 1024 * 10
            ], $conditions);

        $ids = $this->transmissionResource->getTorrentIds();
        foreach ($ids as $name => $id) {
            $torrent = app(TransmissionResource::class)->getTorrentById($id, ['files']);
            $files = $torrent['files'];
            $md5Arr = array_map(function ($file) {
                $name = $file['name'];
                return explode('/', $name)[1];
            }, $files);
            $books = app(Book::class)->findBooksByMd5s($md5Arr, ['md5', 'fileSize', 'extension', 'language']);
            $books = CArray::setArrayKey($books, 'md5');
            foreach ($files as $index => $file) {
                $md5 = explode('/', $file['name'])[1];
                if (isset($books[$md5])) {
                    $book = $books[$md5];
                    if (
                        strcasecmp($book['extension'], $conditions['extension']) != 0 ||
                        strcasecmp($book['language'], $conditions['language']) != 0 ||
                        $book['fileSize'] > $conditions['fileSize']) {
                        $wanted[] = $index;
                        dispatch($this->app->makeWith(DeleteFileJob::class, ['id' => $id, 'name' => $name]));
                    }
                }
            }

        }
    }

    public function addLoadFilesJobs($fileDir)
    {
        $dirs = [];
        $d = dir($fileDir);
        while (false !== ($entry = $d->read())) {
            if ($entry != '.' && $entry != '..' && is_dir($fileDir . $entry))
                $dirs[] = $fileDir . $entry . '/';
        }
        $d->close();
        foreach ($dirs as $dir) {
            dispatch($this->app->makeWith(LoadFilesJob::class, ['fileDir' => $dir]));
        }
    }


    /**
     * 从路径中初始化文件
     * @param $dir
     * @return bool
     */
    public function loadFilesFromDir($dir)
    {
        $md5List = [];
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            if ($entry != '.' && $entry != '..' && !str_contains($entry, '.part') && is_file($dir . $entry))
                $md5List[] = $entry;
        }
        $d->close();
        $books = $this->app->make(Book::class)->findBooksByMd5s($md5List, ['id', 'title', 'md5', 'extension', 'Filesize']);
        foreach ($books as &$book) {
            $book['md5'] = strtoupper($book['md5']);
        }
        $books = CArray::setArrayKey($books, 'md5');
        $fileMd5s = CArray::listData($this->app->make(File::class)->findFilesByMd5s($md5List, ['md5']), 'md5');
        $files = [];
        foreach ($md5List as $md5) {
            if (in_array($md5, $fileMd5s)) {
                continue;
            }
            if (!key_exists(strtoupper($md5), $books)) {
                continue;
            }
            $book = $books[strtoupper($md5)];
            $file['md5'] = $md5;
            $file['bid'] = $book['id'];
            $file['name'] = $book['title'];
            $file['extension'] = $book['extension'];
            $file['file_size'] = $book['Filesize'];
            $file['locator'] = sprintf('%s/%s', intval(intval($book['id']) / 1000) * 1000, $md5);
            $file['updated_at'] = date('y-m-d h:i:s', time());
            $file['created_at'] = date('y-m-d h:i:s', time());
            $files[] = $file;
        }
        return $this->app->make(File::class)->batchSaveFiles($files);
    }

    /**
     * 预览文件
     * @param $md5
     * @return array
     * @throws \Exception
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function previewFile($md5)
    {
        $files = $this->app->make(File::class)->findFilesByMd5s([$md5,], ['name', 'extension', 'md5', 'locator']);
        if (empty($files)) {
            throw new \Exception("{$md5}文件不存在。", 1);
        }
        $file = array_pop($files);
        $previewService = $this->app->make(PreviewFileService::class);
        $extension = $file['extension'];
        $previewFilePath = '';
        switch (strtolower($extension)) {
            case 'pdf':
                $filePath = env("BOOK_FILE_DIR", '') . $file['locator'];
                $previewFilePath = $previewService->makePreviewPdfFile($filePath, '', 10);
                break;
            default:
                throw new \Exception("{$file['name']},暂不支持预览", 2);
                break;
        }
        return ['name' => $file['name'] . '.' . $extension, 'path' => $previewFilePath,];
    }

    /**
     * @param $md5
     * @return array
     * @throws \Exception
     */
    public function downloadFile($md5)
    {
        $files = $this->app->make(File::class)->findFilesByMd5s([$md5,], ['name', 'extension', 'md5', 'locator']);
        if (empty($files)) {
            throw new \Exception("文件不存在。", 1);
        }
        $file = array_pop($files);
        $extension = $file['extension'];
        $filePath = env("BOOK_FILE_DIR", '') . $file['locator'] ;
        return ['name' => $file['name'] . '.' . $extension, 'path' => $filePath,];
    }

    /**
     * @param int $id
     * @param string $name
     * @return bool
     * @throws \App\Exceptions\CurlException
     */
    public function deleteFile(int $id, string $name)
    {
        $torrent = $this->transmissionResource->getTorrentById($id, ['name']);
        $file = $this->fileDir . '/' . $torrent['name'] . '/' . $name;
        if (!file_exists($file) || unlink($file)) {
            return true;
        }
        return false;
    }

}