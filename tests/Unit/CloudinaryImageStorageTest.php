<?php

namespace Tests\Unit;

use App\Services\CloudinaryImageStorage;
use RuntimeException;
use Tests\TestCase;

class CloudinaryImageStorageTest extends TestCase
{
    public function test_cloudinary_references_generate_secure_delivery_urls(): void
    {
        config([
            'services.cloudinary.cloud_name' => 'demo',
            'services.cloudinary.api_key' => 'test-key',
            'services.cloudinary.api_secret' => 'test-secret',
        ]);

        $url = (new CloudinaryImageStorage)->url('cloudinary:1234567890:productos/figura-123');
        $urlParts = parse_url($url);

        $this->assertSame(
            '/demo/image/upload/v1234567890/productos/figura-123',
            $urlParts['path'] ?? null
        );
    }

    public function test_local_and_remote_image_references_remain_compatible(): void
    {
        $storage = new CloudinaryImageStorage;

        $this->assertSame(asset('storage/productos/figura.jpg'), $storage->url('productos/figura.jpg'));
        $this->assertSame('https://example.test/avatar.jpg', $storage->url('https://example.test/avatar.jpg'));
    }

    public function test_cloudinary_references_require_credentials(): void
    {
        config(['services.cloudinary' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cloudinary no está configurado.');

        (new CloudinaryImageStorage)->url('cloudinary:1234567890:productos/figura-123');
    }
}
