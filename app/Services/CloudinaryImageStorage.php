<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryImageStorage
{
    private const REFERENCE_PREFIX = 'cloudinary:';

    private ?Cloudinary $client = null;

    public function upload(UploadedFile $file, string $folder): string
    {
        $path = $file->getRealPath();

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('No se pudo leer el archivo de imagen para subirlo a Cloudinary.');
        }

        $response = $this->client()->uploadApi()->upload($path, [
            'folder' => $folder,
        ]);
        $publicId = $response['public_id'] ?? null;
        $version = $response['version'] ?? null;

        if (
            ! is_string($publicId)
            || $publicId === ''
            || (! is_int($version) && (! is_string($version) || ! ctype_digit($version)))
        ) {
            throw new RuntimeException('Cloudinary no devolvió los datos necesarios de la imagen subida.');
        }

        return self::REFERENCE_PREFIX.$version.':'.$publicId;
    }

    public function url(?string $reference): ?string
    {
        if ($reference === null || $reference === '') {
            return null;
        }

        if (str_starts_with($reference, self::REFERENCE_PREFIX)) {
            [$version, $publicId] = $this->parseReference($reference);

            return $this->client()->image($publicId)->version($version)->toUrl();
        }

        if (str_starts_with($reference, 'https://') || str_starts_with($reference, 'http://')) {
            return $reference;
        }

        return asset('storage/'.ltrim($reference, '/'));
    }

    public function delete(?string $reference): void
    {
        if (! is_string($reference) || ! str_starts_with($reference, self::REFERENCE_PREFIX)) {
            return;
        }

        [, $publicId] = $this->parseReference($reference);
        $this->client()->uploadApi()->destroy($publicId);
    }

    private function parseReference(string $reference): array
    {
        $parts = explode(':', substr($reference, strlen(self::REFERENCE_PREFIX)), 2);

        if (count($parts) !== 2 || ! ctype_digit($parts[0]) || $parts[1] === '') {
            throw new RuntimeException('La referencia de imagen de Cloudinary no tiene un formato válido.');
        }

        return [$parts[0], $parts[1]];
    }

    private function client(): Cloudinary
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $settings = config('services.cloudinary');
        $cloudName = $settings['cloud_name'] ?? null;
        $apiKey = $settings['api_key'] ?? null;
        $apiSecret = $settings['api_secret'] ?? null;

        if (! $cloudName || ! $apiKey || ! $apiSecret) {
            throw new RuntimeException(
                'Cloudinary no está configurado. Define CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY y CLOUDINARY_API_SECRET.'
            );
        }

        return $this->client = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }
}
