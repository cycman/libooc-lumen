<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;

class BookDescType extends BaseType
{
    protected $attributes = [
        'name' => 'BookDesc',
        'description' => 'A BookDesc'
    ];

    public function fields()
    {
        return [
            'descr' => [
                'type' => Type::string(),
                'description' => 'The descr of the book'
            ],

        ];
    }

}
