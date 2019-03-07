<?php

namespace Tests\Feature\Queries;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TokenTestCase;

class TeamQueriesTest extends TokenTestCase
{
    /**
     * @return TestResponse
     */
    protected function postTeamsQuery()
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => '{
                        teams(count: 10) {
                            paginatorInfo {
                                count
                                currentPage
                                lastPage
                                total
                            }
                            data {
                                id
                                name
                            }
                        }
                    }',
                ]
            );
    }

    public function testTeamsQuery()
    {
        $this
            ->postTeamsQuery()
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access teams',
                ]
            );
    }

    public function testTeamsQueryAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $teams = factory(Team::class, 12)
            ->create()
            ->sortBy(function ($team) {
                return $team->name;
            })
            ->take(10)
            ->map(
                function ($team) {
                    return [
                        'id' => $team->id,
                        'name' => $team->name,
                    ];
                }
            )
            ->values()
            ->all();

        $expected = [
            'data' => [
                'teams' => [
                    'data' => $teams,
                    'paginatorInfo' => [
                        'count' => 10,
                        'currentPage' => 1,
                        'lastPage' => 2,
                        'total' => 12,
                    ],
                ],
            ],
        ];

        $this
            ->postTeamsQuery()
            ->assertOk()
            ->assertExactJson($expected);
    }

    protected function postTeamQuery($id)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        team(id: \"{$id}\") {
                            id
                            name
                        }
                    }",
                ]
            );
    }

    public function testTeamQueryAsOther()
    {
        $team = factory(Team::class)->create();

        $expected = [
            'data' => [
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                ],
            ],
        ];

        $this
            ->postTeamQuery($team->id)
            ->assertStatus(200)
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access team',
                ]
            );
    }

    public function testTeamQuery()
    {
        $team = factory(Team::class)->create();
        $this->user->addTeam($team);

        $expected = [
            'data' => [
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                ],
            ],
        ];

        $this
            ->postTeamQuery($team->id)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
