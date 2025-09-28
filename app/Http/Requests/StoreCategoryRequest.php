<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isActive' => 'boolean',

        ];
    }

    /**
 * Map frontend camelCase keys to database snake_case columns
 */
    public function toSnakeCase(): array
    {
    $data = $this->validated();

    return [
        'name'       => $data['name'] ?? null,
        'slug'       => $data['slug'] ?? null,
        'description'=> $data['description'] ?? null,
        'image'      => $data['image'] ?? null,
        'is_active'  => $data['isActive'] ?? ($data['is_active'] ?? true), // handle both frontend camelCase and DB snake_case
    ];
    }

}
