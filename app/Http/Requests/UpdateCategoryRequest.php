<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $this->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sortOrder' => 'sometimes|integer',
            'isActive' => 'sometimes|boolean',
            'metaData' => 'nullable|array',
        ];
    }

    public function toSnakeCase(): array
    {
    $data = $this->validated();

        return array_filter([
        'name'        => $data['name'] ?? null,
        'slug'        => $data['slug'] ?? null,
        'description' => $data['description'] ?? null,
        'is_active'   => $data['isActive'] ?? $data['is_active'] ?? null,
        'sort_order'  => $data['sortOrder'] ?? $data['sort_order'] ?? null,
        'meta_data'   => $data['metaData'] ?? $data['meta_data'] ?? null,
        ], fn($value) => $value !== null);

    }
}
