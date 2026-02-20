<?php

namespace Chocoway\MoonshineCompressedImage\Fields;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use MoonShine\UI\Fields\Image;

class CompressedImage extends Image
{
    protected ?int $compressWidth = null;
    protected ?int $compressHeight = null;
    protected bool $keepAspectRatio = false;
    protected string $compressFormat = 'jpg';
    protected int $compressQuality = 80;

    public function width(int $width): static
    {
        $this->compressWidth = $width;
        return $this;
    }

    public function height(int $height): static
    {
        $this->compressHeight = $height;
        return $this;
    }

    public function aspectRatio(): static
    {
        $this->keepAspectRatio = true;
        return $this;
    }

    public function format(string $format): static
    {
        $this->compressFormat = $format;
        return $this;
    }

    public function quality(int $quality): static
    {
        $this->compressQuality = $quality;
        return $this;
    }

    protected function store(mixed $file): string
    {
        $path = parent::store($file);

        $manager = new ImageManager(new Driver());
        $fullPath = storage_path('app/public/' . $path);

        $image = $manager->read($fullPath);

        if ($this->compressWidth || $this->compressHeight) {
            if ($this->keepAspectRatio) {
                $image->scaleDown(
                    width: $this->compressWidth,
                    height: $this->compressHeight
                );
            } else {
                $image->resize(
                    width: $this->compressWidth ?? $image->width(),
                    height: $this->compressHeight ?? $image->height()
                );
            }
        }

        $newPath = preg_replace('/\.[^.]+$/', '.' . $this->compressFormat, $fullPath);
        $image->toFile($newPath, quality: $this->compressQuality);

        if ($newPath !== $fullPath) {
            unlink($fullPath);
            $path = preg_replace('/\.[^.]+$/', '.' . $this->compressFormat, $path);
        }

        return $path;
    }
}
