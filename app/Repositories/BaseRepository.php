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
        return $this->find($id)->delete();
    }

    //get deleted data
    public function getDelete()
    {
        return $this->model->onlyTrashed()->whereNotNull('deleted_at')->get();
    }

    //restore data by id
    public function restore($id)
    {
        $dataTrashed = $this->model->onlyTrashed()->findOrFail($id);

        if($dataTrashed  && $dataTrashed->trashed()) {
           $dataTrashed->restore();
           return $dataTrashed;
         }
    }

    //force delete data by id
    public function forceDelete($id)
    {
        $dataTrashed = $this->model->onlyTrashed()->findOrFail($id);

        if($dataTrashed) {
           $dataTrashed->forceDelete();
         }

         return null;
    }
}
