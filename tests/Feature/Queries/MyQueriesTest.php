<?php

namespace Tests\Feature\Queries;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use Nuwave\Lighthouse\Execution\Utils\GlobalId;
use Tests\TokenTestCase;

class MyQueriesTest extends TokenTestCase
{
    public function testMeQuery()
    {
        $expected = [
            'data' => [
                'me' => [
                    'id' => GlobalId::encode('User', $this->user->id),
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ],
        ];

        $this
            ->withBearer()
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
}
