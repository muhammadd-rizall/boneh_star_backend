<?php

namespace App\Services;

use App\Repositories\BusCategoryRepository;

class BusCategoryService
{
    protected BusCategoryRepository $busCategoryRepository;

    public function __construct(BusCategoryRepository $busCategoryRepository)
    {
        $this->busCategoryRepository = $busCategoryRepository;
    }

    // get all Categorys
    public function getAllCategory()
    {
        return $this->busCategoryRepository->getAllCategory();
    }

    // get paginated Categorys
    public function getPaginatedCategory(int $perPage = 10, ?string $search = null)
    {
        return $this->busCategoryRepository->getPaginatedCategory($perPage, $search);
    }

    // get Category by id
    public function getCategoryById(string $id)
    {
        return $this->busCategoryRepository->find($id);
    }

    // create Category
    public function createCategory(array $data)
    {
        return $this->busCategoryRepository->create($data);
    }

    // update Category by id
    public function updateCategory(string $id, array $data)
    {
        return $this->busCategoryRepository->update($id, $data);
    }

    // delete customer by id
    public function deleteCategory(string $id)
    {
        return $this->busCategoryRepository->delete($id);
    }

    // get all trashed Categorys
    public function getTrashedCategory()
    {
        return $this->busCategoryRepository->allTrashed();
    }

    // restore Category by id
    public function restoreCategory(string $id)
    {
        return $this->busCategoryRepository->restore($id);
    }

    // force delete Category by id
    public function forceDeleteCategory(string $id)
    {
        return $this->busCategoryRepository->forceDelete($id);
    }

    // get user statistics
    public function getStatistics(): array
    {
        return $this->busCategoryRepository->getStatistics();
    }
}
