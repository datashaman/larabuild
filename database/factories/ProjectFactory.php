<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\Project::class, function (Faker $faker, array $overrides = []) {
    $name = $faker->unique()->words(2, true);

    return [
        'id' => Str::slug($name),
        'team_id' => factory(App\Models\Team::class)->create()->id,
        'name' => $name,
        'repository' => 'https://github.com/' . $faker->username . '/' . $faker->domainWord,
        'private_key' => encrypt('private key'),
        'timeout' => 3600,
    ];
});
