<?php

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $team = Team::firstOrCreate(
            [
                'name' => 'Example Team',
            ]
        );

        $user = User::firstOrCreate(
            [
                'email' => 'user@example.com',
            ],
            [
                'name' => 'Regular User',
                'email_verified_at' => now(),
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'remember_token' => str_random(10),
            ]
        );

        $adminUser = User::firstOrCreate(
            [
                'email' => 'admin-user@example.com',
            ],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'remember_token' => str_random(10),
            ]
        );
        $adminUser->addRole('admin');

        $teamUser = User::firstOrCreate(
            [
                'email' => 'team-user@example.com',
            ],
            [
                'name' => 'Team User',
                'email_verified_at' => now(),
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'remember_token' => str_random(10),
            ]
        );

        $teamAdminUser = User::firstOrCreate(
            [
                'email' => 'team-admin-user@example.com',
            ],
            [
                'name' => 'Team Admin User',
                'email_verified_at' => now(),
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'remember_token' => str_random(10),
            ]
        );
        $adminUser->addRole('team-admin', $team);

        Project::firstOrCreate(
            [
                'team_id' => $team->id,
                'repository' => 'https://github.com/datashaman/larabuild-example.git',
            ],
            [
                'name' => 'Example Project',
                'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEAuFBbL3XE6P4h0JGfjPWwsp6FkfQe5Pg9TsHHA/nNPEw2wx6n
0B3QClHxTX0IFWcDGnAag1lUBi3ibKv2/fmXIXzuO+kNeyYxgZt8ascAbzc8iBkP
mjGI8kXbPfww5WA8a37IKa4e/GmBBPnSi7EuM4cGfI1XItIGZPKp2lmXJ34bpFG6
068PL11XOO5Bu6TqbAjpYzWyeIe9+EgV4wgEinUGc2IXnsgXxGy6lWo2ti3Nx6zd
SLr3JBenwBUfH8cxD4pX5M9VT1QM3tUpLYS1yrGJyx69UF+8Tt0Chx4BZdLEdLb9
mNSATClJxNZW1+OXqAPA96LAEUqze4OQG4JRsQIDAQABAoIBAQCM/UsDXv8li2Cq
fvqhhT+JIyHhp/AKDqo3GJv4Opn4SgSJ9WVBGc0OV+hR8hbA6G/DRYXCfA5+O5M8
tb7WQJqPD1XdlkSts2WjUyE1PMzkRkiTgzggJ0wup6kyHTv5+ke9otnSqFMjmkEL
YV7hZMOGvv8DP8sr46Toi4fKc0Xg+pWA6g+yPICxEAKPVV9bkbGzeKWIwXCxuCQ1
LswSLUoVyAX6xVeYjgnOs1Nsb+VRtq2ijrjZN/j3fxnGNhsQejwrBx6RgK6NyTzR
S7D943AVtrtVedlX/Ly751VldCmEO2L+9CrLX6X62FunDk7H7afGhBhvRZRVZ1Ah
NYql6vEBAoGBAOeR2VcBSzkEyrexGLyoYBg+rbV4ZnUP+MW3SAnL0BLoqLxY62eJ
Y7CGCoIm9NyxilWrYU5RVJjrTPlSfZ3CGf9s3tuRvS95xYIPrXf2ZDvFDqMoia38
RRelQfiAbKcA/lzRq+V2XsbLvNdsro/jzITTPxMyR6KXUJcQmAp6FH3pAoGBAMvC
PSa0m5W2y061Pdesaw1YdrYqygtT9vgvlPJo/2H67Tevaf272zrqUxgjma4/PbB6
7WW0vtJ9YdPsdXLUbVazymHUJaeG9VVXWoAqDU6R40dPhhf1mjiGCyF93vhD1kR9
+Wqy+3e3iqocTAYpJ6P/lP1Ag85PaS4f2HxRrXCJAoGBAOWGHIQuVh7X7w4PTNOB
mG2vgoHCKtuQzyU5uv9qsnxrewPkpr9i4BqRYU0Ly9wLZW/whGwaFN3VK+BbsQJy
503S3TmIxJmP+wIlA+1JnKPZd96kSYLX7qu3MyJaOFd3lqbtc5Hmt54XRr/Hi2Y3
hfmJYJVoWrR/gnOZEPohcroxAoGATPyVElzHNGgepRyBw02YHTDBmc7NDD6rX9bK
llTpuWGP46xZhc9G5BnJT6OT22x1qIqpy/Xg67MIFYSQU9TDgzDnVNNNbuDlLVuW
DDrXUEp672Syq7bWkGjFJ+BhMLig6rwWUyRRM0icEe4jI2jFW4ekCZQouPj0KsNJ
jp9lwtECgYEAo5an01Buh4HtS5TpW6XN1O/POSKZpcUiFcOTI7j3a3DRN+WG1vgL
9KAkJT9LIT0gDAWzqOdeLl+Xkgq18eI2T4S/txy0KoR9CX241m0aOGcgZyTrD7+R
gtP3qkPtKvJQEuFbQnDzfe8GxD6G5rZB/emgMhvaMb/fqOhriZn3Kl4=
-----END RSA PRIVATE KEY-----
                ',
            ]
        );
    }
}
