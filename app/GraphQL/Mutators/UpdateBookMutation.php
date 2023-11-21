<?php


namespace App\GraphQL\Mutators;

use Closure;

use App\Models\Book;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateBookMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'UpdateMyBook'
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

            'title' => [
                'type' => Type::string(),
                'name' => 'title'
            ],

            'year' => [
                'type' => Type::int(),
            ],

            'number_of_page' => [
                'type' => Type::int(),
            ],
            'author_id' => [
                'type' => Type::int(),
            ],



        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $book = Book::find($args['id']);
        $book->fill($args);
        $book->save();
        return $book ;
    }

}

