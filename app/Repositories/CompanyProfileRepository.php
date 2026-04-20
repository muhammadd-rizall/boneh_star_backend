<?php

namespace App\Repositories;

use App\Models\CompanyProfile;

class CompanyProfileRepository extends BaseRepository
{
    public function __construct(CompanyProfile $companyProfile)
    {
        $this->model = $companyProfile;
    }

    public function getFirst()
    {
        return $this->model->first();
    }
}
