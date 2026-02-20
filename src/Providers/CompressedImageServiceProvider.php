<?php

declare(strict_types=1);

namespace Chocoway\MoonshineCompressedImage\Providers;

use Chocoway\MoonshineCompressedImage\Applies\CompressedImageApply;
use Chocoway\MoonshineCompressedImage\Fields\CompressedImage;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Laravel\Resources\ModelResource;

class CompressedImageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->callAfterResolving(AppliesRegisterContract::class, function (AppliesRegisterContract $appliesRegister) {
            $appliesRegister->for(ModelResource::class)->fields()->push([
                CompressedImage::class => CompressedImageApply::class,
            ]);
        });
    }
}