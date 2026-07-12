<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements EloquentRepositoryInterface
{
    protected Model $model;


    //get all data with relations
    public function all(array $relations = [])
    {
        return $this->model->with($relations)->get();
    }


    //get all trashed data with relations
    public function allTrashed(array $relations = [])
    {
        return $this->model->onlyTrashed()->with($relations)->get();
    }


    //find data by id with relations
    public function find($id, array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id);
    }


    //find trashed data by id with relations
    public function findTrashed($id, array $relations = [])
    {
        return $this->model->onlyTrashed()->with($relations)->findOrFail($id);
    }

    //find data by id, including trashed, with relations
    public function findWithTrashed($id, array $relations = [])
    {
        if ($this->hasSoftDeletes()) {
            return $this->model->withTrashed()->with($relations)->find($id);
        }

        return $this->find($id, $relations);
    }

    //create data
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    //update data by id
    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    //delete data by id
    public function delete($id)
    {
        if ($this->hasSoftDeletes()) {
            $record = $this->model->withTrashed()->findOrFail($id);
            if (!$record->trashed()) {
                $record->delete();
            }
            return $record;
        }

        $record = $this->find($id);
        $record->delete();
        return $record;
    }

    //get deleted data
    public function getDelete()
    {
        return $this->model->onlyTrashed()->whereNotNull('deleted_at')->get();
    }

    //restore data by id
    public function restore($id)
    {
        if ($this->hasSoftDeletes()) {
            $record = $this->model->withTrashed()->findOrFail($id);
            if ($record->trashed()) {
                $record->restore();
            }
            return $record;
        }

        throw new \Exception("Model does not support soft deletes.");
    }

    //force delete data by id
    public function forceDelete($id)
    {
        if ($this->hasSoftDeletes()) {
            $record = $this->model->withTrashed()->find($id);
            if ($record) {
                $record->forceDelete();
            }
            return null;
        }

        $record = $this->model->find($id);
        if ($record) {
            $record->delete();
        }

        return null;
    }

    /**
     * Check if the model uses SoftDeletes trait.
     */
    protected function hasSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($this->model));
    }
}
