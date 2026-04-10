<?php

namespace App\Services;

use App\Repositories\BusesRepository;

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
        return $this->busesRepository->getAllBuses();
    }


    // get paginated buses
    public function getPaginatedBuses(int $perPage = 10)
    {
        return $this->busesRepository->getPaginatedBuses($perPage);
    }


    // get bus by id
    public function getBusById(string $id)
    {
        return $this->busesRepository->find($id);
    }


    // create bus
    public function createBus(array $data)
    {
        return $this->busesRepository->create($data);
    }


    // update bus by id
    public function updateBus(string $id, array $data)
    {
        return $this->busesRepository->update($id, $data);
    }


    // delete bus by id
    public function deleteBus(string $id)
    {
        return $this->busesRepository->delete($id);
    }


    // get all trashed buses
    public function getTrashedBuses()
    {
        return $this->busesRepository->allTrashed();
    }


    // restore bus by id
    public function restoreBus(string $id)
    {
        return $this->busesRepository->restore($id);
    }


    // force delete bus by id
    public function forceDeleteBus(string $id)
    {
        return $this->busesRepository->forceDelete($id);
    }
}
