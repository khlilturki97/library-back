<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Log;

class BookClient extends Pivot
{
    public $timestamps = false;

    protected $fillable = [
        'borrowed_at',
        'client_id',
        'book_id',
        'returned_at'
    ];

    protected $foreignKey = 'book_id';
    protected $relatedKey = 'client_id';
}
