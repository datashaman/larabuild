<?php

namespace Tests\Feature\Queries;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;
use Tests\TokenTestCase;

class BuildQueriesTest extends TokenTestCase
{
    /**
     * @return TestResponse
     */
    protected function postBuildsQuery()
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => '{
                        builds(count: 10) {
                            paginatorInfo {
                                count
                                currentPage
                                lastPage
                                total
                            }
                            data {
                                id
                                project {
                                    id
                                    name
                                    repository
                                }
                                status
                                commit
                                completedAt
                            }
                        }
                    }',
                ]
            );
    }

    public function testBuildsQuery()
    {
        $this
            ->postBuildsQuery()
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access builds',
                ]
            );
    }

    public function testBuildsQueryAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $builds = factory(Build::class, 12)
            ->create()
            ->sortByDesc('id')
            ->take(10)
            ->map(
                function ($build) {
                    return [
                        'id' => $build->id,
                        'project' => [
                            'id' => (string) $build->project->id,
                            'name' => $build->project->name,
                            'repository' => $build->project->repository,
                        ],
                        'status' => $build->status,
                        'commit' => $build->commit,
                        'completedAt' => $build->completed_at,
                    ];
                }
            )
            ->values()
            ->all();

        $expected = [
            'data' => [
                'builds' => [
                    'data' => $builds,
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
            ->postBuildsQuery()
            ->assertOk()
            ->assertExactJson($expected);
    }

    /**
     * @param string $projectId
     * @param int    $number
     *
     * @return TestResponse
     */
    protected function postBuildQuery(string $projectId, int $number)
    {
        return $this
            ->withBearer()
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        build(projectId: \"{$projectId}\", number: {$number}) {
                            id
                            project {
                                id
                                name
                            }
                            status
                            commit
                            completedAt
                        }
                    }",
                ]
            );
    }

    public function testBuildQueryAsOther()
    {
        $build = factory(Build::class)->create();

        $this
            ->postBuildQuery($build->project_id, $build->number)
            ->assertStatus(200)
            ->assertJsonFragment(
                [
                    'message' => 'Not authorized to access this field.',
                ]
            );
    }
    public function testBuildQuery()
    {
        $build = factory(Build::class)->create();

        $this->user->addTeam($build->project->team);

        $expected = [
            'data' => [
                'build' => [
                    'id' => $build->id,
                    'project' => [
                        'id' => (string) $build->project->id,
                        'name' => $build->project->name,
                    ],
                    'status' => $build->status,
                    'commit' => $build->commit,
                    'completedAt' => $build->completed_at,
                ],
            ],
        ];

        $this
            ->postBuildQuery($build->project_id, $build->number)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
