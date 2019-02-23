<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Tests\PassportTestCase;

class UserQueriesTest extends PassportTestCase
{
    public function testUsersQuery()
    {
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
            ->postJson(
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
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testUserQuery()
    {
        $user = factory(User::class)->create();

        $expected = [
            'data' => [
                'user' => [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        user(id: {$user->id}) {
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
