<?php

namespace App\Services;

use App\Repositories\DriverRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DriverService
{
    protected DriverRepository $driverRepository;

    public function __construct(DriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    // get all drivers
    public function getAllDrivers()
    {
        return $this->driverRepository->getAllDrivers();
    }

    // get paginated drivers
    public function getPaginatedDrivers(int $perPage = 10, ?string $search = null)
    {
        return $this->driverRepository->getPaginatedDrivers($perPage, $search);
    }

    // get driver by id
    public function getDriverById(string $id)
    {
        return $this->driverRepository->find($id);
    }

    // create driver
    public function createDriver(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                $data['photo'] = $data['photo']->store('drivers', 'public');
            }

            return $this->driverRepository->create($data);
        });
    }

    // update driver by id
    public function updateDriver(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $driver = $this->driverRepository->find($id);

            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                // Delete old photo if exists
                if ($driver->photo) {
                    Storage::disk('public')->delete($driver->photo);
                }
                $data['photo'] = $data['photo']->store('drivers', 'public');
            }

            return $this->driverRepository->update($id, $data);
        });
    }

    // delete driver by id
    public function deleteDriver(string $id)
    {
        return $this->driverRepository->delete($id);
    }

    // get all trashed drivers
    public function getTrashedDrivers()
    {
        return $this->driverRepository->allTrashed();
    }

    // restore driver by id
    public function restoreDriver(string $id)
    {
        return $this->driverRepository->restore($id);
    }

    // force delete driver by id
    public function forceDeleteDriver(string $id)
    {
        $driver = $this->driverRepository->findTrashed($id);

        return DB::transaction(function () use ($driver) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }

            return $this->driverRepository->forceDelete($driver->id);
        });
    }

    // get driver statistics
    public function getStatistics(): array
    {
        return $this->driverRepository->getStatistics();
    }
}
