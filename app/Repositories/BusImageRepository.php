<?php

namespace App\Repositories;

use App\Models\BusImage;

class BusImageRepository extends BaseRepository
{
    public function __construct(BusImage $busImage)
    {
        $this->model = $busImage;
    }

    /**
     * Get images by bus ID.
     */
    public function getByBusId(string $busId)
    {
        return $this->model->where('bus_id', $busId)->orderBy('sort_order')->get();
    }

    /**
     * Set an image as primary and unset others for the same bus.
     */
    public function setPrimary(string $id)
    {
        $image = $this->find($id);

        $this->model->where('bus_id', $image->bus_id)
            ->where('id', '!=', $id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true]);

        return $image;
    }
}
