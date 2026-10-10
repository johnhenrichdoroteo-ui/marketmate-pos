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

        $serverless = (bool) getenv('VERCEL');
        $directories = [$this->stagingDirectory];
        if (! $serverless) {
            $directories[] = $this->publicDirectory;
        }

        foreach ($directories as $directory) {
            if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                throw new RuntimeException('The image folder could not be created.');
            }
        }

        $extension = $file->getMimeType() === 'image/png' ? '.png' : '.jpg';
        $filename = bin2hex(random_bytes(16)) . $extension;
        $stagingName = bin2hex(random_bytes(16)) . $extension;
        $stagingPath = $this->stagingDirectory . $stagingName;
        $preparedPath = $serverless
            ? $this->stagingDirectory . 'prepared-' . $filename
            : $this->publicDirectory . $filename;

        try {
            $file->move($this->stagingDirectory, $stagingName);
            $image = service('image');
            $image->withFile($stagingPath)->fit($this->width, $this->height, 'center');

            if (! $image->save($preparedPath)) {
                throw new RuntimeException('The image could not be prepared.');
            }

            if ($serverless) {
                $contents = file_get_contents($preparedPath);
                if ($contents === false) {
                    throw new RuntimeException('The prepared image could not be read.');
                }

                $mime = $extension === '.png' ? 'image/png' : 'image/jpeg';
                return 'data:' . $mime . ';base64,' . base64_encode($contents);
            }

            return $filename;
        } catch (Throwable $exception) {
            if (is_file($preparedPath)) {
                unlink($preparedPath);
            }

            throw $exception;
        } finally {
            if (is_file($stagingPath)) {
                unlink($stagingPath);
            }
            if ($serverless && is_file($preparedPath)) {
                unlink($preparedPath);
            }
        }
    }

    public function remove(?string $filename): void
    {
        if ($filename !== null && str_starts_with($filename, 'data:image/')) {
            return;
        }

        if ($filename === null || preg_match('/\A[a-f0-9]{32}\.(?:jpg|png)\z/', $filename) !== 1) {
            return;
        }

        $path = $this->publicDirectory . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }
}
