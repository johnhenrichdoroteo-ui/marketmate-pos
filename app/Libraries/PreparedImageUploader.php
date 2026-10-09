<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;
use Throwable;

/** Stores only generated, display-ready images under public/uploads. */
class PreparedImageUploader
{
    private string $publicDirectory;
    private string $stagingDirectory;

    public function __construct(string $kind, private int $width, private int $height)
    {
        $this->publicDirectory = FCPATH . 'uploads/' . $kind . '/';
        $this->stagingDirectory = WRITEPATH . 'uploads/' . $kind . '-staging/';
    }

    public function store(UploadedFile $file): string
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('The GD image extension is required to prepare images.');
        }

        foreach ([$this->stagingDirectory, $this->publicDirectory] as $directory) {
            if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                throw new RuntimeException('The image folder could not be created.');
            }
        }

        $extension = $file->getMimeType() === 'image/png' ? '.png' : '.jpg';
        $filename = bin2hex(random_bytes(16)) . $extension;
        $stagingName = bin2hex(random_bytes(16)) . $extension;
        $stagingPath = $this->stagingDirectory . $stagingName;
        $publicPath = $this->publicDirectory . $filename;

        try {
            $file->move($this->stagingDirectory, $stagingName);
            $image = service('image');
            $image->withFile($stagingPath)->fit($this->width, $this->height, 'center');

            if (! $image->save($publicPath)) {
                throw new RuntimeException('The image could not be prepared.');
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
