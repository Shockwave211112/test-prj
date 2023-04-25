<?php

namespace Tests\Feature;

use App\Models\ResetPin;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->artisan('db:seed --class TestSeeder');
    }
    public function testRegister(): void
    {
        $user = User::factory()->make();
        $response = $this->post('/api/register',
            [
                'name' => $user->getAttributes()['name'],
                'email' => $user->getAttributes()['email'],
                'password' => $user->getAttributes()['password'],
                'password_confirmation' => $user->getAttributes()['password'],
                'pin_code' => $user->getAttributes()['pin_code'],
                'role_id' => $user->getAttributes()['role_id']
            ]
        );
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.token', csrf_token());
    }

    public function testAdminAuth(): void
    {
        $user = User::factory()->create(['password' => bcrypt($password = 'abobus1234'), 'role_id' => 1]);
        $response = $this->post('/api/login',
            [
                'email' => $user->email,
                'password' => $password,
            ]
        );
        $response->assertStatus(200);
        $response->assertJsonPath('data.token', csrf_token());
    }
    public function testMailSend(): void
    {
        $user = User::all()->random();

        $response = $this->post('/api/password/forgot',
            [
                'email' => $user->email,
            ]
        );
        $response->assertStatus(200);
        $response = $this->post('/api/password/forgot',
            [
                'email' => 'efasofoasf@mail.net',
            ]
        );
        $response->assertStatus(404);
    }
    public function testPinInput(): void
    {
        $user = User::all()->random();
        $reset_pin = ResetPin::create(['email' => $user->email, 'pin_code' => 111333, 'expires_at' => Carbon::tomorrow()]);
        $response = $this->post('/api/password/reset/getpin',
            [
                'pin_code' => $reset_pin->pin_code,
            ]
        );
        $response->assertStatus(200);
        $response = $this->post('/api/password/reset/getpin',
            [
                'pin_code' => 999999,
            ]
        );
        $response->assertStatus(404);
    }
    public function testNewPassword(): void
    {
        $user = User::all()->random();
        $newUser = User::make(['password' => 'abobus12345']);
        $reset_pin = ResetPin::create(['email' => $user->email, 'pin_code' => 111333, 'expires_at' => Carbon::tomorrow()]);
        $response = $this->post('/api/password/reset',
            [
                'pin_code' => $reset_pin->pin_code,
                'password' => $newUser->password,
                'password_confirmation' => $newUser->password
            ]
        );
        $this->assertTrue(Hash::check($newUser->password, User::where('email', '=', $user->email)
            ->first()->password));
        $response->assertStatus(200);
        $response = $this->post('/api/password/reset',
            [
                'pin_code' => 111111,
                'password' => $newUser->password,
                'password_confirmation' => $newUser->password
            ]
        );
        $response->assertStatus(404);
    }
    public function testPinExpire(): void
    {
        $user = User::all()->random();
        $reset_pin = ResetPin::create(['email' => $user->email, 'pin_code' => 111333, 'expires_at' => Carbon::now()->subHours(12)]);
        $this->assertDatabaseHas('reset_pins', [
            'pin_code' => $reset_pin->pin_code
        ]);
        $response = $this->post('/api/password/reset/getpin',
            [
                'pin_code' => $reset_pin->pin_code,
            ]
        );
        $response->assertStatus(405);
        $response = $this->post('/api/password/reset',
            [
                'pin_code' => $reset_pin->pin_code,
                'password' => "qwerty12345",
                'password_confirmation' => "qwerty12345"
            ]
        );
        $response->assertStatus(405);
    }

    public function testWaiterAuth(): void
    {
        $user = User::factory()->create(['role_id' => 3]);
        $response = $this->post('/api/login',
            [
                'email' => $user->email,
                'password' => $user->password,
            ]
        );
        $response->assertStatus(401);

        $response = $this->post('/api/login',
            [
                'pin_code' => $user->pin_code
            ]
        );
        $response->assertStatus(200);
        $response->assertJsonPath('data.token', csrf_token());

        $response = $this->actingAs($user)->post('/api/logout');
        $response->assertStatus(200);
    }
    public function testLogout(): void
    {
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post("/api/logout");
        $response->assertStatus(200);
    }

}
