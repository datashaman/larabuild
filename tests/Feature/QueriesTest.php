<?php

namespace Tests\Feature;

use App\Models\Build;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;

class QueriesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->team = factory(Team::class)->create();
        $this->user = factory(User::class)->create();
        $this->team->users()->attach($this->user);

        $this->project = factory(Project::class)->create(['team_id' => $this->team->id]);

        Passport::actingAs($this->user);
    }

    public function testBuilds()
    {
        $builds = factory(Build::class, 12)
            ->create(['project_id' => $this->project->id])
            ->take(10)
            ->map(
                function ($build) {
                    return [
                        'id' => (string) $build->id,
                        'hash' => $build->hash,
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
                            }
                        }
                    }',
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testBuild()
    {
        $build = factory(Build::class)->create();

        $expected = [
            'data' => [
                'build' => [
                    'id' => (string) $build->id,
                    'hash' => $build->hash,
                ],
            ],
        ];

        $this
            ->postJson(
                '/graphql',
                [
                    'query' => "{
                        build(id: {$build['id']}) {
                            id
                            hash
                        }
                    }",
                ]
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
