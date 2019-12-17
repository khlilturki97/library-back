<?php

namespace App\Repositories;

use App\Book;

/**
 * Class BookRepository
 * @package App\Repositories
 */
class BookRepository extends CrudRepository
{
    /**
     * BookRepository constructor.
     * @param Book $book
     */
    public function __construct(Book $book)
    {
        parent::__construct($book);
    }

    public function notBorrowed()
    {
        return $this->model->whereDoesntHave('clients',function ($q){
            $q->whereNull('returned_at');
        })->get();
    }
}
