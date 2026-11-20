<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthDemoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('demo.account'))->assertRedirect(route('demo.login'));
    }

    public function test_users_can_register(): void
    {
        $this->post(route('demo.register'), [
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'password' => 'Secret-pass-123',
            'password_confirmation' => 'Secret-pass-123',
        ])->assertRedirect(route('demo.account'));

        $this->assertAuthenticated();
        $this->assertNotSame('Secret-pass-123', User::first()->password, 'Password must be hashed');
    }

    public function test_registration_validates_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('demo.register'), [
            'name' => '',
            'email' => 'taken@example.com',
            'password' => 'a',
            'password_confirmation' => 'b',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_users_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->post(route('demo.login'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('demo.login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('demo.account'));
        $this->assertAuthenticatedAs($user);

        $this->get(route('demo.account'))->assertOk()->assertSee($user->name);

        $this->post(route('demo.logout'))->assertRedirect(route('demo.login'));
        $this->assertGuest();
    }

    public function test_logged_in_users_cannot_see_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('demo.login'))
            ->assertRedirect(route('demo.account'));
    }

    public function test_route_parameter_demo(): void
    {
        $this->get('/demos/hello')->assertOk()->assertSee('Hello, stranger!');
        $this->get('/demos/hello/Ada')->assertOk()->assertSee('Hello, Ada!');
        $this->get('/demos/hello/Ada?shout=1')->assertSee('HELLO, ADA!');
        $this->get('/demos/hello/R2D2')->assertNotFound();
    }
}
