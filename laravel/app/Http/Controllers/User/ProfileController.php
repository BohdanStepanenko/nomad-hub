<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Http\Requests\User\UpdateNameRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use App\Services\Auth\PasswordService;
use App\Services\Media\MediaService;
use App\Services\User\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        public PasswordService $passwordService,
        public MediaService $mediaService,
        public ProfileService $profileService,
        public AuthService $authService
    ) {
    }

    public function getProfileInfo(): UserResource
    {
        return UserResource::make(Auth::user());
    }

    /**
     * @throws \Exception
     */
    public function updateName(UpdateNameRequest $request): JsonResponse
    {
        $updatedName = $request->input('name');

        return $this->success($this->profileService->updateName($updatedName));
    }

    /**
     * @throws \Exception
     */
    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $updatedEmail = $request->input('email');

        return $this->success($this->profileService->updateEmail($updatedEmail));
    }

    /**
     * @throws \Exception
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $currentPassword = $request->input('currentPassword');
        $newPassword = $request->input('newPassword');

        return $this->success($this->passwordService->updatePassword($currentPassword, $newPassword));
    }

    public function uploadAvatar(UploadAvatarRequest $request): JsonResponse
    {
        $avatar = $request->file('avatar');

        return $this->success($this->mediaService->uploadAvatar($avatar));
    }

    public function deleteAvatar(): JsonResponse
    {
        return $this->success($this->mediaService->deleteAvatar());
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->success($this->authService->logout($request));
    }
}
