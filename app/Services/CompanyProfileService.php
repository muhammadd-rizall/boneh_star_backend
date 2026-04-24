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

        // 1. Handle logo removal or update
        if (isset($data['remove_logo']) && $data['remove_logo']) {
            if ($profile && $profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = null;
        } elseif (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            if ($profile && $profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $data['logo']->store('company', 'public');
        }

        // 2. Handle WhatsApp clearing
        if (isset($data['clear_whatsapp']) && $data['clear_whatsapp']) {
            $data['whatsapp'] = [];
        }

        // 3. Handle Social Media clearing and normalization
        if (isset($data['clear_social_media']) && $data['clear_social_media']) {
            $data['social_media'] = [];
        } elseif (isset($data['social_media']) && is_array($data['social_media'])) {
            $normalized = [];
            foreach ($data['social_media'] as $item) {
                if (isset($item['platform'], $item['url'])) {
                    $platform = strtolower(trim($item['platform']));
                    // Deduplicate by platform
                    $normalized[$platform] = [
                        'platform' => $platform,
                        'url' => trim($item['url']),
                    ];
                }
            }
            $data['social_media'] = array_values($normalized);
        }

        // Remove flags from $data to avoid column mismatch errors
        unset($data['remove_logo'], $data['clear_whatsapp'], $data['clear_social_media']);

        if ($profile) {
            return $this->repository->update($profile->id, $data);
        }

        return $this->repository->create($data);
    }

    public function deleteProfile()
    {
        $profile = $this->repository->getFirst();

        if ($profile) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }

            return $this->repository->delete($profile->id);
        }

        return false;
    }
}
