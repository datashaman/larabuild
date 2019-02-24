<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Hashing\BcryptHasher;

class UserCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'larabuild:user {name} {email} {password} {--roles=}';

    /**
     * @var string
     */
    protected $description = 'Create a user';

    public function handle()
    {
        $attrs = $this->argument();
        $attrs['password'] = app(BcryptHasher::class)->make($attrs['password']);

        $user = User::create($attrs);

        $roles = collect(explode(',', $this->option('roles')))
            ->each(
                function ($role) use ($user) {
                    $user->addRole($role);
                }
            );

        if ($user->exists) {
            $this->info('User ' . $user->email . ' created');
        } else {
            $this->error('User ' . $user->email . ' NOT created');
            exit(1);
        }
    }
}
