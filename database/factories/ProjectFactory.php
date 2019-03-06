<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Project::class, function (Faker $faker) {
    return [
        'team_id' => factory(App\Models\Team::class)->create()->id,
        'name' => $faker->unique()->words(2, true),
        'repository' => 'https://github.com/' . $faker->username . '/' . $faker->domainWord,
        'private_key' => encrypt('private key'),
    ];
});
