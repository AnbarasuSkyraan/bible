<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'version_id',
        'book_num',
        'title',
        'short_title',
        'meta_keyword',
        'meta_description'
    ];
}
