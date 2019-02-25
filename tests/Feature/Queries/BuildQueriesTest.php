<?php

namespace Tests\Feature\Queries;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Execution\Utils\GlobalId;
use Tests\PassportTestCase;

class BuildQueriesTest extends PassportTestCase
{
    /**
     * @return TestResponse
     */
    protected function postBuildsQuery()
    {
        return $this
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
                                completed_at
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
        $this->user->addRole('admin');

        $builds = factory(Build::class, 12)
            ->create()
            ->sortByDesc('id')
            ->take(10)
            ->map(
                function ($build) {
                    return [
                        'id' => GlobalId::encode('Build', $build->id),
                        'project' => [
                            'id' => (string) $build->project->id,
                            'name' => $build->project->name,
                            'repository' => $build->project->repository,
                        ],
                        'status' => $build->status,
                        'commit' => $build->commit,
                        'completed_at' => $build->completed_at,
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
     * @param string $id
     *
     * @return TestResponse
     */
    protected function postBuildQuery(string $id)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        build(id: \"{$id}\") {
                            id
                            project {
                                id
                                name
                            }
                            status
                            commit
                            completed_at
                        }
                    }",
                ]
            );
    }

    public function testBuildQueryAsOther()
    {
        $build = factory(Build::class)->create();
        $globalId = GlobalId::encode('Build', $build->id);

        $this
            ->postBuildQuery($globalId)
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
        $globalId = GlobalId::encode('Build', $build->id);

        $this->user->addTeam($build->project->team);

        $expected = [
            'data' => [
                'build' => [
                    'id' => $globalId,
                    'project' => [
                        'id' => (string) $build->project->id,
                        'name' => $build->project->name,
                    ],
                    'status' => $build->status,
                    'commit' => $build->commit,
                    'completed_at' => $build->completed_at,
                ],
            ],
        ];

        $this
            ->postBuildQuery($globalId)
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
