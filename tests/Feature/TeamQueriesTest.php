<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Tests\PassportTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamQueriesTest extends PassportTestCase
{
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
                        'id' => (string) $user->id,
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
