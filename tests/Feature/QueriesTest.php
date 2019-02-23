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
use Laravel\Passport\ClientRepository;

class QueriesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $clientRepository = app(ClientRepository::class);

        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', ''
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->team = factory(Team::class)->create();
        $this->user = factory(User::class)->create();
        $this->team->users()->attach($this->user);

        $this->project = factory(Project::class)->create(['team_id' => $this->team->id]);

        $token = $this->user->createToken(uniqid())->accessToken;

        $this->headers = [
            'Authorization' => "Bearer $token",
        ];
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
                        'currentPage' => 1,
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
                                currentPage
                                total
                            }
                            data {
                                id
                                hash
                            }
                        }
                    }',
                ],
                $this->headers
            )
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
