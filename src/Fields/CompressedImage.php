<?php

declare(strict_types=1);

namespace Chocoway\MoonshineCompressedImage\Fields;

use MoonShine\UI\Fields\Image;

class CompressedImage extends Image
{
    protected ?int $compressWidth = null;
    protected ?int $compressHeight = null;
    protected bool $keepAspectRatio = false;
    protected string $compressFormat = 'jpg';
    protected int $compressQuality = 80;
    protected ?int $thumbWidth = null;
    protected ?int $thumbHeight = null;

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

    public function thumb(int $width, int $height): static
    {
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;
        return $this;
    }

    public function getCompressWidth(): ?int
    {
        return $this->compressWidth;
    }

    public function getCompressHeight(): ?int
    {
        return $this->compressHeight;
    }

    public function isKeepAspectRatio(): bool
    {
        return $this->keepAspectRatio;
    }

    public function getCompressFormat(): string
    {
        return $this->compressFormat;
    }

    public function getCompressQuality(): int
    {
        return $this->compressQuality;
    }

    public function getThumbWidth(): ?int
    {
        return $this->thumbWidth;
    }

    public function getThumbHeight(): ?int
    {
        return $this->thumbHeight;
    }

    public function hasThumb(): bool
    {
        return $this->thumbWidth !== null || $this->thumbHeight !== null;
    }
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $data = parent::resolveAfterDestroy($data);

        if ($this->hasThumb() && !blank($this->toValue())) {
            $values = $this->isMultiple() ? $this->toValue() : [$this->toValue()];

            foreach ($values as $file) {
                $thumbFile = dirname($file) . '/thumb_' . basename($file);
                $this->deleteStorageFile($thumbFile);
            }
        }

        return $data;
    }
}