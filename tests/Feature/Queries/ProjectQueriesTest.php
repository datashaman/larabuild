<?php

namespace Tests\Feature\Queries;

use App\Models\Build;
use App\Models\Project;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TokenTestCase;

class ProjectQueriesTest extends TokenTestCase
{
    /**
     * @return TestResponse
     */
    protected function postProjectsQuery()
    {
        return $this
            ->withBearer()
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
                                    createdAt
                                    updatedAt
                                }
                                name
                                repository
                                createdAt
                                updatedAt
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
        $this->user->addRole('ADMIN');

        $projects = factory(Project::class, 12)
            ->create()
            ->sortBy(function($project) {
                return $project->name;
            })
            ->take(10)
            ->map(
                function ($project) {
                    return [
                        'id' => $project->id,
                        'team' => [
                            'id' => $project->team->id,
                            'name' => $project->team->name,
                            'createdAt' => $this->formatDateTime($project->team->created_at),
                            'updatedAt' => $this->formatDateTime($project->team->updated_at),
                        ],
                        'name' => $project->name,
                        'repository' => $project->repository,
                        'createdAt' => $this->formatDateTime($project->created_at),
                        'updatedAt' => $this->formatDateTime($project->updated_at),
                    ];
                }
            )
            ->values()
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

    protected function postProjectQuery(string $id)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        project(id: \"{$id}\") {
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
                    'id' => $project->id,
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
                    'id' => $project->id,
                    'name' => $project->name,
                ],
            ],
        ];

        $this
            ->postProjectQuery($project->id)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
