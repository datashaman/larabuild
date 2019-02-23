<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $teamId = DB::table('teams')->insertGetId(
            [
                'name' => 'Sample Team',
            ]
        );

        DB::table('projects')->insertGetId(
            [
                'team_id' => $teamId,
                'name' => 'Sample Project',
                'repository' => 'https://github.com/datashaman/larabuild-test',
            ]
        );
    }
}
