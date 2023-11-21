<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $guarded  = [];
    // protected $fillable = ['id','title','year','number_of_page']; // black list


    public function author()
    {
        return $this->belongsTo(Author::class);
    }

}
