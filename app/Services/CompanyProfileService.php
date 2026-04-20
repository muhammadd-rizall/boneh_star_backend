<?php

namespace App\Services;

use App\Repositories\CompanyProfileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyProfileService
{
    protected CompanyProfileRepository $repository;

    public function __construct(CompanyProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProfile()
    {
        return $this->repository->getFirst();
    }

    public function updateOrCreateProfile(array $data)
    {
        $profile = $this->repository->getFirst();

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            if ($profile && $profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $data['logo']->store('company', 'public');
        }

        if ($profile) {
            return $this->repository->update($profile->id, $data);
        }

        return $this->repository->create($data);
    }
}
