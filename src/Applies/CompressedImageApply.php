<?php

declare(strict_types=1);

namespace Chocoway\MoonshineCompressedImage\Applies;

use Closure;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use MoonShine\Laravel\Applies\Fields\FileModelApply;
use MoonShine\UI\Fields\File;
use Chocoway\MoonshineCompressedImage\Fields\CompressedImage;

final class CompressedImageApply extends FileModelApply
{
    public function store(File $field, UploadedFile $file): string
    {
        $path = parent::store($field, $file);

        if (! $field instanceof CompressedImage) {
            return $path;
        }

        $storage = $field->getCore()->getStorage(disk: $field->getDisk());
        $fullPath = storage_path('app/public/' . $path);

        if (! file_exists($fullPath)) {
            return $path;
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($fullPath);

        if ($field->getCompressWidth() || $field->getCompressHeight()) {
            if ($field->isKeepAspectRatio()) {
                $image->scaleDown(
                    width: $field->getCompressWidth(),
                    height: $field->getCompressHeight()
                );
            } else {
                $image->resize(
                    width: $field->getCompressWidth() ?? $image->width(),
                    height: $field->getCompressHeight() ?? $image->height()
                );
            }
        }

        $format = $field->getCompressFormat();
        $newFullPath = preg_replace('/\.[^.]+$/', '.' . $format, $fullPath);
        $image->toFile($newFullPath, quality: $field->getCompressQuality());

        if ($newFullPath !== $fullPath) {
            unlink($fullPath);
            $path = preg_replace('/\.[^.]+$/', '.' . $format, $path);
        }

        return $path;
    }
}
