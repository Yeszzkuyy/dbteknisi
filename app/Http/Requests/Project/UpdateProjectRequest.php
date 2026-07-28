<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek apakah user punya permission 'edit-projects'
        return $this->user()->hasPermissionTo('edit-projects');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'project_name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'work_type_id' => 'required|exists:work_types,id',
            'account_manager_id' => 'nullable|exists:account_managers,id',
            'pic_engineer' => 'required|string|max:255',
            'support_technicians' => 'nullable|string|max:500',
            'status' => 'nullable|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
        ];
    }
}