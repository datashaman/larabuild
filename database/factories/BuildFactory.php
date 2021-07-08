<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Build::class, function (Faker $faker, array $overrides = []) {
    return [
        'project_id' => array_get($overrides, 'project_id') ?: factory(App\Models\Project::class)->create()->id,
        'number' => $faker->unique()->numberBetween(1, 10000),
        'status' => $faker->randomElement(config('larabuild.statuses')),
        'commit' => $faker->sha1,
    ];
});
