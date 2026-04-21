<?php

namespace App\Services;

use App\Repositories\BusesRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BusesService
{
    protected BusesRepository $busesRepository;

    public function __construct(BusesRepository $busesRepository)
    {
        $this->busesRepository = $busesRepository;
    }

    // get all buses
    public function getAllBuses()
    {
        return $this->busesRepository->all(['category', 'images']);
    }

    // get paginated buses
    public function getPaginatedBuses(int $perPage = 10, ?string $search = null)
    {
        return $this->busesRepository->getPaginatedBuses($perPage, $search);
    }

    // get bus by id
    public function getBusById(string $id)
    {
        return $this->busesRepository->find($id, ['category', 'images']);
    }

    // create bus
    public function createBus(array $data)
    {
        return DB::transaction(function () use ($data) {
            $bus = $this->busesRepository->create($data);

            if (isset($data['facilities'])) {
                $bus->facilities()->sync($data['facilities']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $path = $image->store('buses', 'public');
                    $bus->images()->create([
                        'image_path' => $path,
                        'is_primary' => $index === 0, // Set first image as primary by default
                    ]);
                }
            }

            return $bus->load(['category', 'images', 'facilities']);
        });
    }

    // update bus by id
    public function updateBus(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $bus = $this->busesRepository->update($id, $data);

            if (isset($data['facilities'])) {
                $bus->facilities()->sync($data['facilities']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $image) {
                    $path = $image->store('buses', 'public');
                    $bus->images()->create([
                        'image_path' => $path,
                        'is_primary' => $index === 0 && $bus->images()->where('is_primary', true)->count() === 0,
                    ]);
                }
            }

            return $bus->load(['category', 'images', 'facilities']);
        });
    }

    // delete bus by id
    public function deleteBus(string $id)
    {
        return $this->busesRepository->delete($id);
    }

    // get all trashed buses
    public function getTrashedBuses()
    {
        return $this->busesRepository->allTrashed(['category', 'images']);
    }

    // restore bus by id
    public function restoreBus(string $id)
    {
        return $this->busesRepository->restore($id);
    }

    // force delete bus by id
    public function forceDeleteBus(string $id)
    {
        $bus = $this->busesRepository->findTrashed($id, ['images']);

        return DB::transaction(function () use ($bus) {
            $this->deleteBusImages($bus);

            return $this->busesRepository->forceDelete($bus->id);
        });
    }

    /**
     * Get statistics for buses.
     */
    public function getStatistics(): array
    {
        return $this->busesRepository->getStatistics();
    }

    /**
     * Delete all images associated with a bus.
     */
    protected function deleteBusImages($bus): void
    {
        foreach ($bus->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }
}
