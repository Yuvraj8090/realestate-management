<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PropertyImageService
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    /**
     * @param  array<int, UploadedFile>  $uploadedFiles
     * @param  array<int, array<string, mixed>>  $manifest
     * @param  array<string, array<string, mixed>>  $cropPayloads
     */
    public function sync(Property $property, array $uploadedFiles, array $manifest, array $cropPayloads, ?string $primaryMediaKey): void
    {
        $existingImages = $property->images()->get()->keyBy('id');
        $keptExistingIds = collect($manifest)
            ->where('type', 'existing')
            ->pluck('id')
            ->filter()
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        $existingImages
            ->reject(fn (PropertyImage $image): bool => in_array($image->id, $keptExistingIds, true))
            ->each(fn (PropertyImage $image) => $this->deleteImageRecord($image));

        $newManifestItems = array_values(array_filter(
            $manifest,
            static fn (array $item): bool => ($item['type'] ?? null) === 'new',
        ));

        $newUploadsByKey = [];

        foreach ($newManifestItems as $index => $item) {
            if (! isset($uploadedFiles[$index])) {
                continue;
            }

            $newUploadsByKey[$item['key']] = $uploadedFiles[$index];
        }

        $sortOrder = 0;
        $primaryWasApplied = false;

        foreach ($manifest as $item) {
            if (($item['type'] ?? null) === 'existing' && isset($item['id'])) {
                /** @var PropertyImage|null $image */
                $image = $existingImages->get((int) $item['id']);

                if ($image === null) {
                    continue;
                }

                $isPrimary = $primaryMediaKey === ($item['key'] ?? null);
                $primaryWasApplied = $primaryWasApplied || $isPrimary;

                $image->update([
                    'sort_order' => $sortOrder,
                    'is_primary' => $isPrimary,
                ]);

                $sortOrder++;

                continue;
            }

            if (($item['type'] ?? null) !== 'new' || ! isset($item['key'], $newUploadsByKey[$item['key']])) {
                continue;
            }

            $isPrimary = $primaryMediaKey === $item['key'];
            $primaryWasApplied = $primaryWasApplied || $isPrimary;

            $this->storeUploadedImage(
                property: $property,
                file: $newUploadsByKey[$item['key']],
                cropData: $cropPayloads[$item['key']] ?? [],
                sortOrder: $sortOrder,
                isPrimary: $isPrimary,
            );

            $sortOrder++;
        }

        if (! $primaryWasApplied) {
            $property->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }
    }

    public function deleteImageRecord(PropertyImage $image): void
    {
        Storage::disk('public')->delete([$image->path, $image->thumbnail_path]);
        $image->delete();
    }

    /**
     * @param  array<string, mixed>  $cropData
     */
    private function storeUploadedImage(Property $property, UploadedFile $file, array $cropData, int $sortOrder, bool $isPrimary): void
    {
        $directory = "properties/{$property->id}";
        $basename = Str::uuid()->toString();

        $image = $this->imageManager->read($file->getPathname())->orient();
        $this->applyCrop($image, $cropData);

        if ($image->width() > 1800) {
            $image->scale(width: 1800);
        }

        $full = $image->toWebp(82);
        $thumbnail = $this->imageManager->read((string) $full)->cover(480, 320)->toWebp(76);

        $imagePath = "{$directory}/{$basename}.webp";
        $thumbnailPath = "{$directory}/thumb_{$basename}.webp";

        Storage::disk('public')->put($imagePath, (string) $full);
        Storage::disk('public')->put($thumbnailPath, (string) $thumbnail);

        $property->images()->create([
            'path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => 'image/webp',
            'file_size' => strlen((string) $full),
            'width' => $image->width(),
            'height' => $image->height(),
            'is_primary' => $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * @param  array<string, mixed>  $cropData
     */
    private function applyCrop(mixed $image, array $cropData): void
    {
        $width = max(0, (int) ($cropData['width'] ?? 0));
        $height = max(0, (int) ($cropData['height'] ?? 0));

        if ($width === 0 || $height === 0) {
            return;
        }

        $offsetX = max(0, (int) ($cropData['x'] ?? 0));
        $offsetY = max(0, (int) ($cropData['y'] ?? 0));
        $safeWidth = min($width, $image->width() - $offsetX);
        $safeHeight = min($height, $image->height() - $offsetY);

        if ($safeWidth < 50 || $safeHeight < 50) {
            return;
        }

        $image->crop($safeWidth, $safeHeight, $offsetX, $offsetY);
    }
}
