<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isban',
        'image'
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class)
            ->using(BookClient::class)
            ->as('borrowing')
            ->withPivot('borrowed_at', 'returned_at');
    }
}
