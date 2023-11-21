<?php
namespace App\GraphQL\Types;
use App\Author;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AuthorType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'MyAuthorType', // --------< here for query
        'description'   => 'Author type',
        'model'         => Author::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Id',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name',
                'resolve' => function($root, array $args) {
                    return strtolower($root->name);
                }
            ],

            'age' => [
                'type' => Type::int(),
                'description' => 'Age',
            ],

            'books' => [
                'type' => Type::listOf(GraphQL::type('MyBookType')),
                'description' => 'books',
            ],
        ];
    }

}
