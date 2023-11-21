<?php

namespace App\GraphQL\Types;

use App\Book;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BookType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'MyBookType',
        'description'   => 'book type',
        // Note: only necessary if you use `SelectFields`
        'model'         => Book::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Id',
                // Use 'alias', if the database column is different from the type name.
                // This is supported for discrete values as well as relations.
                // - you can also use `DB::raw()` to solve more complex issues
                // - or a callback returning the value (string or `DB::raw()` result)
                // 'alias' => 'book_id',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title',
                'resolve' => function($root, array $args) {
                    // If you want to resolve the field yourself,
                    // it can be done here
                    return strtolower($root->title);
                }
            ],

            'year' => [
                'type' => Type::int(),
                'description' => 'Year',
            ],

            'number_of_page' => [
                'type' => Type::int(),
                'description' => 'Number Of Page',
            ],
            'author' => [
                'type' => GraphQL::type('MyAuthorType'),
                'description' => 'Author Type',
            ],

        ];
    }

}
