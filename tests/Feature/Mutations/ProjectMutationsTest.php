<?php

namespace Tests\Feature\Mutations;

use App\Models\Team;
use Tests\PassportTestCase;

class ProjectMutationsTest extends PassportTestCase
{
    public function postCreateProject(array $project)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation createProject(\$project: ProjectInput!) {
                            createProject(project: \$project) {
                                id
                                team {
                                    id
                                    name
                                }
                                name
                                repository
                            }
                        }
                    ",
                    'variables' => compact('project'),
                ]
            );
    }

    public function testCreateProject()
    {
        $team = factory(Team::class)->create();

        $project = [
            'team_id' => $team->id,
            'name' => $this->faker->words(3, true),
            'repository' => $this->faker->url,
        ];

        $this
            ->postCreateProject($project)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access createProject',
                ]
            );

        $this->assertDatabaseMissing('projects', $project);
    }

    public function testCreateProjectAsAdmin()
    {
        $this->user->addRole('admin');

        $team = factory(Team::class)->create();

        $project = [
            'team_id' => $team->id,
            'name' => $this->faker->words(3, true),
            'repository' => $this->faker->url,
        ];

        $fragment = collect($project)
            ->forget('team_id')
            ->put(
                'team',
                [
                    'id' => (string) $team->id,
                    'name' => $team->name,
                ]
            )
            ->map(
                function ($value, $key) {
                    if ($key === 'id') {
                        return (string) $value;
                    }

                    return $value;
                }
            )
            ->all();

        $this
            ->postCreateProject($project)
            ->assertOk()
            ->assertJsonFragment($fragment);

        $this->assertDatabaseHas('projects', $project);
    }
}
