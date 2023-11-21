<?php


namespace App\GraphQL\Queries;

use Closure;
use App\models\Book;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class booksQuery extends Query
{
    protected $attributes = [
        'name' => 'myBooksQuary', // ---------< here
    ];

    public function type(): Type
    {
        // return Type::listOf(GraphQL::type('MyBookType')) ;   // ---------< here
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('MyBookType')))); // ---------< here
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
        if (isset($args['id'])) {
            return Book::where('id',$args['id'])->get();
        }
        return Book::all();
    }
}
