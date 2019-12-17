<?php

namespace App\Http\Controllers;

use App\Repositories\BookRepository;
use Illuminate\Http\Request;

class BookController extends CrudController
{

    /**
     * BookController constructor.
     * @param BookRepository $bookRepository
     */

    public function __construct(BookRepository $bookRepository)
    {
        $relations = ['clients'];
        parent::__construct($bookRepository, $relations);
    }

    public function notBorrowed()
    {
        $this->relations=[];
        $this->nullConditions = ['returned_at' => true];
        return $this->repository->notBorrowed();
    }
}
