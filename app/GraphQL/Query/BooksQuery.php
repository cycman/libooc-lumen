<?php

namespace App\GraphQL\Query;

use App\Models\Book;
use Folklore\GraphQL\Support\Query;
use Folklore\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class BooksQuery extends Query
{
    protected $attributes = [
        'name' => 'books',
        'description' => 'A  books query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Book'));
    }

    public function args()
    {
        return  [
            'page' => ['name' => 'page', 'type' => Type::int()],
            'size' => ['name' => 'size', 'type' => Type::int()],
            'topic' => ['name' => 'topic', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $topic = $args['topic'] ?? '';
        $page = $args['page'] ?? 0;
        $size = $args['size'] ?? 1;
        $query = Book::with('extBookDesc');
        if (is_numeric($topic)) {
            $query->where('topic', '=', $topic);
        } else {
            $query->where('topic', 'like', sprintf('%%s%', $topic));
        }
        $query->forPage($page, $size);
        return array_map(function ($item){
            $item['Desc'] = $item['ext_book_desc']['descr'];
            return $item;
        },$query->get()->toArray());
    }
}
