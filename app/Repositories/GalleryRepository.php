<?php

namespace App\Repositories;

use App\Models\Gallery;

class GalleryRepository
{
    public function __construct(protected Gallery $model)
    {}

    public function getAllPaginated(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->query();
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getPublishedPaginated(int $perPage = 10)
    {
        return $this->model->where('is_publish', true)->latest('date')->paginate($perPage);
    }

    public function find(string $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data)
    {
        $gallery = $this->find($id);
        $gallery->update($data);
        return $gallery;
    }

    public function delete(string $id)
    {
        $gallery = $this->find($id);
        return $gallery->delete();
    }
}
