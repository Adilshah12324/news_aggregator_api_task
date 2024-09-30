<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Relationships\BelongsToManyUsers;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsSource extends Model
{
    use HasFactory, BelongsToManyUsers;

    protected $fillable = [
    'source',
    'category',
    'author',
    'keyword',
    'article',
    ];

    protected $casts = [
        'article' => 'array',
    ];
}
