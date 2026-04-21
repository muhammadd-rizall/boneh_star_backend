<?php

namespace App\Services;

use App\Repositories\RouteTypeRepository;

class RouteTypeService
{
    protected RouteTypeRepository $routeTypeRepository;

    public function __construct(RouteTypeRepository $routeTypeRepository)
    {
        $this->routeTypeRepository = $routeTypeRepository;
    }

    // get all RouteTypes
    public function getAllRouteTypes()
    {
        return $this->routeTypeRepository->getAllRouteTypes();
    }

    // get paginated RouteTypes
    public function getPaginatedRouteTypes(int $perPage = 10, ?string $search = null)
    {
        return $this->routeTypeRepository->getPaginatedRouteTypes($perPage, $search);
    }

    // get RouteType by id
    public function getRouteTypeById(string $id)
    {
        return $this->routeTypeRepository->find($id);
    }

    // create RouteType
    public function createRouteType(array $data)
    {
        return $this->routeTypeRepository->create($data);
    }

    // update RouteType by id
    public function updateRouteType(string $id, array $data)
    {
        return $this->routeTypeRepository->update($id, $data);
    }

    // delete RouteType by id
    public function deleteRouteType(string $id)
    {
        return $this->routeTypeRepository->delete($id);
    }

    // get all trashed RouteTypes
    public function getTrashedRouteTypes()
    {
        return $this->routeTypeRepository->allTrashed();
    }

    // restore RouteType by id
    public function restoreRouteType(string $id)
    {
        return $this->routeTypeRepository->restore($id);
    }

    // force delete RouteType by id
    public function forceDeleteRouteType(string $id)
    {
        return $this->routeTypeRepository->forceDelete($id);
    }

    // get route type statistics
    public function getStatistics(): array
    {
        return $this->routeTypeRepository->getStatistics();
    }
}
