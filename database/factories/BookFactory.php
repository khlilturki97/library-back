<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Book;
use Faker\Generator as Faker;

$factory->define(Book::class, function (Faker $faker) {
    if (!is_dir('public/uploads/' . 'books')) {
        mkdir('public/uploads/' . 'books');
    }
    return [
        'title' => $faker->paragraph(1),
        'isban' => $faker->numerify('######'),
        'author' => $faker->name,
        'image' => function () use ($faker) {
            $image = $faker->image('public/uploads/' . 'books', 400, 400, null);
            print_r($image . "\n");
            print_r(explode('public/', $image)[1] . "\n");
            return explode('public/', $image)[1];
        },
    ];
});
