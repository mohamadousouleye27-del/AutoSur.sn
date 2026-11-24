<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class ImageStorage
{
    private ?Cloudinary $client = null;

    private function client(): Cloudinary
    {
        return $this->client ??= new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    /** Envoie une image (fichier envoyé ou chemin local) et retourne son public_id. */
    public function upload(UploadedFile|string $file, string $folder): string
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $result = $this->client()->uploadApi()->upload($path, [
            'folder'        => config('cloudinary.folder') . '/' . $folder,
            'resource_type' => 'image',
        ]);

        return $result['public_id'];
    }

    public function delete(string $publicId): void
    {
        try {
            $this->client()->uploadApi()->destroy($publicId, ['invalidate' => true]);
        } catch (\Throwable $e) {
            report($e); // une suppression ratée ne doit jamais bloquer l'utilisateur
        }
    }

    /** URL optimisée : recadrage, qualité et format automatiques. */
    public function url(string $publicId, int $width = 800, int $height = 500, string $crop = 'fill'): string
    {
        $transform = $crop === 'fill'
            ? "c_fill,g_auto,w_{$width},h_{$height},q_auto,f_auto"
            : "c_limit,w_{$width},h_{$height},q_auto,f_auto";

        return 'https://res.cloudinary.com/' . config('cloudinary.cloud_name') . "/image/upload/{$transform}/{$publicId}";
    }
}