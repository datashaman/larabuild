<?php

namespace Tests\Feature\Mutations;

use App\Models\Project;
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
                        mutation createProject(\$project: CreateProjectInput!) {
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

    public function testCreateProjectAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->teams()->attach($team);
        $this->user->addRole('team-admin', $team);

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

    public function testCreateProjectAsTeamAdminNotInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->addRole('team-admin');

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

    public function postUpdateProject(int $id, array $project)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation updateProject(\$id: ID!, \$project: UpdateProjectInput!) {
                            updateProject(id: \$id, project: \$project) {
                                name
                                repository
                            }
                        }
                    ",
                    'variables' => compact('id', 'project'),
                ]
            );
    }

    public function testUpdateProject()
    {
        $project = factory(Project::class)->create();

        $attrs = [
            'name' => $this->faker->words(3, true),
            'repository' => $this->faker->url,
        ];

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateProject',
                ]
            );

        $this->assertDatabaseMissing('projects', $attrs);
    }

    public function testUpdateProjectAsAdmin()
    {
        $this->user->addRole('admin');

        $project = factory(Project::class)->create();

        $attrs = [
            'name' => $this->faker->words(3, true),
            'repository' => $this->faker->url,
        ];

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertDatabaseHas('projects', $attrs);
    }

    public function testUpdateProjectAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->teams()->attach($team);
        $this->user->addRole('team-admin', $team);

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $attrs = [
            'name' => $this->faker->words(3, true),
            'repository' => $this->faker->url,
        ];

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment($attrs);

        $this->assertDatabaseHas('projects', $attrs);
    }

    public function testUpdateProjectAsTeamAdminNotInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->addRole('team-admin');

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $attrs = [
            'name' => $this->faker->words(3, true),
            'repository' => $this->faker->url,
        ];

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateProject',
                ]
            );

        $this->assertDatabaseMissing('projects', $attrs);
    }
}
