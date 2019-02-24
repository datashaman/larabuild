<?php

namespace Tests\Feature\Mutations;

use App\Models\Team;
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
        $this->user->teams()->attach($team);
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

}
