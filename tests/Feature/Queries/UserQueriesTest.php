<?php

namespace Tests\Feature\Queries;

use App\Models\Team;
use App\Models\User;
use Nuwave\Lighthouse\Execution\Utils\GlobalId;
use Tests\TokenTestCase;

class UserQueriesTest extends TokenTestCase
{
    protected function postUsersQuery()
    {
        return $this
            ->withBearer()
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
            );
    }

    public function testUsersQuery()
    {
        $this
            ->postUsersQuery()
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access users',
                ]
            );
    }

    public function testUsersQueryAsAdmin()
    {
        $this->user->addRole('ADMIN');

        factory(User::class, 11)->create();

        $users = User::query()
            ->take(10)
            ->get()
            ->map(
                function ($user) {
                    return [
                        'id' => GlobalId::encode('User', $user->id),
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
        $query = "{
            user(id: \"{$id}\") {
                id
                name
                email
            }
        }";

        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        user(id: \"{$id}\") {
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
        $globalId = GlobalId::encode('User', $user->id);

        $this
            ->postUserQuery($globalId)
            ->assertStatus(200)
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }

    public function testUserQuery()
    {
        $globalId = GlobalId::encode('User', $this->user->id);

        $expected = [
            'data' => [
                'user' => [
                    'id' => $globalId,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ],
        ];

        $this
            ->postUserQuery($globalId)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
