
[youtube](https://www.youtube.com/watch?v=F1xjR_eaHus&list=PLFsTDfETmB8byUeU_8QuOtA3meagr4ob9&index=5)


[https://github.com/rebing/graphql-laravel](https://github.com/rebing/graphql-laravel)




# بسم الله الرحمن الرحيم

```shell
composer create-project laravel/laravel laravel_graphql
composer require rebing/graphql-laravel
php artisan vendor:publish --provider="Rebing\GraphQL\GraphQLServiceProvider"

php artisan make:model -m Author
php artisan make:model -m Book

```

```php
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->timestamps();
        });
    }
```

```php
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('year');
            $table->integer('number_of_page');
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->timestamps();
        });
    }
```


```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $guarded = []; // --------< here

    // --------here---------------
    public function books()
    {
        return $this->hasMany(Book::class);
    }
    // -----------------------
}
```


```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $guarded  = [];  // --------< here

    // --------here---------------
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    // -----------------------
}
```



```shell
php artisan migrate:fresh
php artisan make:factory AuthorFactory
php artisan make:factory BookFactory
```


```php
return [
    'name' => fake()->name,
    'age' =>  rand(15,100),
];
```


```php
return [
    'title' => fake()->name,
    'year' =>  fake()->date('Y'),
    'number_of_page' => rand(10,1000),
    'author_id' => Author::inRandomOrder()->first()->id,
];
```

```php
Author::factory(3)->create();
Book::factory(10)->create();
```

```shell
php artisan db:seed
```


# Query

## جلب بيانات الكتب


```php
<?php
namespace App\GraphQL\Types;
use App\Book;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BookType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'MyBookType', // --------< here for query
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

        ];
    }

}
```


```php
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
        'name' => 'myBookQuery', // --------------< here for query
    ];

    public function type(): Type
    {
        return GraphQL::type('MyBookType') ; // --------< here from type
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

    public function resolve($root, array $args)
    {
            return Book::find($args['id']);
    }
}
```

لاحظ اننا استخدما find ومستخدمناش where
لو كنا استخدمنا where كانت هترجع الداتا عادي لكن داخل array 
فكان هيكون array في عنصر واحد وهو الداتا بتاعتي 
فمكنش ليها داعي لكن مكنتش هتسبب مشاكل عادي 


```php
// config/graphql.php
'schemas' => [
    'default' => [
        'query' => [
            bookQuery::class, //---------< here
        ],
    ],
],

'types' => [
        BookType::class, //---------< here
],
```


### use postman


```url
http://laravel_graphql.test/graphql
```

```js
query MyBookQuary {
    myBookQuery(id: "1") {
        id
        title
        year
        number_of_page
    }
```


### لاحظ 

لاحظ ان في ال config/graphQl ممكن اعمل ال query بالشكل ده 

```php
// config/graphql.php
'schemas' => [
    'default' => [
        'query' => [
            'aaa' => bookQuery::class, //---------< here
        ],
    ],
]
```

وفي ال postman تكون بالشكل ده 

```js
query Aaa {
    aaa(id: "1") {
        id
        title
        year
        number_of_page
    }
}
```





### fetch all books


```php
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
        return Type::listOf(GraphQL::type('MyBookType')) ;   // ---------< here
    }


    public function resolve()
    {
        return Book::all(); // ---------< here
    }
}
```


```php
// config/graphql.php
'schemas' => [
    'default' => [
        'query' => [
            booksQuery::class, //---------< here
        ],
    ],
],
```



```url
http://laravel_graphql.test/graphql
```

```js
query MyBooksQuary {
    myBooksQuary {
        id
        title
        year
        number_of_page
    }
}
```




### الطريقة الافضل من الاتنين

اول حاجه هعمل ال type عادي

```php
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
        'name' => 'myBooksQuery',
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
        // return Book::all(); // ---------< here
        if (isset($args['id'])) {
            return Book::where('id',$args['id'])->get();
        }
        return Book::all();
        // ----------------------------
    }
}
```

بعد كده هربطه في ال graphQl


لاحظ ان في الطريقة الافضل بنستخدم الجمع مش المفرد





## عمل نفس الشيئ لل author بس علي نضافة 


```php
<?php
namespace App\GraphQL\Types;
use App\Author;
use GraphQL\Type\Definition\Type;
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
                    return strtolower($root->title);
                }
            ],

            'age' => [
                'type' => Type::int(),
                'description' => 'Age',
            ],
        ];
    }

}
```


```php
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
```

لاحظ ان هنا مينفعش استخدم ال find بدل من ال where لان لازم يرجع array





```php
// config/graphql.php
'schemas' => [
    'default' => [
        'query' => [
            AuthorsQuery::class, //---------< here
        ],
    ],
],

'types' => [
        AuthorType::class, //---------< here
],
```



```url
http://laravel_graphql.test/graphql
```

```js
query MyAuthorsQuary {
    myAuthorsQuary {
        id
        name
        age
    }
}
```

```js
query MyAuthorsQuery {
    myAuthorsQuery(id: "1") {
        id
        name
        age
    }
}
```




لاحظ ان ال type بيكون مفرد وال query بيكون جمع


# العلاقات في ال graphQL
سوف نقوم بجلب البيانات مع العلاقات



```php
namespace App\GraphQL\Types;
class BookType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'MyBookType',
    ];
    public function fields(): array
    {
        return [
            'author' => [
                'type' => GraphQL::type('MyAuthorType'), // ----------< here
            ],

        ];
    }
}
```


```php
namespace App\GraphQL\Types;
class AuthorType extends GraphQLType
{
    protected $attributes = [
        'name'          => 'MyAuthorType', // --------< here for 
    ];

    public function fields(): array
    {
        return [
            'books' => [
                'type' => Type::listOf(GraphQL::type('MyBookType')),
            ],
        ];
    }

}
```

## Mutators


### create

```php
<?php


namespace App\GraphQL\Mutators;

use Closure;

use App\Models\Book;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;

class CreateBookMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'CreateMyBook'
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type('MyBookType'));
    }

    public function args(): array
    {
        return [
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
        $book = new Book();
        $book->fill($args);
        $book->save();
        return $book ;
    }
}
```


```php
// config/graphql.php


return [
    'schemas' => [
        'default' => [
            'mutation' => [
                // ExampleMutation::class,
                CreateBookMutation::class, // ---------< here
            ],
        ],
    ],
];

```




```url
http://laravel_graphql.test/graphql
```

```js
mutation CreateMyBook {
    CreateMyBook(title: "bbb", year: 50, number_of_page: 50, author_id: 1) {
        id
        title
        year
        number_of_page
    }
}
```


### update


```php
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

```






```php
// config/graphql.php


return [
    'schemas' => [
        'default' => [
            'mutation' => [
                // ExampleMutation::class,
                UpdateBookMutation::class, // ---------< here
            ],
        ],
    ],
];

```




```url
http://laravel_graphql.test/graphql
```

```js
mutation UpdateMyBook {
    UpdateMyBook(id: "1", title: "www") {
        id
        title
        year
        number_of_page
    }
}
```

### delete

```php
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
```






```php
// config/graphql.php


return [
    'schemas' => [
        'default' => [
            'mutation' => [
                // ExampleMutation::class,
                DeleteBookMutation::class, // ---------< here
            ],
        ],
    ],
];

```




```url
http://laravel_graphql.test/graphql
```

```js
mutation DeleteMyBook {
    DeleteMyBook(id: "5") {
        id
        title
        year
        number_of_page
    }
}
```
