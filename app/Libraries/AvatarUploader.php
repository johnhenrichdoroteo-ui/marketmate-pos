<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;
use Throwable;

class AvatarUploader
{
    private string $publicDirectory = FCPATH . 'uploads/avatars/';
    private string $stagingDirectory = WRITEPATH . 'uploads/avatars-staging/';

    public function store(UploadedFile $file): string
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('The GD image extension is required to prepare avatars.');
        }

        foreach ([$this->stagingDirectory, $this->publicDirectory] as $directory) {
            if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                throw new RuntimeException('The avatar folder could not be created.');
            }
        }

        $extension = $file->getMimeType() === 'image/png' ? '.png' : '.jpg';
        $filename = bin2hex(random_bytes(16)) . $extension;
        $stagingPath = $this->stagingDirectory . bin2hex(random_bytes(16)) . $extension;
        $publicPath = $this->publicDirectory . $filename;

        try {
            $file->move($this->stagingDirectory, basename($stagingPath));

            $image = service('image');
            $image->withFile($stagingPath)->fit(300, 300, 'center');

            if (! $image->save($publicPath)) {
                throw new RuntimeException('The avatar could not be prepared.');
            }

            return $filename;
        } catch (Throwable $exception) {
            if (is_file($publicPath)) {
                unlink($publicPath);
            }

            throw $exception;
        } finally {
            if (is_file($stagingPath)) {
                unlink($stagingPath);
            }
        }
    }

    public function remove(?string $filename): void
    {
        if ($filename === null || preg_match('/\A[a-f0-9]{32}\.(?:jpg|png)\z/', $filename) !== 1) {
            return;
        }

        $path = $this->publicDirectory . $filename;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
