<?php


namespace App\GraphQL\Queries;

use Closure;
use App\models\Book;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class bookQuery extends Query
{
    protected $attributes = [
        'name' => 'myBookQuery',
    ];

    public function type(): Type
    {
        return GraphQL::type('MyBookType') ;
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Book::find($args['id']);
    }
}
