<?php


namespace App\GraphQL\Mutators;

use Closure;

use App\Models\Book;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteBookMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'DeleteMyBook'
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type('MyBookType'));
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $book = Book::find($args['id']);
        $book->delete();
        return $book ;
    }

}

