<?php

namespace Tests;

use App\Models\User;
use Tests\TestCase;

class TokenTestCase extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    protected function withBearer(User $user = null)
    {
        if (is_null($user)) {
            $user = $this->user;
        }

        return $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $user->api_token,
            ]
        );
    }
}
