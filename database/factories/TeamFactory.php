<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Team::class, function (Faker $faker) {
    return [
        'name' => $faker->domainWord,
    ];
});
