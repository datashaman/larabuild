<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Build::class, function (Faker $faker) {
    return [
        'hash' => $faker->md5,
        'project_id' => factory(App\Models\Project::class)->create()->id,
        'status' => $faker->randomElement(config('larabuild.statuses')),
        'commit' => $faker->sha1,
    ];
});
