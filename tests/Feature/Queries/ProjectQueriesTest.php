<?php

namespace Tests\Feature\Queries;

use App\Models\Build;
use App\Models\Project;
use Illuminate\Foundation\Testing\TestResponse;
use Nuwave\Lighthouse\Execution\Utils\GlobalId;
use Tests\PassportTestCase;

class ProjectQueriesTest extends PassportTestCase
{
    /**
     * @return TestResponse
     */
    protected function postProjectsQuery()
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => '{
                        projects(count: 10) {
                            paginatorInfo {
                                count
                                currentPage
                                lastPage
                                total
                            }
                            data {
                                id
                                team {
                                    id
                                    name
                                    created_at
                                    updated_at
                                }
                                name
                                repository
                                created_at
                                updated_at
                            }
                        }
                    }',
                ]
            );
    }

    public function testProjectsQuery()
    {
        $this
            ->postProjectsQuery()
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access projects',
                ]
            );
    }

    public function testProjectsQueryAsAdmin()
    {
        $this->user->addRole('admin');

        $projects = factory(Project::class, 12)
            ->create()
            ->take(10)
            ->map(
                function ($project) {
                    return [
                        'id' => (string) $project->id,
                        'team' => [
                            'id' => (string) $project->team->id,
                            'name' => $project->team->name,
                            'created_at' => $this->formatDateTime($project->team->created_at),
                            'updated_at' => $this->formatDateTime($project->team->updated_at),
                        ],
                        'name' => $project->name,
                        'repository' => $project->repository,
                        'created_at' => $this->formatDateTime($project->created_at),
                        'updated_at' => $this->formatDateTime($project->updated_at),
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'projects' => [
                    'data' => $projects,
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
            ->postProjectsQuery()
            ->assertOk()
            ->assertExactJson($expected);
    }

    protected function postProjectQuery(int $id)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        project(id: {$id}) {
                            id
                            name
                        }
                    }",
                ]
            );
    }

    public function testProjectQueryAsOther()
    {
        $project = factory(Project::class)->create();

        $expected = [
            'data' => [
                'project' => [
                    'id' => (string) $project->id,
                    'name' => $project->name,
                ],
            ],
        ];

        $this
            ->postProjectQuery($project->id)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }

    public function testProjectQuery()
    {
        $project = factory(Project::class)->create();
        $this->user->addTeam($project->team);

        $expected = [
            'data' => [
                'project' => [
                    'id' => (string) $project->id,
                    'name' => $project->name,
                ],
            ],
        ];

        $this
            ->postProjectQuery($project->id)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testProjectBuildsQuery()
    {
        $otherBuild = factory(Build::class)->create();
        $project = factory(Project::class)->create();

        $projectBuilds = factory(Build::class, 3)
            ->create(
                [
                    'project_id' => $project->id,
                ]
            )
            ->map(
                function ($build) {
                    return [
                        'id' => GlobalId::encode('Build', $build->id),
                        'commit' => $build->commit,
                    ];
                }
            )
            ->all();

        $expected = [
            'data' => [
                'projectBuilds' => $projectBuilds,
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        projectBuilds(project_id: \"{$project->id}\") {
                            id
                            commit
                        }
                    }",
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
