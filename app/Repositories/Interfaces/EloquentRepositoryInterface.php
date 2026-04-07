<?php

namespace App\Repositories\Interfaces;

interface EloquentRepositoryInterface
{
    public function all(array $relations = []);

    public function allTrashed(array $relations = []);

    public function find($id, array $relations = []);

    Public function findTrashed($id, array $relations = []);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getDelete();

    public function restore($id);

    public function forceDelete($id);

}
