<?php

namespace App\Http\Controllers;

use App\Service\BookQueryService;
use App\Service\BookService;
use App\Service\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function responseBooks($err = 0, $data, $stamp)
    {
        return
            [
                'err' => $err,
                'books' => $data,
                'stamp' => $stamp,

            ];
    }

    /**
     * 获取书籍列表
     * @param Request $request
     * @return array
     */
    public function getBooks(Request $request)
    {
        try {
            $data = $request->toArray();
            $input = [
                'size' => $data['sum'] ?? 10,
                    'topic' => $data['category'] ?? ''
            ];
            $books = app(BookQueryService::class)->get_english_books_without_queried($input);
            return $this->responseBooks(0, $books, $this->encode_stamp(array_pluck($books, 'ID')));
        } catch (\Exception $e) {
            return $this->responseBooks(10001, $e->getTraceAsString(), '');
        }

    }

//记录已经使用过的书籍
    public function record_books(Request $request)
    {
        $ids = $this->decode_stamp($request['stamp']);
        app(BookService::class)->addQueryRecord($ids);
        return ['err' => 0];
    }

    private function decode_stamp($stamp)
    {
        $data = base64_decode($stamp);
        return json_decode($data, true);
    }

    private function encode_stamp($data)
    {
        return base64_encode(json_encode($data));
    }


}
