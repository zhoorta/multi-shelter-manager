<?php

declare(strict_types=1);

namespace App\Actions;

/**
 * Scaled-down JPEG copy of an image, for lists and galleries. Needs the GD
 * extension; without it, or for a file it cannot read, it returns null and
 * the caller falls back to the original.
 */
class MakeImageThumbnail
{
    /**
     * @return string|null JPEG bytes, at most $maxSide pixels on the long side
     */
    public function handle(string $path, int $maxSide = 480): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $bytes = @file_get_contents($path);
        $source = $bytes === false ? false : @imagecreatefromstring($bytes);

        if ($source === false) {
            return null;
        }

        $source = $this->rotatedUpright($source, $path);
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxSide / max($width, $height));
        $thumbnail = imagecreatetruecolor(max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale)));

        imagefill($thumbnail, 0, 0, (int) imagecolorallocate($thumbnail, 255, 255, 255));
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, imagesx($thumbnail), imagesy($thumbnail), $width, $height);

        ob_start();
        imagejpeg($thumbnail, null, 80);

        return (string) ob_get_clean();
    }

    /**
     * Phones store the rotation in the EXIF data instead of turning the pixels.
     */
    private function rotatedUpright(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = @exif_read_data($path)['Orientation'] ?? 1;
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angle === 0 ? $image : (imagerotate($image, $angle, 0) ?: $image);
    }
}
