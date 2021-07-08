<?php

namespace Tests\Feature\Queries;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use DB;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Log;
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

    /**
     * @param int $number
     */
    protected function createBuilds(int $number)
    {
        $records = Collection::times($number, function () {
            return [
                'project_id' => factory(Project::class)->create()->id,
                'number' => $this->faker->unique()->numberBetween(1, 10000),
                'status' => $this->faker->randomElement(config('larabuild.statuses')),
                'commit' => $this->faker->sha1,
            ];
        })->all();

        DB::table('builds')->insert($records);
    }

    public function testBuildsQueryAsAdmin()
    {
        $this->user->addRole('ADMIN');

        $this->createBuilds(12);

        $builds = Build::all()
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

    /**
     * @return Build
     */
    protected function createBuild()
    {
        $owner = $this->faker->username;
        $repo = $this->faker->domainWord;
        $teamId = $this->faker->username;
        $projectId = $this->faker->domainWord;
        $commit = $this->faker->sha1;
        $number = $this->faker->numberBetween(1, 10000);

        $url = "https://api.github.com/repos/{$owner}/{$repo}/statuses/${commit}";

        $json = [
            'state' => 'pending',
            'target_url' => "http://larabuild.test/#/{$teamId}/{$projectId}/{$number}",
            'description' => 'The build is pending',
            'context' => 'ci/larabuild',
        ];

        Log::debug("Expected", $json);

        $this->mock(Client::class)
            ->shouldReceive('post')
            ->with($url, compact('json'));

        $team = factory(Team::class)->create(
            [
                'id' => $teamId,
            ]
        );

        $project = factory(Project::class)->create(
            [
                'id' => $projectId,
                'repository' => "https://github.com/{$owner}/{$repo}",
                'team_id' => $teamId,
            ]
        );

        return factory(Build::class)->create(
            [
                'commit' => $commit,
                'number' => $number,
                'project_id' => $projectId,
                'status' => 'NEW',
            ]
        );
    }

    public function testBuildQueryAsOther()
    {
        $build = $this->createBuild();

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
        $build = $this->createBuild();

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
