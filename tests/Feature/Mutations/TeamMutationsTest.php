<?php

namespace Tests\Feature\Mutations;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TokenTestCase;

class TeamMutationsTest extends TokenTestCase
{
    public function postCreateTeam(array $team)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation createTeam(\$team: TeamInput!) {
                            createTeam(team: \$team) {
                                id
                                name
                            }
                        }
                    ",
                    'variables' => compact('team'),
                ]
            );
    }

    public function testCreateTeam()
    {
        $name = $this->faker->words(3, true);

        $team = [
            'id' => Str::slug($name),
            'name' => $name,
        ];

        $this
            ->postCreateTeam($team)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access createTeam',
                ]
            );

        $this->assertDatabaseMissing('teams', $team);
    }

    public function testCreateTeamAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $name = $this->faker->words(3, true);

        $team = [
            'name' => $name,
            'id' => Str::slug($name),
        ];

        $this
            ->postCreateTeam($team)
            ->assertOk()
            ->assertJsonFragment($team);

        $this->assertDatabaseHas('teams', $team);
    }

    public function postUpdateTeam(string $id, array $team)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation updateTeam(\$id: ID!, \$team: TeamInput!) {
                            updateTeam(id: \$id, team: \$team) {
                                id
                                name
                            }
                        }
                    ",
                    'variables' => compact('id', 'team'),
                ]
            );
    }

    public function testUpdateTeam()
    {
        $existingTeam = factory(Team::class)->create();

        $name = $this->faker->words(3, true);

        $team = [
            'name' => $name,
            'id' => Str::slug($name),
        ];

        $this
            ->postUpdateTeam($existingTeam->id, $team)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateTeam',
                ]
            );

        $this->assertDatabaseMissing('teams', $team);
    }

    public function testUpdateTeamAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $existingTeam = factory(Team::class)->create();

        $name = $this->faker->words(3, true);

        $team = [
            'id' => Str::slug($name),
            'name' => $name,
        ];

        $this
            ->postUpdateTeam($existingTeam->id, $team)
            ->assertOk()
            ->assertJsonFragment($team);

        $this->assertDatabaseHas('teams', $team);
    }

    public function testUpdateTeamAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();
        $team->addUser($this->user);
        $this->user->addRole('TEAM_ADMIN', $team);

        $name = $this->faker->words(3, true);

        $attrs = [
            'id' => Str::slug($name),
            'name' => $name,
        ];

        $this
            ->postUpdateTeam($team->id, $attrs)
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertDatabaseHas('teams', $attrs);
    }

    public function testUpdateTeamAsTeamAdminNotInTeam()
    {
        $this->user->addRole('TEAM_ADMIN');

        $existingTeam = factory(Team::class)->create();

        $name = $this->faker->words(3, true);

        $team = [
            'id' => Str::slug($name),
            'name' => $name,
        ];

        $this
            ->postUpdateTeam($existingTeam->id, $team)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateTeam',
                ]
            );

        $this->assertDatabaseMissing('teams', $team);
    }

    public function postDeleteTeam(string $id)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation deleteTeam(\$id: ID!) {
                            deleteTeam(id: \$id) {
                                id
                                name
                            }
                        }
                    ",
                    'variables' => compact('id'),
                ]
            );
    }

    public function testDeleteTeam()
    {
        $existingTeam = factory(Team::class)->create();

        $this
            ->postDeleteTeam($existingTeam->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access deleteTeam',
                ]
            );

        $this->assertDatabaseHas('teams', $existingTeam->only(['id', 'name']));
    }

    public function testDeleteTeamAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $existingTeam = factory(Team::class)->create();

        $name = $this->faker->words(3, true);

        $team = [
            'id' => $existingTeam->id,
            'name' => $existingTeam->name,
        ];

        $this
            ->postDeleteTeam($existingTeam->id)
            ->assertOk()
            ->assertJsonFragment($team);

        $this->assertDatabaseMissing('teams', $team);
    }

    public function postAddTeamUser(string $teamId, int $userId)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation addTeamUser(\$teamId: ID!, \$userId: ID!) {
                            addTeamUser(teamId: \$teamId, userId: \$userId) {
                                teams {
                                    id
                                }
                            }
                        }
                    ",
                    'variables' => compact('teamId', 'userId'),
                ]
            );
    }

    public function testAddTeamUser()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();

        $this
            ->postAddTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access addTeamUser',
                ]
            );

        $this->assertDatabaseMissing(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function testAddTeamUserAsTeamAdmin()
    {
        $team = factory(Team::class)->create();
        $team->addUser($this->user);
        $this->user->addRole('TEAM_ADMIN', $team);

        $user = factory(User::class)->create();

        $this
            ->postAddTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'teams' => [
                        [
                            'id' => (string) $team->id,
                        ],
                    ],
                ]
            );

        $this->assertDatabaseHas(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function testAddTeamUserAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();

        $this
            ->postAddTeamUser($team->id, $user->id);

        $this
            ->postAddTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'teams' => [
                        [
                            'id' => (string) $team->id,
                        ],
                    ],
                ]
            );

        $this->assertDatabaseHas(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function testAddTeamUserExisting()
    {
        $this->user->addRole('ADMIN');

        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();
        $team->addUser($user);

        $this
            ->postAddTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'teams' => [
                        [
                            'id' => (string) $team->id,
                        ],
                    ],
                ]
            );

        $this->assertDatabaseHas(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function postRemoveTeamUser(string $teamId, int $userId)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation removeTeamUser(\$teamId: ID!, \$userId: ID!) {
                            removeTeamUser(teamId: \$teamId, userId: \$userId) {
                                teams {
                                    id
                                }
                            }
                        }
                    ",
                    'variables' => compact('teamId', 'userId'),
                ]
            );
    }

    public function testRemoveTeamUser()
    {
        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();
        $team->addUser($user);

        $this
            ->postRemoveTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access removeTeamUser',
                ]
            );

        $this->assertDatabaseHas(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function testRemoveTeamUserAsTeamAdmin()
    {
        $team = factory(Team::class)->create();
        $team->addUser($this->user);
        $this->user->addRole('TEAM_ADMIN', $team);

        $user = factory(User::class)->create();

        $this
            ->postRemoveTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'teams' => [],
                ]
            );

        $this->assertDatabaseMissing(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function testRemoveTeamUserAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();

        $this
            ->postRemoveTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'teams' => [],
                ]
            );

        $this->assertDatabaseMissing(
            'team_user',
            [
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]
        );
    }

    public function testRemoveTeamUserMissing()
    {
        $this->user->addRole('ADMIN');

        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();

        $this
            ->postRemoveTeamUser($team->id, $user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'teams' => [],
                ]
            );
    }
}
