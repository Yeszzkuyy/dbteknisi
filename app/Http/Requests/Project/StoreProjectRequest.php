<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('create-projects');
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'work_type_id' => 'required|exists:work_types,id',
            'account_manager_id' => 'nullable|exists:account_managers,id',
            'pic_engineer' => 'required|string|max:255', // WAJIB text
            'support_technicians' => 'nullable|string|max:500', // OPSIONAL text
            'description' => 'nullable|string',
        ];
    }
}