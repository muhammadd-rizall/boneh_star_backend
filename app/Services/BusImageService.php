<?php

namespace App\Services;

use App\Repositories\BusImageRepository;
use Illuminate\Support\Facades\Storage;

class BusImageService
{
    protected BusImageRepository $busImageRepository;

    public function __construct(BusImageRepository $busImageRepository)
    {
        $this->busImageRepository = $busImageRepository;
    }

    /**
     * Get all images for a bus.
     */
    public function getImagesByBus(string $busId)
    {
        return $this->busImageRepository->getByBusId($busId);
    }

    /**
     * Upload and create new images for a bus.
     */
    public function uploadImages(string $busId, array $images)
    {
        $uploadedImages = [];

        foreach ($images as $index => $imageFile) {
            $path = $imageFile->store('buses', 'public');

            $uploadedImages[] = $this->busImageRepository->create([
                'bus_id' => $busId,
                'image_path' => $path,
                'is_primary' => false,
                'sort_order' => $index,
            ]);
        }

        return $uploadedImages;
    }

    /**
     * Update image metadata.
     */
    public function updateImage(string $id, array $data)
    {
        return $this->busImageRepository->update($id, $data);
    }

    /**
     * Delete an image and its file.
     */
    public function deleteImage(string $id)
    {
        $image = $this->busImageRepository->find($id);

        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }

        return $this->busImageRepository->delete($id);
    }

    /**
     * Set image as primary.
     */
    public function setPrimary(string $id)
    {
        return $this->busImageRepository->setPrimary($id);
    }
}
