<?php

use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients=factory(\App\Client::class,5)->create();
        foreach ($clients as $client)
        {
            $client->books()->sync(\App\Book::inRandomOrder()->first()->id);
        }
    }
}
