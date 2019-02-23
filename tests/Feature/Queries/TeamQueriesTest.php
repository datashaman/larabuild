<?php

namespace Tests\Feature\Queries;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\TestResponse;
use Nuwave\Lighthouse\Execution\Utils\GlobalId;
use Tests\PassportTestCase;

class TeamQueriesTest extends PassportTestCase
{
    /**
     * @return TestResponse
     */
    protected function postTeamsQuery()
    {
        return $this
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
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }

    public function testTeamsQueryAsAdmin()
    {
        $this->user->addRole('admin');

        $teams = factory(Team::class, 12)
            ->create()
            ->take(10)
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
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        team(id: {$id}) {
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
                    'id' => (string) $team->id,
                    'name' => $team->name,
                ],
            ],
        ];

        $this
            ->postTeamQuery($team->id)
            ->assertStatus(200)
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }

    public function testTeamQuery()
    {
        $team = factory(Team::class)->create();
        $this->user->teams()->attach($team);

        $expected = [
            'data' => [
                'team' => [
                    'id' => (string) $team->id,
                    'name' => $team->name,
                ],
            ],
        ];

        $this
            ->postTeamQuery($team->id)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testTeamProjectsQuery()
    {
        $otherProject = factory(Project::class);
        $team = factory(Team::class)->create();

        $teamProjects = factory(Project::class, 3)
            ->create(
                [
                    'team_id' => $team->id,
                ]
            )
            ->map(
                function ($project) {
                    return [
                        'id' => (string) $project->id,
                        'name' => $project->name,
                        'repository' => $project->repository,
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'teamProjects' => $teamProjects,
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        teamProjects(team_id: \"{$team->id}\") {
                            id
                            name
                            repository
                        }
                    }",
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testTeamUsersQuery()
    {
        $team = factory(Team::class)->create();

        $otherUser = factory(User::class);

        $teamUsers = factory(User::class, 3)
            ->create()
            ->each(
                function ($user) use ($team) {
                    $user->teams()->attach($team);
                }
            )
            ->map(
                function ($user) {
                    return [
                        'id' => GlobalId::encode('User', $user->id),
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'teamUsers' => $teamUsers,
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        teamUsers(team_id: \"{$team->id}\") {
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
}
