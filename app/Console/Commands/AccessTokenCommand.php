<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AccessTokenCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'larabuild:access-token {tokenName} {--id=} {--email=}';

    /**
     * @var string
     */
    protected $description = 'Generate an access token for the user.';

    /**
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('id')) {
            $user = User::findOrFail($this->option('id'));
        } elseif ($this->option('email')) {
            $user = User::where('email', $this->option('email'))->firstOrFail();
        } else {
            $this->error('Either id or email must be specified');
            exit(1);
        }

        $this->info($user->createToken($this->argument('tokenName'))->accessToken);
    }
}
