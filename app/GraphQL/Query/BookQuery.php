<?php

namespace App\GraphQL\Query;
use App\Models\Book;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Facades\GraphQL;
class BookQuery extends Query
{



    protected $attributes = [
        'name' => 'book',
        'description' => 'A query'
    ];

    public function type()
    {
        return GraphQL::type('Book');
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $book =  Book::with('extBookDesc')->find($args['id']??null);
        return $book;
    }
}
