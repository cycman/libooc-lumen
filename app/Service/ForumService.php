<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2018/10/4
 * Time: 上午10:13
 */

namespace App\Service;


use App\Jobs\CreatePostJob;
use App\Models\Book;
use App\Models\BookThread;
use App\Models\File;
use Illuminate\Support\Facades\DB;

class ForumService extends BaseService
{
    protected $config;

    public function __construct()
    {
        $this->config = $this->app->make('config')->get('forum');
    }


    /**
     * @param array $conditions
     */
    public function createPostJobsFromFile(array $conditions)
    {
        $topicMapForumIds = $this->config['topicmapforumids'];
        $pageSize = 100;
        $pageNum = 1;
        $jobs = [];
        $query = $this->app->make(File::class)::query()->where($conditions)->with('extBook')->paginate($pageSize, ['*'], 'page', $pageNum);
        while (true) {
            foreach ($query as $file) {
                $file = $file->toArray();
                if (!isset($file['ext_book']['Topic'])) {
                    continue;
                }
                $fid = $file['ext_book']['Topic'];
                $fid = empty($fid) ? '' : $fid;
                $jobs[$fid][] = $file['bid'];
            }
            if (!$query->hasMorePages()) {
                break;
            }
            $pageNum++;
            $query = $this->app->make(File::class)::query()->paginate($pageSize, ['*'], 'page', $pageNum);
        }
        foreach ($jobs as $topicId => $bids) {
            if (!isset($topicMapForumIds[$topicId])) {
                continue;
            }
            dispatch($this->app->makeWith(CreatePostJob::class, ['ids' => $bids, 'fid' => $topicMapForumIds[$topicId]]));
        }
    }

    /**
     * 跟主题发表对应的帖子列表
     * @param $fid
     * @param array $args
     */
    public function deployPost($fid, $args = [])
    {
        $pageSize = 1;
        $pageNum = 1;
        while (true) {
            $query = app(Book::class)->pageQueryByIds($args, $pageSize, $pageNum);
            foreach ($query as $book) {
                $book = $book->toArray();
                $bookThread = $this->app->make(BookThread::class);
                if ($bookThread->existsByMd5AndFid($book['MD5'], $fid)) {
                    continue;
                }
                $thread = <<<json
                {
                    "fid": 2,
                    "posttableid": 0,
                    "typeid": 0,
                    "sortid": 0,
                    "readperm": 0,
                    "price": 0,
                    "author": "admin",
                    "authorid": 1,
                    "subject": "abc",
                    "dateline": 1531652229,
                    "lastpost": 1531652229,
                    "lastposter": "admin",
                    "views": 1,
                    "replies": 0,
                    "displayorder": 0,
                    "highlight": 0,
                    "digest": 0,
                    "rate": 0,
                    "special": 0,
                    "attachment": 0,
                    "moderated": 0,
                    "closed": 0,
                    "stickreply": 0,
                    "recommends": 0,
                    "recommend_add": 0,
                    "recommend_sub": 0,
                    "heats": 0,
                    "status": 32,
                    "isgroup": 0,
                    "favtimes": 0,
                    "sharetimes": 0,
                    "stamp": -1,
                    "icon": 20,
                    "pushedaid": 0,
                    "cover": 0,
                    "replycredit": 0,
                    "relatebytag": "0",
                    "maxposition": 1,
                    "bgcolor": "",
                    "comments": 0,
                    "hidden": 0
                }
json;
                $post = <<<json
		{
			"first": 1,
			"author": "admin",
			"authorid": 1,
			"dateline": 1531661875,
			"useip": "127.0.0.1",
			"port": 53522,
			"invisible": 0,
			"anonymous": 0,
			"usesig": 1,
			"htmlon": 0,
			"bbcodeoff": 0,
			"smileyoff": -1,
			"parseurloff": 0,
			"attachment": 0,
			"rate": 0,
			"ratetimes": 0,
			"status": 0,
			"comment": 0,
			"replycredit": 0,
			"position": 1
		}
json;

                $subject = $this->genPostBookTitle($book);
                $message = $this->genPostBookContent($book);
                $dateLine = time() + rand(10, 60);//创建时间
                $lastPost = $dateLine;//最后发布时间
                $thread = json_decode($thread, true);
                $thread['fid'] = $fid;
                $thread['icon'] = 10;//帖子的类型 20 新人帖  10热帖 -1什么都没有
                $thread['subject'] = $subject;
                $thread['dateline'] = $dateLine;
                $thread['lastpost'] = $lastPost;

                $tid = DB::connection('mysql_discuz')->table('forum_thread')->insertGetId(
                    $thread
                );
                // C::t('forum_newthread')->insert(['tid'=>$tid,'fid'=>$fid,'dateLine'=>time()]);
                $post = json_decode($post, true);

                $post['fid'] = $fid;
                $post['tid'] = $tid;
                $post['message'] = $message;
                $post['subject'] = $subject;
                $post['dateline'] = $dateLine;
                $post['htmlon'] = 1;
                $maxPid = Db::connection('mysql_discuz')->table('forum_post_tableid')->getCountForPagination() + 1;
                $post['pid'] = $maxPid;
                Db::connection('mysql_discuz')->table('forum_post_tableid')->insert(['pid' => $maxPid]);
                Db::connection('mysql_discuz')->table('forum_post')->insert($post);
                $bookThread = $this->app->make(BookThread::class);
                $bookThread->bid = $book['ID'];
                $bookThread->md5 = $book['MD5'];
                $bookThread->fid = $fid;
                $bookThread->tid = $tid;
                $bookThread->save();
            }
            $pageNum += 1;
            if (!$query->hasMorePages()) {
                break;
            }
        }
    }

