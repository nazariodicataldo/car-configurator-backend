<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasFileUpload
{
    public function uploadImage(
        Request $request,
        string $folder,
        ?string $existingPath = null,
        string $fileField = 'file',
    ): string {
        $request->validate([
            $fileField => 'required|file|max:5120|mimes:jpg,jpeg,png,webp,svg',
        ]);

        $file = $request->file($fileField);
        $newPath = Str::uuid() . '_' . $file->getClientOriginalName();

        if ($existingPath) {
            $existingName = explode('_', basename($existingPath), 2)[1] ?? null;
            if ($existingName !== $file->getClientOriginalName()) {
                Storage::delete($existingPath);
            }
        }

        return $file->storeAs($folder, $newPath);
    }

    public function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::delete($path);
        }
    }
}
