<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_auth_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('logout'));
        $this->assertTrue(Route::has('member.dashboard'));
    }
}
