<?php

namespace App\Repositories;

use App\Client;

/**
 * Class ClientRepository
 * @package App\Repositories
 */
class ClientRepository extends CrudRepository
{
    /**
     * ClientRepository constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }
}
