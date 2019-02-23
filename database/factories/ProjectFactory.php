<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Project::class, function (Faker $faker) {
    return [
        'team_id' => factory(App\Models\Team::class)->create()->id,
        'name' => $faker->words(2, true),
    ];
});
