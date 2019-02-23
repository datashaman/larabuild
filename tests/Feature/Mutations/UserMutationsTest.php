<?php

namespace Tests\Feature\Mutations;

use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Tests\PassportTestCase;

class UserMutationsTest extends PassportTestCase
{
    public function postCreateUser(array $user)
    {
        return $this
            ->postJson(
                '/graphql',
                [
                    'query' => "
                        mutation createUser(\$user: UserInput!) {
                            createUser(user: \$user) {
                                id
                                name
                                email
                            }
                        }
                    ",
                    'variables' => [
                        'user' => $user,
                    ],
                ]
            );
    }

    public function testCreateUser()
    {
        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'secret',
        ];

        $this
            ->postCreateUser($user)
            ->assertOk()
            ->assertJsonFragment(
                [
                    'message' => 'You are not authorized to access createUser',
                ]
            );

        unset($user['password']);

        $this->assertDatabaseMissing('users', $user);
    }

    public function testCreateUserAsAdmin()
    {
        $this->user->addRole('admin');

        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $password = 'secret';

        $this
            ->postCreateUser($user + compact('password'))
            ->assertOk()
            ->assertJsonFragment($user);

        $user = User::where('email', $user['email'])->firstOrFail();

        $this->assertTrue(app(BcryptHasher::class)->check('secret', $user->password));
    }
}
