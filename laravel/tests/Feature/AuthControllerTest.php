<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordMail;
use App\Mail\VerificationMail;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public $mockConsoleOutput = false;

    private const EMAIL = 'test@test.com';

    private const PASSWORD = 'password_test';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install', [
            '--env' => 'testing',
            '--no-interaction' => true,
        ]);

        Mail::fake();

        $this->seed(RolesSeeder::class);

        $this->user = User::factory()->create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ])->assignRole('Client');
    }

    public function testUnauthorizedUserCanLogin(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => self::EMAIL,
            'password' => Hash::make(self::PASSWORD),
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotLoginWithWrongEmail(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => fake()->email,
            'password' => Hash::make(self::PASSWORD),
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotLoginWithWrongPassword(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => self::EMAIL,
            'password' => Hash::make(fake()->password(8)),
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotLoginWithWrongEmailAndPassword(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => fake()->email,
            'password' => Hash::make(fake()->password(8)),
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotLoginWithEmptyEmail(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => '',
            'password' => Hash::make(self::PASSWORD),
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotLoginWithEmptyPassword(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => self::EMAIL,
            'password' => '',
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotLoginWithEmptyEmailAndPassword(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => '',
            'password' => '',
        ];

        $response = $this->postJson(
            'api/auth/login',
            $input
        );

        $response->assertStatus(422);
    }

    public function testNewUserCanRegister(): void
    {
        $this->withExceptionHandling();

        $email = fake()->email;
        $password = fake()->password(8);

        $input = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson(
            '/api/auth/register',
            $input
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function testNewUserCannotRegisterWithExistEmail(): void
    {
        $this->withExceptionHandling();

        $email = self::EMAIL;
        $password = fake()->password(8);

        $input = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson(
            '/api/auth/register',
            $input
        );

        $response->assertStatus(422);
    }

    public function testNewUserCannotRegisterWithEmptyEmail(): void
    {
        $this->withExceptionHandling();

        $email = '';
        $password = fake()->password(8);

        $input = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson(
            '/api/auth/register',
            $input
        );

        $response->assertStatus(422);
    }

    public function testNewUserCannotRegisterWithEmptyPassword(): void
    {
        $this->withExceptionHandling();

        $email = fake()->email;
        $password = '';

        $input = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson(
            '/api/auth/register',
            $input
        );

        $response->assertStatus(422);
    }

    public function testNewUserCannotRegisterWithoutPasswordConfirmation(): void
    {
        $this->withExceptionHandling();

        $email = fake()->email;
        $password = fake()->password(8);

        $input = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => '',
        ];

        $response = $this->postJson(
            '/api/auth/register',
            $input
        );

        $response->assertStatus(422);
    }

    public function testNewUserCannotRegisterWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $email = '';
        $password = '';

        $input = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson(
            '/api/auth/register',
            $input
        );

        $response->assertStatus(422);
    }

    public function testVerifiesEmailUsingToken(): void
    {
        $this->withExceptionHandling();

        $dummyToken = fake()->word;
        $user = User::factory()->create([
            'email' => fake()->email,
            'email_verified_at' => null,
            'verification_token' => $dummyToken,
        ]);

        $response = $this->getJson('/api/auth/verify-email/' . $dummyToken);

        $response->assertStatus(200);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function testNotVerifiesEmailUsingWrongToken(): void
    {
        $this->withExceptionHandling();

        $user = User::factory()->create([
            'email' => fake()->email,
            'email_verified_at' => null,
            'verification_token' => fake()->word,
        ]);

        $response = $this->getJson('/api/auth/verify-email/' . fake()->word());

        $response->assertStatus(200);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function testPasswordResetLinkSendToValidEmail(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson('/api/auth/password/forgot', ['email' => $this->user->email]);
        $response->assertStatus(200);

        Mail::assertSent(ResetPasswordMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function testPasswordResetLinkNotSendToExceptEmail(): void
    {
        $this->withExceptionHandling();

        $exceptEmail = fake()->email;

        $response = $this->postJson('/api/auth/password/forgot', ['email' => $exceptEmail]);
        $response->assertStatus(422);

        Mail::assertNotSent(ResetPasswordMail::class, function ($mail) use ($exceptEmail) {
            return $mail->hasTo($exceptEmail);
        });
    }

    public function testAuthorizedUserCanResendEmailVerificationLinkToValidEmail(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson('/api/auth/resend-verification', ['email' => $this->user->email]);
        $response->assertStatus(200);

        Mail::assertSent(VerificationMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });
    }

    public function testAuthorizedUserCannotResendEmailVerificationLinkToExceptEmail(): void
    {
        $this->withExceptionHandling();

        $exceptEmail = fake()->email;

        $response = $this->postJson('/api/auth/resend-verification', ['email' => $exceptEmail]);
        $response->assertStatus(422);

        Mail::assertNotSent(VerificationMail::class, function ($mail) use ($exceptEmail) {
            return $mail->hasTo($exceptEmail);
        });
    }

    public function testUserCanResetPasswordWithToken(): void
    {
        $this->withExceptionHandling();

        $token = app('auth.password.broker')->createToken($this->user);

        $newPassword = fake()->password(8);

        $input = [
            'email' => $this->user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(200);

        $this->assertTrue(Hash::check($newPassword, $this->user->fresh()->password));
    }

    public function testUserCannotResetPasswordWithoutExistEmail(): void
    {
        $this->withExceptionHandling();

        $token = app('auth.password.broker')->createToken($this->user);

        $newPassword = fake()->password(8);

        $input = [
            'email' => fake()->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUserCannotResetPasswordWithEmptyEmail(): void
    {
        $this->withExceptionHandling();

        $token = app('auth.password.broker')->createToken($this->user);

        $newPassword = fake()->password(8);

        $input = [
            'email' => '',
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUserCannotResetPasswordWithEmptyToken(): void
    {
        $this->withExceptionHandling();

        app('auth.password.broker')->createToken($this->user);

        $newPassword = fake()->password(8);

        $input = [
            'email' => $this->user->email,
            'token' => '',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUserCannotResetPasswordWithEmptyPassword(): void
    {
        $this->withExceptionHandling();

        $token = app('auth.password.broker')->createToken($this->user);

        $newPassword = fake()->password(8);

        $input = [
            'email' => $this->user->email,
            'token' => $token,
            'password' => '',
            'password_confirmation' => $newPassword,
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUserCannotResetPasswordWithoutPasswordConfirmation(): void
    {
        $this->withExceptionHandling();

        $token = app('auth.password.broker')->createToken($this->user);

        $newPassword = fake()->password(8);

        $input = [
            'email' => $this->user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => '',
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(422);
    }

    public function testUserCannotResetPasswordWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        app('auth.password.broker')->createToken($this->user);

        $input = [
            'email' => '',
            'token' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->postJson(
            '/api/auth/password/reset',
            $input
        );

        $response->assertStatus(422);
    }
}
