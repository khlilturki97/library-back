<?php

namespace App\Http\Controllers;

use App\Repositories\BookClientRepository;

class BookClientController extends CrudController
{
    public function __construct(BookClientRepository $repository)
    {
        parent::__construct($repository);
    }
}
