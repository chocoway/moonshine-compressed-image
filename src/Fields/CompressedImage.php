<?php

declare(strict_types=1);

namespace Chocoway\MoonshineCompressedImage\Fields;

use Closure;
use MoonShine\UI\Fields\Image;

class CompressedImage extends Image
{
    protected ?int $compressWidth = null;
    protected ?int $compressHeight = null;
    protected bool $keepAspectRatio = false;
    protected string $compressFormat = 'jpg';
    protected int $compressQuality = 80;
    protected ?Closure $modifier = null;

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

    public function modify(Closure $callback): static
    {
        $this->modifier = $callback;
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

    public function getModifier(): ?Closure
    {
        return $this->modifier;
    }
}