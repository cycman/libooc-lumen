<?php
/**
 * Created by PhpStorm.
 * User: cyc
 * Date: 2019/1/19
 * Time: 2:19 PM
 */

namespace App\Service;

use App\Models\Book;
use App\Models\BookTopic;
use App\Tool\CArray;

class BookQueryService extends BaseService
{
    /**
     * @param $input
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function lists($input)
    {
        $topic = $input['topic'];
        $offset = $input['offset'] ?? 0;
        $size = $input['size'] ?? 1;
        $query = Book::query();
        $query->leftJoin('b_file', 'updated.ID', '=', 'b_file.bid');
        $query->leftJoin('b_book_thread', 'updated.ID', '=', 'b_book_thread.bid');
        if (empty($topic)) {
            $query->where(['updated.topic' => $topic]);
        } else {
            $topics = $this->app->make(BookTopic::class)->findByTopicIdHlId($topic);
            $tids = CArray::listData($topics, 'topic_id');
            $query->whereIn('updated.topic', $tids);
        }
        $query->where(['updated.language' => 'english']);
        $query->offset($offset);
        $query->limit($size);
        return $query->with('extBookDesc')->get();
    }

    public function get_english_books_without_queried($input)
    {
        $limit = $input['size'];
        $query = Book::query();
        $query->with('extZhImf');
        $query->leftJoin('b_book_query_record', 'updated.ID', '=', 'b_book_query_record.bid');
        $query->whereNotNull('b_book_zh_imf.id');
        $query->select("updated.Title as name,b_book_zh_imf.title as translation, b_book_zh_imf.descr as brief");
        $query->rightJoin('b_file', 'updated.ID', '=', 'b_file.bid');
        if (!empty($input['topic'])) {
            $query->where(['updated.topic' => $input['topic']]);
        }
        $query->where(['updated.language' => 'english']);
        $query->limit($limit);
        return $query->get()->toArray();
    }
}
