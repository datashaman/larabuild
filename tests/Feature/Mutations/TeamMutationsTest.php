<?php

namespace Tests\Feature\Mutations;

use App\Models\Team;
use App\Models\User;
use Tests\PassportTestCase;

class TeamMutationsTest extends PassportTestCase
{
    public function postCreateTeam(array $team)
    {
        return $this
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
        $team = [
            'name' => $this->faker->words(3, true),
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
        $this->user->addRole('admin');

        $team = [
            'name' => $this->faker->words(3, true),
        ];


        $this
            ->postCreateTeam($team)
            ->assertOk()
            ->assertJsonFragment($team);

        $this->assertDatabaseHas('teams', $team);
    }

    public function postUpdateTeam(int $id, array $team)
    {
        return $this
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

        $team = [
            'name' => $this->faker->words(3, true),
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
        $this->user->addRole('admin');

        $existingTeam = factory(Team::class)->create();

        $team = [
            'name' => $this->faker->words(3, true),
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
        $this->user->addRole('team-admin', $team);

        $attrs = [
            'name' => $this->faker->words(3, true),
        ];

        $this
            ->postUpdateTeam($team->id, $attrs)
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertDatabaseHas('teams', $attrs);
    }

    public function testUpdateTeamAsTeamAdminNotInTeam()
    {
        $this->user->addRole('team-admin');

        $existingTeam = factory(Team::class)->create();

        $team = [
            'name' => $this->faker->words(3, true),
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

    public function postDeleteTeam(int $id)
    {
        return $this
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
        $this->user->addRole('admin');

        $existingTeam = factory(Team::class)->create();

        $team = [
            'id' => (string) $existingTeam->id,
            'name' => $existingTeam->name,
        ];

        $this
            ->postDeleteTeam($existingTeam->id)
            ->assertOk()
            ->assertJsonFragment($team);

        $this->assertDatabaseMissing('teams', $team);
    }

    public function postAddTeamUser(int $team_id, int $user_id)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation addTeamUser(\$team_id: ID!, \$user_id: ID!) {
                            addTeamUser(team_id: \$team_id, user_id: \$user_id) {
                                teams {
                                    id
                                }
                            }
                        }
                    ",
                    'variables' => compact('team_id', 'user_id'),
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
        $this->user->addRole('team-admin', $team);

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
        $this->user->addRole('admin');

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
        $this->user->addRole('admin');

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

    public function postRemoveTeamUser(int $team_id, int $user_id)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation removeTeamUser(\$team_id: ID!, \$user_id: ID!) {
                            removeTeamUser(team_id: \$team_id, user_id: \$user_id) {
                                teams {
                                    id
                                }
                            }
                        }
                    ",
                    'variables' => compact('team_id', 'user_id'),
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
        $this->user->addRole('team-admin', $team);

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
        $this->user->addRole('admin');

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
        $this->user->addRole('admin');

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
