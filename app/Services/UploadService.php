<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Sube un archivo al storage público y devuelve su URL.
     */
    public function upload(UploadedFile $file, string $folder = 'evidences'): string
    {
        // nombre único
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // guardar en storage/app/public/evidences
        $path = $file->storeAs($folder, $fileName, 'public');

        // URL pública
        return asset('storage/' . $path);
    }
}