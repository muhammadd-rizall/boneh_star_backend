<?php

namespace App\Services;

use App\Repositories\DriverRepository;

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
    public function getPaginatedDrivers(int $perPage = 10)
    {
        return $this->driverRepository->getPaginatedDrivers($perPage);
    }

    // get driver by id
    public function getDriverById(string $id)
    {
        return $this->driverRepository->find($id);
    }

    // create driver
    public function createDriver(array $data)
    {
        return $this->driverRepository->create($data);
    }

    // update driver by id
    public function updateDriver(string $id, array $data)
    {
        return $this->driverRepository->update($id, $data);
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
        return $this->driverRepository->forceDelete($id);
    }

    // get driver statistics
    public function getStatistics(): array
    {
        return $this->driverRepository->getStatistics();
    }
}
