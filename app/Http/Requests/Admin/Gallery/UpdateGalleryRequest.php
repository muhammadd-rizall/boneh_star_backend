<?php

namespace App\Http\Requests\Admin\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('galleries.update'));
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'date' => ['sometimes', 'nullable', 'date'],
            'is_publish' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:5120'],
            'deleted_images' => ['nullable', 'array'],
            'deleted_images.*' => ['string']
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul galeri wajib diisi.',
            'title.max' => 'Judul galeri tidak boleh lebih dari 255 karakter.',
            'date.date' => 'Format tanggal tidak valid.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus berupa jpeg, png, jpg, gif, svg, atau webp.',
            'images.*.max' => 'Ukuran gambar tidak boleh melebihi 5MB.',
            'images.*.uploaded' => 'Foto gagal diunggah. Pastikan ukuran file tidak melebihi batas maksimal server.',
        ];
    }
}
