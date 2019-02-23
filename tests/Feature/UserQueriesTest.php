<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Tests\PassportTestCase;

class UserQueriesTest extends PassportTestCase
{
    public function testUserTeamsQuery()
    {
        $otherTeam = factory(Team::class)->create();
        $user = factory(User::class)->create();

        $userTeams = factory(Team::class, 3)
            ->create()
            ->each(
                function ($team) use ($user) {
                    $team->users()->attach($user);
                }
            )
            ->map(
                function ($team) {
                    return [
                        'id' => (string) $team->id,
                        'name' => $team->name,
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'userTeams' => $userTeams,
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        userTeams(user_id: \"{$user->id}\") {
                            id
                            name
                        }
                    }",
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