    function genPostBookTitle(array $book)
    {
        return sprintf('%s [%s] -[%s]-[%s]-[%s]-[%s]', $book['Title'], $book['Edition'], $book['Year'], $book['Extension'], $book['Publisher'], $book['Author']);//标题
    }

    function genPostBookContent($book)
    {
        $title = $book['Title'];
        $language = $book['Language'];
        $page = $book['Pages'];
        $year = $book['Year'];
        /** @var book $book */
        $contentLines = [];
        $imgUrl = $this->genPostBookCoverImageLink($book);
        $img = sprintf('[img]%s[/img]', $imgUrl);
        $contentLines[] = $img;
        $bookDetail = $this->tag('strong', $this->tag('font', '书籍信息', ['color' => 'red']), []);
        $contentLines[] = $bookDetail;
        //language
        $bookTitle = $this->tag('strong', '标题：' . $title, []);
        $contentLines[] = $bookTitle;
        $tagLanguage = $this->tag('strong', '语言：' . $language, []);
        $contentLines[] = $tagLanguage;

        $size = $this->tag('strong', '大小：' . $this->getSize('m', $book['Filesize']), []);
        $contentLines[] = $size;
        $page = $this->tag('strong', '页数：' . $page, []);
        $contentLines[] = $page;
        $year = $this->tag('strong', '日期：' . $year, []);
        $contentLines[] = $year;
        $author = $book['Author'];
        $author = $this->tag('strong', '作者：' . $author, []);
        $contentLines[] = $author;
        $edition = $book['Edition'];

        $edition = $this->tag('strong', '版本：' . $edition, []);
        $contentLines[] = $edition;
        $publisher = $book['Publisher'];

        $publisher = $this->tag('strong', '出版社：' . $publisher, []);
        $contentLines[] = $publisher;
        $subDes = $this->tag('strong', $this->tag('font', '简介', ['color' => 'red']), []);
        $descr = $book['ext_book_desc']['descr'];
        $des = $this->tag('blockquote', $subDes . '<br>' . $descr, ['class' => 'quote']);
        $description = $this->tag('div', $des, ['class' => 'quote']);
        $contentLines[] = $description;

        //preview
        if (strtolower($book['Extension']) == 'pdf') {
            $preview = $this->tag('a', "免费下载预览文件", ['href' => $this->genPostBookPreviewLink($book)]);
            $contentLines[] = $preview;
        }

        $sourceDes = $this->tag('strong', $this->tag('font', '电子书下载地址回复可见:', ['color' => 'red']), []);
        $contentLines[] = $sourceDes;
        $hiddenLink = sprintf('[hide]%s[/hide]', $this->genPostBookDownLink($book));
        $contentLines[] = $hiddenLink;
        return implode('<br>', $contentLines);
    }

    function genPostBookDownLink(array $book)
    {
        $md5 = $book['MD5'];
        $host = 'http://source.libooc.com';
        $queryData = [];
        $queryData['models'] = 'books';
        $queryData['md5'] = $md5;
        $queryData = http_build_query($queryData);
        return sprintf('%s?%s', $host, $queryData);
    }

    function genPostBookPreviewLink(array $book)
    {
        $md5 = $book['MD5'];
        $host = 'http://source.libooc.com';
        $queryData = [];
        $queryData['models'] = 'books_preview';
        $queryData['md5'] = $md5;
        $queryData = http_build_query($queryData);
        return sprintf('%s?%s', $host, $queryData);
    }

    function genPostBookCoverImageLink($book)
    {
        $host = 'http://source.libooc.com';
        $queryData = [];
        $queryData['models'] = 'covers';
        $queryData['location'] = $book['Coverurl'];
        $queryData = http_build_query($queryData);
        return sprintf('%s?%s', $host, $queryData);
    }

    public function tag($tag, $content, $attrs)
    {
        $htmlAttrs = [];
        foreach ($attrs as $attrKey => $value) {
            $htmlAttrs[] = "{$attrKey}={$value}";
        }
        $htmlAttrs = implode(' ', $htmlAttrs);
        return "<{$tag} {$htmlAttrs}>{$content}</{$tag}>";
    }

    public function getSize($format = 'm', $size = 0)
    {
        switch ($format) {
            case 'm':
                $size = number_format(($size / 1024 / 1024), 2) . 'm';
                break;
        }
        return $size;
    }

}