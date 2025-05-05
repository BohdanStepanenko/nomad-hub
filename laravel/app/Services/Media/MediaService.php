<?php

namespace App\Services\Media;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class MediaService
{
    protected string $imagePath = 'images/';

    protected string $userPath = 'users/';

    protected int $optimizeImageMaxSize = 10000000;

    public function uploadAvatar(UploadedFile $uploadedFile): bool
    {
        $user = Auth::user();
        $path = $this->generateUserAvatarPath($user);
        $storagePath = $this->uploadImage($uploadedFile, $path);

        return $user->update(['avatar' => $storagePath]);
    }

    public function deleteAvatar(): bool
    {
        $user = Auth::user();

        if ($user?->avatar !== null) {
            if ($this->removeMedia($user?->avatar)) {
                $user->update(['avatar' => null]);

                return true;
            }
        }

        return false;
    }

    private function generateUserAvatarPath(User $user): string
    {
        return $this->imagePath . $this->userPath . $user->id;
    }

    private function uploadImage(UploadedFile $uploadedFile, string $path): string
    {
        // Check if the file needs to be optimized
        if ($uploadedFile->getSize() > $this->optimizeImageMaxSize) {
            $storagePath = $this->optimizeImage($path, $uploadedFile);
        } else {
            $storagePath = Storage::disk('s3')->put($path, $uploadedFile);
        }

        return $storagePath;
    }

    private function optimizeImage($path, $uploadedFile): bool
    {
        $image = Image::make($uploadedFile)
            ->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($uploadedFile->getClientOriginalExtension(), 75);

        return Storage::disk('s3')->put($path, $image);
    }

    private function removeMedia(string $path): bool
    {
        $disk = Storage::disk('s3');

        if ($disk->exists($path)) {
            $disk->delete($path);

            return true;
        }

        return false;
    }
}
