<?php

namespace App\GraphQL\Type;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;

class BookType extends BaseType
{

    protected $attributes = [
        'name' => 'BookType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'ID' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the book'
            ],
            'Title' => [
                'type' => Type::string(),
                'description' => 'The title of book'
            ],
            'Series' => [
                'type' => Type::string(),
                'description' => 'The Series of book'
            ],
            'Author' => [
                'type' => Type::string(),
                'description' => 'The Author of book'
            ],
            'Year' => [
                'type' => Type::string(),
                'description' => 'The Year of book'
            ],
            'Edition' => [
                'type' => Type::string(),
                'description' => 'The Edition of book'
            ],
            'Publisher' => [
                'type' => Type::string(),
                'description' => 'The Publisher of book'
            ],
            'Pages' => [
                'type' => Type::string(),
                'description' => 'The Pages of book'
            ],
            'Language' => [
                'type' => Type::string(),
                'description' => 'The Language of book'
            ],
            'Topic' => [
                'type' => Type::string(),
                'description' => 'The Topic of book'
            ],
            'Identifier' => [
                'type' => Type::string(),
                'description' => 'The Identifier of book'
            ],
            'Filesize' => [
                'type' => Type::string(),
                'description' => 'The Filesize of book'
            ],
            'Extension' => [
                'type' => Type::string(),
                'description' => 'The Extension of book'
            ],
            'MD5' => [
                'type' => Type::string(),
                'description' => 'The MD5 of book'
            ],
            'Locator' => [
                'type' => Type::string(),
                'description' => 'The Locator of book'
            ],
            'Coverurl' => [
                'type' => Type::string(),
                'description' => 'The Coverurl of book'
            ],
            'Tags' => [
                'type' => Type::string(),
                'description' => 'The Tags of book'
            ],
            'Desc' => [
                'type' => Type::string(),
                'description' => 'The Desc of book'
            ],
        ];
    }

}
