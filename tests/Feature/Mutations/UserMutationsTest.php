<?php

namespace Tests\Feature\Mutations;

use App\Models\Team;
use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Tests\PassportTestCase;

class UserMutationsTest extends PassportTestCase
{
    public function postCreateUser(array $user)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation createUser(\$user: UserInput!) {
                            createUser(user: \$user) {
                                id
                                name
                                email
                            }
                        }
                    ",
                    'variables' => [
                        'user' => $user,
                    ],
                ]
            );
    }

    public function testCreateUser()
    {
        $attrs = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'secret',
        ];

        $this
            ->postCreateUser($attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access createUser',
                ]
            );

        unset($attrs['password']);

        $this->assertDatabaseMissing('users', $attrs);
    }

    public function testCreateUserAsAdmin()
    {
        $this->user->addRole('admin');

        $attrs = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $password = 'secret';

        $this
            ->postCreateUser($attrs + compact('password'))
            ->assertOk()
            ->assertJsonFragment($attrs);

        $user = User::where('email', $attrs['email'])->firstOrFail();

        $this->assertTrue(app(BcryptHasher::class)->check('secret', $user->password));
    }

    public function postUpdateUser(int $id, array $user)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation updateUser(\$id: ID!, \$user: UserInput!) {
                            updateUser(id: \$id, user: \$user) {
                                id
                                name
                                email
                            }
                        }
                    ",
                    'variables' => compact('id', 'user'),
                ]
            );
    }

    public function testUpdateUser()
    {
        $user = factory(User::class)->create();

        $attrs = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $password = 'secret';

        $this
            ->postUpdateUser($user->id, $attrs + compact('password'))
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateUser',
                ]
            );

        $this->assertDatabaseMissing('users', $attrs);
    }

    public function testUpdateUserAsAdmin()
    {
        $this->user->addRole('admin');

        $user = factory(User::class)->create();

        $attrs = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $password = 'secret';

        $this
            ->postUpdateUser($user->id, $attrs + compact('password'))
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertTrue(app(BcryptHasher::class)->check('secret', $user->password));
    }

    public function testUpdateUserAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();

        $this->user->addTeam($team);
        $this->user->addRole('team-admin', $team);

        $user = factory(User::class)->create();
        $user->addTeam($team);

        $attrs = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $password = 'secret';

        $this
            ->postUpdateUser($user->id, $attrs + compact('password'))
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertTrue(app(BcryptHasher::class)->check('secret', $user->password));
    }

    public function testUpdateUserAsTeamAdminNotInTeam()
    {
        $this->user->addRole('team-admin');

        $team = factory(Team::class)->create();

        $user = factory(User::class)->create();
        $user->addTeam($team);

        $attrs = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $password = 'secret';

        $this
            ->postUpdateUser($user->id, $attrs + compact('password'))
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateUser',
                ]
            );

        $this->assertDatabaseMissing('users', $attrs);
    }

    public function postDeleteUser(int $id)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation deleteUser(\$id: ID!) {
                            deleteUser(id: \$id) {
                                name
                                email
                            }
                        }
                    ",
                    'variables' => compact('id'),
                ]
            );
    }

    public function testDeleteUser()
    {
        $user = factory(User::class)->create();

        $this
            ->postDeleteUser($user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access deleteUser',
                ]
            );

        $this->assertDatabaseHas('users', $user->only(['id', 'name']));
    }

    public function testDeleteUserAsAdmin()
    {
        $this->user->addRole('admin');

        $user = factory(User::class)->create();

        $attrs = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $this
            ->postDeleteUser($user->id)
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertDatabaseMissing('users', $attrs);
    }

    public function testDeleteUserAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();

        $this->user->addTeam($team);
        $this->user->addRole('team-admin', $team);

        $user = factory(User::class)->create();
        $user->addTeam($team);

        $attrs = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $this
            ->postDeleteUser($user->id)
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertDatabaseMissing('users', $attrs);
    }

    public function testDeleteUserAsTeamAdminNotInTeam()
    {
        $this->user->addRole('team-admin');

        $team = factory(Team::class)->create();

        $user = factory(User::class)->create();
        $user->addTeam($team);

        $attrs = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $this
            ->postDeleteUser($user->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access deleteUser',
                ]
            );

        $this->assertDatabaseHas('users', $attrs);
    }

    public function postAddRole(int $id, string $role, int $team_id = null)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation addUserRole(\$id: ID!, \$role: String!, \$team_id: Int) {
                            addUserRole(id: \$id, role: \$role, team_id: \$team_id) {
                                userRoles {
                                    role
                                    team {
                                        id
                                    }
                                }
                            }
                        }
                    ",
                    'variables' => compact('id', 'role', 'team_id'),
                ]
            );
    }

    public function testAddRole()
    {
        $user = factory(User::class)->create();

        $this
            ->postAddRole($user->id, 'admin')
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access addUserRole',
                ]
            );

        $this->assertFalse($user->hasRole('admin'));
    }

    public function testAddRoleAsAdmin()
    {
        $this->user->addRole('admin');

        $user = factory(User::class)->create();

        $fragment = [
            'data' => [
                'addUserRole' => [
                    'userRoles' => [
                        [
                            'role' => 'admin',
                            'team' => null,
                        ],
                    ],
                ],
            ],
        ];

        $this
            ->postAddRole($user->id, 'admin')
            ->assertOk()
            ->assertJsonFragment($fragment);

        $this->assertTrue($user->hasRole('admin'));
    }

    public function testAddTeamRoleAsAdmin()
    {
        $this->user->addRole('admin');

        $team = factory(Team::class)->create();
        $user = factory(User::class)->create();

        $fragment = [
            'data' => [
                'addUserRole' => [
                    'userRoles' => [
                        [
                            'role' => 'team-admin',
                            'team' => [
                                'id' => (string) $team->id,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this
            ->postAddRole($user->id, 'team-admin', $team->id)
            ->assertOk()
            ->assertJsonFragment($fragment);

        $this->assertTrue($user->hasRole('team-admin', $team));
    }

    public function postRemoveRole(int $id, string $role, int $team_id = null)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation removeUserRole(\$id: ID!, \$role: String!, \$team_id: Int) {
                            removeUserRole(id: \$id, role: \$role, team_id: \$team_id) {
                                userRoles {
                                    role
                                    team {
                                        id
                                    }
                                }
                            }
                        }
                    ",
                    'variables' => compact('id', 'role', 'team_id'),
                ]
            );
    }

    public function testRemoveRole()
    {
        $user = factory(User::class)->create();
        $user->addRole('admin');

        $this
            ->postRemoveRole($user->id, 'admin')
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access removeUserRole',
                ]
            );

        $this->assertTrue($user->hasRole('admin'));
    }

    public function testRemoveRoleAsAdmin()
    {
        $this->user->addRole('admin');

        $user = factory(User::class)->create();
        $user->addRole('admin');

        $fragment = [
            'data' => [
                'removeUserRole' => [
                    'userRoles' => [
                    ],
                ],
            ],
        ];

        $this
            ->postRemoveRole($user->id, 'admin')
            ->assertOk()
            ->assertJsonFragment($fragment);

        $this->assertFalse($user->hasRole('admin'));
    }

    public function testRemoveTeamRoleAsAdmin()
    {
        $this->user->addRole('admin');

        $team = factory(Team::class)->create();
        $otherTeam = factory(Team::class)->create();

        $user = factory(User::class)->create();
        $user->addRole('admin', $otherTeam);
        $user->addRole('admin', $team);

        $fragment = [
            'data' => [
                'removeUserRole' => [
                    'userRoles' => [
                        [
                            'role' => 'admin',
                            'team' => [
                                'id' => (string) $otherTeam->id,
                            ],
                        ]
                    ],
                ],
            ],
        ];

        $this
            ->postRemoveRole($user->id, 'admin', $team->id)
            ->assertOk()
            ->assertJsonFragment($fragment);

        $this->assertFalse($user->hasRole('admin'));
    }
}
