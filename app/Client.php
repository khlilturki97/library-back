<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class)
            ->using(BookClient::class)
            ->as('borrowing')
            ->withPivot('borrowed_at', 'returned_at');
    }
}
