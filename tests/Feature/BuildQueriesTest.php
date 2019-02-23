<?php

namespace Tests\Feature;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use DB;
use Tests\PassportTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class BuildQueriesTest extends PassportTestCase
{
    public function testBuildsQuery()
    {
        $builds = factory(Build::class, 12)
            ->create()
            ->take(10)
            ->map(
                function ($build) {
                    return [
                        'id' => (string) $build->id,
                        'hash' => $build->hash,
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
                                hash
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
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testBuildQuery()
    {
        $build = factory(Build::class)->create();

        $expected = [
            'data' => [
                'build' => [
                    'id' => (string) $build->id,
                    'hash' => $build->hash,
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
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        build(hash: \"{$build->hash}\") {
                            id
                            hash
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
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
