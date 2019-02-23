<?php

namespace Tests\Feature;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use Tests\PassportTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends PassportTestCase
{
    public function testMeQuery()
    {
        $expected = [
            'data' => [
                'me' => [
                    'id' => (string) $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        me {
                            id
                            name
                            email
                        }
                    }",
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testMyTeamsQuery()
    {
        $otherTeam = factory(Team::class)->create();

        $myTeams = factory(Team::class, 3)
            ->create()
            ->each(
                function ($team) {
                    $team->users()->attach($this->user);
                }
            )
            ->map(
                function ($team) {
                    return [
                        'id' => (string) $team->id,
                        'name' => (string) $team->name,
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'myTeams' => $myTeams,
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        myTeams {
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
