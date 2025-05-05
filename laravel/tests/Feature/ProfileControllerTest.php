<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
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

        User::unsetEventDispatcher();

        Notification::fake();

        Storage::fake('s3');

        $this->user = User::factory()->create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ]);
    }

    public function testAuthorizedUserCanGetProfileInfo(): void
    {
        $this->withExceptionHandling();

        $response = $this->actingAs($this->user)
            ->getJson('/api/profile');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotGetProfileInfo(): void
    {
        $this->withExceptionHandling();

        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanUploadAvatar(): void
    {
        $this->withExceptionHandling();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $input = [
            'avatar' => $file,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(
                '/api/profile/avatar',
                $input
            );

        $response->assertStatus(200);

        $expectedPath = 'images/users/' . $this->user->id;

        Storage::disk('s3')->assertExists($expectedPath);
    }

    public function testUnauthorizedUserCannotUploadAvatar(): void
    {
        $this->withExceptionHandling();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $input = [
            'avatar' => $file,
        ];

        $uploadResponse = $this->postJson(
            '/api/profile/avatar',
            $input
        );

        $uploadResponse->assertStatus(401);
    }

    public function testAuthorizedUserCanDeleteAvatar(): void
    {
        $this->withExceptionHandling();

        $expectedPath = 'images/users/' . $this->user->id;

        $this->user->update(['avatar' => $expectedPath . '/avatar.jpg']);

        Storage::disk('s3')->put($expectedPath . '/avatar.jpg', 'dummy content');

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/profile/avatar');

        $response->assertStatus(200);

        Storage::disk('s3')->assertMissing($expectedPath . '/avatar.jpg');
    }

    public function testUnauthorizedUserCannotDeleteAvatar(): void
    {
        $this->withExceptionHandling();

        $expectedPath = 'images/users/' . $this->user->id;

        $this->user->update(['avatar' => $expectedPath . '/avatar.jpg']);

        Storage::disk('s3')->put($expectedPath . '/avatar.jpg', 'dummy content');

        $response = $this->deleteJson('/api/profile/avatar');

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanUpdatePassword(): void
    {
        $this->withExceptionHandling();

        $newPassword = 'new_password';

        $input = [
            'currentPassword' => self::PASSWORD,
            'newPassword' => $newPassword,
            'newPassword_confirmation' => $newPassword,
        ];

        $passwordResponse = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/password',
                $input
            );

        $passwordResponse->assertStatus(200);

        $this->assertTrue(Hash::check($newPassword, $this->user->fresh()->password));
    }

    public function testUnauthorizedUserCannotUpdatePassword(): void
    {
        $this->withExceptionHandling();

        $newPassword = 'new_password';

        $input = [
            'currentPassword' => self::PASSWORD,
            'newPassword' => $newPassword,
            'newPassword_confirmation' => $newPassword,
        ];

        $passwordResponse = $this->putJson(
            '/api/profile/password',
            $input
        );

        $passwordResponse->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdatePasswordWithoutCurrentPassword(): void
    {
        $this->withExceptionHandling();

        $newPassword = 'new_password';

        $input = [
            'currentPassword' => '',
            'newPassword' => $newPassword,
            'newPassword_confirmation' => $newPassword,
        ];

        $passwordResponse = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/password',
                $input
            );

        $passwordResponse->assertStatus(422);
    }

    public function testAuthorizedUserCannotUpdatePasswordWithoutNewPassword(): void
    {
        $this->withExceptionHandling();

        $newPassword = 'new_password';

        $input = [
            'currentPassword' => '',
            'newPassword' => '',
            'newPassword_confirmation' => $newPassword,
        ];

        $passwordResponse = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/password',
                $input
            );

        $passwordResponse->assertStatus(422);
    }

    public function testAuthorizedUserCannotUpdatePasswordWithoutNewPasswordConfirmation(): void
    {
        $this->withExceptionHandling();

        $newPassword = 'new_password';

        $input = [
            'currentPassword' => '',
            'newPassword' => $newPassword,
            'newPassword_confirmation' => '',
        ];

        $passwordResponse = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/password',
                $input
            );

        $passwordResponse->assertStatus(422);
    }

    public function testAuthorizedUserCannotUpdatePasswordWithEmptyFields(): void
    {
        $this->withExceptionHandling();

        $input = [
            'currentPassword' => '',
            'newPassword' => '',
            'newPassword_confirmation' => '',
        ];

        $passwordResponse = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/password',
                $input
            );

        $passwordResponse->assertStatus(422);
    }

    public function testAuthorizedUserCanUpdateName(): void
    {
        $this->withExceptionHandling();

        $newName = 'Updated Name';

        $input = [
            'name' => $newName,
        ];

        $response = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/name',
                $input
            );

        $response->assertStatus(200);

        $this->assertEquals($newName, $this->user->fresh()->name);
    }

    public function testAuthorizedUserCannotUpdateNameWithEmptyName(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => '',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(
                '/api/profile/name',
                $input
            );

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotUpdateName(): void
    {
        $this->withExceptionHandling();

        $input = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson(
            '/api/profile/name',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCanUpdateEmail(): void
    {
        $this->withExceptionHandling();

        $newEmail = 'updated@test.com';
        $input = [
            'email' => $newEmail,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/profile/email', $input);

        $response->assertStatus(200);

        $this->assertEquals($newEmail, $this->user->fresh()->email);
        $this->assertNotNull($this->user->fresh()->verification_token);
        $this->assertNull($this->user->fresh()->email_verified_at);
    }

    public function testAuthorizedUserCannotUpdateEmailWithEmptyEmail(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => '',
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/profile/email', $input);

        $response->assertStatus(422);
    }

    public function testUnauthorizedUserCannotUpdateEmail(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => 'updated@test.com',
        ];

        $response = $this->putJson(
            '/api/profile/email',
            $input
        );

        $response->assertStatus(401);
    }

    public function testAuthorizedUserCannotUpdateEmailIfSameAsCurrent(): void
    {
        $this->withExceptionHandling();

        $input = [
            'email' => self::EMAIL,
        ];

        $response = $this->actingAs($this->user)
            ->putJson('/api/profile/email', $input);

        $response->assertStatus(422);
    }

    public function testAuthorizedUserCanLogout(): void
    {
        $this->withExceptionHandling();

        $token = $this->user->createToken('Test Token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
            ->postJson('/api/profile/logout');

        $response->assertStatus(200);
    }

    public function testUnauthorizedUserCannotLogout(): void
    {
        $this->withExceptionHandling();

        $response = $this->postJson('/api/profile/logout');

        $response->assertStatus(401);
    }
}
