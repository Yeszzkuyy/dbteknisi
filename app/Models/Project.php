<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'customer_id',
        'company_id',
        'account_manager_id',
        'work_type_id',
        'pic_engineer_id',
        'project_name',
        'project_code',
        'quotation_number',
        'status',
        'start_date',
        'end_date',
        'description',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(AccountManager::class);
    }

    public function workType()
    {
        return $this->belongsTo(WorkType::class);
    }

    public function picEngineer()
    {
        return $this->belongsTo(
            User::class,
            'pic_engineer_id'
        );
    }

    public function supports()
    {
        return $this->hasMany(ProjectSupport::class);
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class);
    }
}