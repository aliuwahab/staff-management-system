<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->disableExceptionHandling();
    }



    /** @test */
    public function an_authenticated_admin_can_view_all_admins()
    {
        $user = $this->factoryWithoutObservers(User::class)->create([
            'is_admin' => true
        ]);
        $user = $this->actingAs($user);
        $this->factoryWithoutObservers(User::class, 5);
        $this->factoryWithoutObservers(User::class)->create([
            'name' => 'Aliu'
        ]);

        $response = $user->get('/users');
        $response->assertStatus(200);
        $response->assertViewIs("all-admin-members");
        $response->assertViewHas("admins");
        $response->assertSeeText("Aliu");

    }



    /** @test */
    public function an_unauthenticated_admin_cannot_view_all_admins()
    {
        $this->factoryWithoutObservers(User::class)->create([
            'is_admin' => true
        ]);
        $this->factoryWithoutObservers(User::class, 5);
        $this->factoryWithoutObservers(User::class)->create([
            'name' => 'Aliu'
        ]);

        $this->expectException("Illuminate\Auth\AuthenticationException");
        $this->get('/users');

    }



    /** @test */
    public function a_user_who_is_not_an_admin_cannot_view_all_admins()
    {
        $user = $this->factoryWithoutObservers(User::class)->create([
            'is_admin' => false
        ]);
        $user = $this->actingAs($user);
        $this->factoryWithoutObservers(User::class, 5);
        $admin = $this->factoryWithoutObservers(User::class)->create([
            'name' => 'Aliu'
        ]);

        $response = $user->get('/users');
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));

    }




}
