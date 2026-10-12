<?php

namespace App\Repositories\Site\DesignMe;

use App\Models\Design\DesignMeOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignMeOrderRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): DesignMeOrder
    {
        $sketchPath = $this->storeSketch($data['sketch_data'] ?? null);
        $referenceImages = $this->storeReferenceImages($data['reference_images'] ?? []);

        return DesignMeOrder::query()->create([
            'user_id' => Auth::id(),
            'furniture_type' => $data['furniture_type'],
            'material' => $data['material'] ?? null,
            'color' => $data['color'] ?? null,
            'color_name' => $data['color_name'] ?? null,
            'finish' => $data['finish'] ?? null,
            'dimensions' => [
                'width' => (int) ($data['width'] ?? 0),
                'depth' => (int) ($data['depth'] ?? 0),
                'height' => (int) ($data['height'] ?? 0),
            ],
            'features' => $data['features'] ?? [],
            'timeline' => $data['timeline'] ?? 'standard',
            'delivery_method' => $data['delivery_method'] ?? 'standard',
            'contact_name' => encrypt($data['contact_name']),
            'contact_email' => encrypt($data['contact_email']),
            'contact_phone' => encrypt($data['contact_phone']),
            'contact_address' => encrypt($data['contact_address'] ?? ''),
            'source' => $data['source'] ?? null,
            'notes' => $data['notes'] ?? null,
            'sketch_path' => $sketchPath,
            'reference_images' => $referenceImages !== [] ? $referenceImages : null,
            'estimated_price' => $data['estimated_price'] ?? null,
            'deposit_amount' => $data['deposit_amount'] ?? null,
            'status' => 'submitted',
        ]);
    }

    private function storeSketch(?string $dataUrl): ?string
    {
        if (! is_string($dataUrl) || ! str_starts_with($dataUrl, 'data:image/')) {
            return null;
        }

        if (! preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            return null;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $contents = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);

        if ($contents === false) {
            return null;
        }

        $path = 'design-me/sketches/'.Str::uuid().'.'.$extension;
        Storage::disk('local')->put($path, $contents);

        return $path;
    }

    /**
     * @param  array<int, string>  $images
     * @return array<int, string>
     */
    private function storeReferenceImages(array $images): array
    {
        $paths = [];

        foreach ($images as $dataUrl) {
            if (! is_string($dataUrl) || ! str_starts_with($dataUrl, 'data:image/')) {
                continue;
            }

            if (! preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
                continue;
            }

            $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
            $contents = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);

            if ($contents === false) {
                continue;
            }

            $path = 'design-me/references/'.Str::uuid().'.'.$extension;
            Storage::disk('local')->put($path, $contents);
            $paths[] = $path;
        }

        return $paths;
    }
}
