<?php

namespace Tests\Feature;

use App\Models\Build;
use App\Models\Project;
use Tests\PassportTestCase;

class ProjectQueriesTest extends PassportTestCase
{
    public function testProjectsQuery()
    {
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
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testProjectQuery()
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
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        project(id: {$project->id}) {
                            id
                            name
                        }
                    }",
                ]
            )
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
                        'id' => (string) $build->id,
                        'commit' => $build->commit,
                        'hash' => $build->hash,
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
                            hash
                        }
                    }",
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
