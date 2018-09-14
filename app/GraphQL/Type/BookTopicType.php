<?php

namespace App\GraphQL\Type;

use App\GraphQL\Query\BookQuery;
use Folklore\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;

class BookTopicType extends BaseType
{

    protected $attributes = [
        'name' => 'BookTopicType',
        'description' => 'A type'
    ];


    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the book'
            ],
            'topic_descr' => [
                'type' => Type::string(),
                'description' => 'The topic_descr of the book'
            ],
            'lang' => [
                'type' => Type::string(),
                'description' => 'The lang of the book'
            ],
            'kolxoz_code' => [
                'type' => Type::string(),
                'description' => 'The kolxoz_code of the book'
            ],
            'topic_id' => [
                'type' => Type::int(),
                'description' => 'The topic_id of the book'
            ],
            'topic_id_hl' => [
                'type' => Type::int(),
                'description' => 'The topic_id_hl of the book'
            ],


        ];
    }

}
