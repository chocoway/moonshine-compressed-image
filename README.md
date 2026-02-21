# MoonShine Compressed Image

A custom image field for [MoonShine](https://getmoonshine.app) admin panel with built-in image compression, resizing, thumbnail generation and format conversion powered by [Intervention Image v3](https://image.intervention.io/v3).

## Requirements

- PHP 8.2+
- Laravel 10|11|12
- MoonShine 4.x
- GD extension

## Installation

```bash
composer require chocoway/moonshine-compressed-image
```

## Usage

```php
use Chocoway\MoonshineCompressedImage\Fields\CompressedImage;

CompressedImage::make('Photo', 'photo')
    ->width(1000)
    ->height(800)
    ->aspectRatio()
    ->format('webp')
    ->quality(80)
    ->thumb(200, 200)
```

## Available Methods

| Method | Description |
|---|---|
| `->width(int $width)` | Set max width in pixels |
| `->height(int $height)` | Set max height in pixels |
| `->aspectRatio()` | Keep aspect ratio when resizing |
| `->format(string $format)` | Output format: `jpg`, `png`, `webp`, `gif` |
| `->quality(int $quality)` | Compression quality from 1 to 100 (default: 80) |
| `->thumb(int $width, int $height)` | Generate a thumbnail alongside the original |

## Examples

**Resize with aspect ratio (recommended):**
```php
CompressedImage::make('Photo', 'photo')
    ->width(1000)
    ->aspectRatio()
    ->format('webp')
    ->quality(80)
```

**With thumbnail:**
```php
CompressedImage::make('Photo', 'photo')
    ->width(1000)
    ->aspectRatio()
    ->format('webp')
    ->quality(80)
    ->thumb(200, 200)
```

Thumbnail is saved alongside the original with a `thumb_` prefix:
- Original: `images/photo.webp`
- Thumbnail: `images/thumb_photo.webp`

**Convert to WebP without resizing:**
```php
CompressedImage::make('Photo', 'photo')
    ->format('webp')
    ->quality(75)
```

**Strict resize to exact dimensions:**
```php
CompressedImage::make('Photo', 'photo')
    ->width(1000)
    ->height(500)
    ->format('jpg')
    ->quality(90)
```

## License

MIT