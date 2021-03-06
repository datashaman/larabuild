<?php

namespace Tests\Feature\Mutations;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Support\Str;
use Tests\TokenTestCase;

class ProjectMutationsTest extends TokenTestCase
{
    /**
     * @var string
     */
    protected $privateKey;

    public function setUp(): void
    {
        parent::setUp();
        $this->privateKey = trim(file_get_contents(__DIR__ . '/../../fixtures/private-key'));
    }

    public function postCreateProject(array $project)
    {
        return $this
            ->withBearer()
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

        $name = $this->faker->words(3, true);

        $attrs = [
            'teamId' => $team->id,
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $this
            ->postCreateProject($attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access createProject',
                ]
            );

        $this->assertDatabaseMissing(
            'projects',
            [
                'team_id' => $attrs['teamId'],
                'name' => $attrs['name'],
                'repository' => $attrs['repository'],
                'private_key' => $attrs['privateKey'],
            ]
        );
    }

    public function testCreateProjectAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $team = factory(Team::class)->create();

        $name = $this->faker->words(3, true);

        $attrs = [
            'teamId' => $team->id,
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $fragment = collect($attrs)
            ->forget('privateKey')
            ->forget('teamId')
            ->put(
                'team',
                [
                    'id' => $team->id,
                    'name' => $team->name,
                ]
            )
            ->all();

        $this
            ->postCreateProject($attrs)
            ->assertOk();

        $project = Project::query()
            ->where(
                [
                    'team_id' => $attrs['teamId'],
                    'name' => $attrs['name'],
                    'repository' => $attrs['repository'],
                ]
            )
            ->firstOrFail();

        $this->assertEquals($attrs['privateKey'], $project->private_key);
    }

    public function testCreateProjectAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->addTeam($team);
        $this->user->addRole('TEAM_ADMIN', $team);

        $name = $this->faker->words(3, true);

        $attrs = [
            'teamId' => $team->id,
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $fragment = collect($attrs)
            ->forget('privateKey')
            ->forget('teamId')
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
            ->postCreateProject($attrs)
            ->assertOk();

        $project = Project::query()
            ->where(
                [
                    'team_id' => $attrs['teamId'],
                    'name' => $attrs['name'],
                    'repository' => $attrs['repository'],
                ]
            )
            ->firstOrFail();

        $this->assertEquals($attrs['privateKey'], $project->private_key);
    }

    public function testCreateProjectAsTeamAdminNotInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->addRole('TEAM_ADMIN');

        $name = $this->faker->words(3, true);

        $attrs = [
            'teamId' => $team->id,
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $this
            ->postCreateProject($attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access createProject',
                ]
            );

        $this->assertDatabaseMissing(
            'projects',
            [
                'team_id' => $attrs['teamId'],
                'name' => $attrs['name'],
                'repository' => $attrs['repository'],
                'private_key' => $attrs['privateKey'],
            ]
        );
    }

    public function postUpdateProject(string $id, array $project)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation updateProject(\$id: ID!, \$project: UpdateProjectInput!) {
                            updateProject(id: \$id, project: \$project) {
                                id
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

        $name = $this->faker->words(3, true);

        $attrs = [
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateProject',
                ]
            );

        $this->assertDatabaseMissing(
            'projects',
            [
                'id' => $attrs['id'],
                'name' => $attrs['name'],
                'repository' => $attrs['repository'],
                'private_key' => $attrs['privateKey'],
            ]
        );
    }

    public function testUpdateProjectAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $project = factory(Project::class)->create();

        $name = $this->faker->words(3, true);

        $attrs = [
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $fragment = collect($attrs)
            ->forget('privateKey')
            ->all();

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment($fragment);

        $project = Project::query()
            ->where(
                [
                    'id' => $attrs['id'],
                    'name' => $attrs['name'],
                    'repository' => $attrs['repository'],
                ]
            )
            ->firstOrFail();

        $this->assertEquals($attrs['privateKey'], $project->private_key);
    }

    public function testUpdateProjectAsTeamAdminInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->addTeam($team);
        $this->user->addRole('TEAM_ADMIN', $team);

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $name = $this->faker->words(3, true);

        $attrs = [
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $fragment = collect($attrs)
            ->forget('privateKey')
            ->all();

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment($fragment);

        $this->assertDatabaseHas('projects', $fragment);
    }

    public function testUpdateProjectAsTeamAdminNotInTeam()
    {
        $team = factory(Team::class)->create();
        $this->user->addRole('TEAM_ADMIN');

        $project = factory(Project::class)->create(['team_id' => $team->id]);

        $name = $this->faker->words(3, true);

        $attrs = [
            'id' => Str::slug($name),
            'name' => $name,
            'repository' => 'https://github.com/datashaman/larabuild-example.git',
            'privateKey' => $this->privateKey,
        ];

        $this
            ->postUpdateProject($project->id, $attrs)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access updateProject',
                ]
            );

        $this->assertDatabaseMissing(
            'projects',
            [
                'id' => $attrs['id'],
                'name' => $attrs['name'],
                'repository' => $attrs['repository'],
                'private_key' => $attrs['privateKey'],
            ]
        );
    }

    public function postBuildProject(string $id, string $commit)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation buildProject(\$id: ID!, \$commit: String!) {
                            buildProject(id: \$id, commit: \$commit)
                        }
                    ",
                    'variables' => compact('id', 'commit'),
                ]
            );
    }

    public function testBuildProject()
    {
        $project = factory(Project::class)->create(
            [
                'repository' => 'https://github.com/datashaman/larabuild-example.git',
                'private_key' => $this->privateKey,
            ]
        );

        $this
            ->postBuildProject($project->id, 'master')
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access buildProject',
                ]
            );

        $this->assertDatabaseMissing(
            'builds',
            [
                'project_id' => $project->id,
                'commit' => 'master',
            ]
        );
    }

    public function testBuildProjectInTeam()
    {
        $project = factory(Project::class)->create(
            [
                'repository' => 'https://github.com/datashaman/larabuild-example.git',
                'private_key' => encrypt($this->privateKey),
            ]
        );

        $this->user->addTeam($project->team);

        $this
            ->postBuildProject($project->id, 'master')
            ->assertOk();

        $this->assertDatabaseHas(
            'builds',
            [
                'project_id' => $project->id,
                'commit' => 'master',
            ]
        );
    }

    public function testBuildProjectAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $project = factory(Project::class)->create(
            [
                'repository' => 'https://github.com/datashaman/larabuild-example.git',
                'private_key' => encrypt($this->privateKey),
            ]
        );

        $this->user->addTeam($project->team);

        $this
            ->postBuildProject($project->id, 'master')
            ->assertOk();

        $this->assertDatabaseHas(
            'builds',
            [
                'project_id' => $project->id,
                'commit' => 'master',
            ]
        );
    }
}
