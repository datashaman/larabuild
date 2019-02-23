<?php

namespace Tests;

use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PassportTestCase extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        Passport::actingAs($this->user);
    }
}
