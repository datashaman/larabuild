<?php

namespace Tests\Feature\Mutations;

use Tests\PassportTestCase;

class TeamMutationsTest extends PassportTestCase
{
    public function postCreateTeam(string $name)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation createTeam(\$input: CreateTeamInput!) {
                            createTeam(input: \$input) {
                                id
                                name
                            }
                        }
                    ",
                    'variables' => [
                        'input' => [
                            'name' => $name,
                        ],
                    ],
                ]
            );
    }

    public function testCreateTeam()
    {
        $name = $this->faker->words(3, true);

        $this
            ->postCreateTeam($name)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );

        $this
            ->assertDatabaseMissing(
                'teams',
                [
                    'name' => $name,
                ]
            );
    }

    public function testCreateTeamAsAdmin()
    {
        $this->user->addRole('admin');

        $name = $this->faker->words(3, true);

        $this
            ->postCreateTeam($name)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'name' => $name,
                ]
            );

        $this
            ->assertDatabaseHas(
                'teams',
                [
                    'name' => $name,
                ]
            );
    }
}
