<?php

namespace App\Services;

use App\Repositories\GalleryRepository;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    public function __construct(protected GalleryRepository $galleryRepository)
    {}

    public function getAllPaginated(int $perPage = 10, ?string $search = null)
    {
        return $this->galleryRepository->getAllPaginated($perPage, $search);
    }

    public function getPublishedPaginated(int $perPage = 12)
    {
        return $this->galleryRepository->getPublishedPaginated($perPage);
    }

    public function find(string $id)
    {
        return $this->galleryRepository->find($id);
    }

    public function create(array $data)
    {
        $images = [];
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {
                    $images[] = $image->store('galleries', 'public');
                }
            }
        }
        $data['images'] = $images;

        return $this->galleryRepository->create($data);
    }

    public function update(string $id, array $data)
    {
        $gallery = $this->galleryRepository->find($id);
        $currentImages = $gallery->images ?? [];

        // Handle deleted images
        if (isset($data['deleted_images']) && is_array($data['deleted_images'])) {
            foreach ($data['deleted_images'] as $delImage) {
                if (in_array($delImage, $currentImages)) {
                    Storage::disk('public')->delete($delImage);
                    $currentImages = array_filter($currentImages, fn($img) => $img !== $delImage);
                }
            }
            $currentImages = array_values($currentImages); // re-index
        }

        // Handle new images
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {
                    $currentImages[] = $image->store('galleries', 'public');
                }
            }
        }
        
        $data['images'] = $currentImages;

        return $this->galleryRepository->update($id, $data);
    }

    public function delete(string $id)
    {
        $gallery = $this->galleryRepository->find($id);
        if ($gallery->images && is_array($gallery->images)) {
            foreach ($gallery->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        return $this->galleryRepository->delete($id);
    }
}
