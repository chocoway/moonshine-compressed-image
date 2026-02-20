<?php

declare(strict_types=1);

namespace Chocoway\MoonshineCompressedImage\Providers;

use Chocoway\MoonshineCompressedImage\Applies\CompressedImageApply;
use Chocoway\MoonshineCompressedImage\Fields\CompressedImage;
use Illuminate\Support\ServiceProvider;

class CompressedImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CompressedImage::class,
            fn() => new CompressedImage(''),
        );
    }

    public function boot(): void
    {
        $this->app->resolving(CompressedImage::class, function (CompressedImage $field) {
            $field->apply(new CompressedImageApply());
        });
    }
}
