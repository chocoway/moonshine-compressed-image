<?php

declare(strict_types=1);

namespace Chocoway\MoonshineCompressedImage\Applies;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Exceptions\FileFieldException;
use MoonShine\UI\Fields\File;
use Chocoway\MoonshineCompressedImage\Fields\CompressedImage;

final class CompressedImageApply implements ApplyContract
{
    /**
     * @param File $field
     */
    public function apply(FieldContract $field): Closure
    {
        return function (mixed $item) use ($field): mixed {
            /** @var Model $item */
            $requestValue = $field->getRequestValue();
            $remainingValues = $field->getRemainingValues();
            data_forget($item, $field->getHiddenRemainingValuesKey());
            $newValue = $field->isMultiple() ? $remainingValues : $remainingValues->first();

            if ($requestValue !== false) {
                if ($field->isMultiple()) {
                    $paths = [];
                    foreach ($requestValue as $file) {
                        $paths[] = $this->store($field, $file);
                    }
                    $newValue = $newValue->merge($paths)
                        ->values()
                        ->unique()
                        ->toArray();
                } else {
                    $newValue = $this->store($field, $requestValue);
                    $field->setRemainingValues([]);
                }
            }

            if ($newValue instanceof Collection) {
                $newValue = $newValue->toArray();
            }

            $field->removeExcludedFiles(
                $field->getCustomName() !== null || $field->isKeepOriginalFileName()
                    ? $newValue
                    : null,
            );

            return data_set($item, $field->getColumn(), $newValue);
        };
    }

    public function store(File $field, UploadedFile $file): string
    {
        $extension = $file->extension();

        if (! $field->isAllowedExtension($extension)) {
            throw FileFieldException::extensionNotAllowed($extension);
        }

        if ($field->isKeepOriginalFileName()) {
            $path = $file->storeAs(
                $field->getDir(),
                $file->getClientOriginalName(),
                $field->getOptions(),
            );
        } elseif (! \is_null($field->getCustomName())) {
            $path = $file->storeAs(
                $field->getDir(),
                \call_user_func($field->getCustomName(), $file, $field),
                $field->getOptions(),
            );
        } else {
            if (! $path = $file->store($field->getDir(), $field->getOptions())) {
                throw FileFieldException::failedSave();
            }
        }

        if (! $field instanceof CompressedImage) {
            return $path;
        }

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
EOF
