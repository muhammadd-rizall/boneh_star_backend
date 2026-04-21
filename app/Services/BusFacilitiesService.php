<?php

namespace App\Services;

use App\Repositories\BusFacilitiesRepository;

class BusFacilitiesService
{
    protected BusFacilitiesRepository $busFacilitiesRepository;

    public function __construct(BusFacilitiesRepository $busFacilitiesRepository)
    {
        $this->busFacilitiesRepository = $busFacilitiesRepository;
    }

    // get all facilities
    public function getAllFacilities()
    {
        return $this->busFacilitiesRepository->getAllFacilities();
    }

    // get paginated Facilities
    public function getPaginatedFacilities(int $perPage = 10, ?string $search = null)
    {
        return $this->busFacilitiesRepository->getPaginatedFacilities($perPage, $search);
    }

    // get facility by id
    public function getFacilityById(string $id)
    {
        return $this->busFacilitiesRepository->find($id);
    }

    // create facility
    public function createFacility(array $data)
    {
        return $this->busFacilitiesRepository->create($data);
    }

    // update facility by id
    public function updateFacility(string $id, array $data)
    {
        return $this->busFacilitiesRepository->update($id, $data);
    }

    // delete facility by id
    public function deleteFacility(string $id)
    {
        return $this->busFacilitiesRepository->delete($id);
    }

    // get all trashed facilities
    public function getTrashedFacilities()
    {
        return $this->busFacilitiesRepository->allTrashed();
    }

    // restore facility by id
    public function restoreFacility(string $id)
    {
        return $this->busFacilitiesRepository->restore($id);
    }

    // force delete facility by id
    public function forceDeleteFacility(string $id)
    {
        return $this->busFacilitiesRepository->forceDelete($id);
    }

    // get statistics
    public function getStatistics(): array
    {
        return $this->busFacilitiesRepository->getStatistics();
    }
}
