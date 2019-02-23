<?php

namespace Tests\Feature;

use Tests\PassportTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends PassportTestCase
{
    public function testMeQuery()
    {
        $expected = [
            'data' => [
                'me' => [
                    'id' => (string) $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ],
        ];

        $this
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
