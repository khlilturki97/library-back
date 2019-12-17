<?php

namespace App\Http\Controllers;

use App\Repositories\ClientRepository;

class ClientController extends CrudController
{

    /**
     * ClientController constructor.
     * @param ClientRepository $clientRepository
     */

    public function __construct(ClientRepository $clientRepository)
    {
        $relations = ['books'];
        parent::__construct($clientRepository, $relations);
    }
}
