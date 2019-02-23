<?php

namespace Tests\Feature\Queries;

use App\Models\Team;
use App\Models\User;
use Tests\PassportTestCase;

class UserQueriesTest extends PassportTestCase
{
    protected function postUsersQuery()
    {
        return $this->postJson(
            '/graphql',
            [
                'query' => '{
                    users(count: 10) {
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

    public function testUsersQuery()
    {
        $this
            ->postUsersQuery()
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }

    public function testUsersQueryAsAdmin()
    {
        $this->user->addRole('admin');

        factory(User::class, 11)->create();

        $users = User::query()
            ->take(10)
            ->get()
            ->map(
                function ($user) {
                    return [
                        'id' => (string) $user->id,
                        'name' => $user->name,
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'users' => [
                    'data' => $users,
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
            ->postUsersQuery()
            ->assertOk()
            ->assertExactJson($expected);
    }

    protected function postUserQuery($id)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        user(id: {$id}) {
                            id
                            name
                            email
                        }
                    }",
                ]
            );
    }

    public function testUserQueryAsOther()
    {
        $user = factory(User::class)->create();

        $this
            ->postUserQuery($user->id)
            ->assertStatus(200)
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }

    public function testUserQuery()
    {
        $expected = [
            'data' => [
                'user' => [
                    'id' => (string) $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ],
        ];

        $this
            ->postUserQuery($this->user->id)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

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
