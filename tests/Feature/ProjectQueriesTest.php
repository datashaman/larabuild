<?php

namespace Tests\Feature;

use App\Models\Build;
use App\Models\Project;
use Tests\PassportTestCase;

class ProjectQueriesTest extends PassportTestCase
{
    public function testProjectBuilds()
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
