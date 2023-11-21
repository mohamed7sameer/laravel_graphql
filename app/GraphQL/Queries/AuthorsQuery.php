<?php
namespace App\GraphQL\Queries;

use Closure;
use App\models\Author;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class AuthorsQuery extends Query
{
    protected $attributes = [
        'name' => 'myAuthorsQuery',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('MyAuthorType')))); // ---------< here
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
            return Author::where('id',$args['id'])->get();
        }
        return Author::all();
        // ----------------------------
    }
}
