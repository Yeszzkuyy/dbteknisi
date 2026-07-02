<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- INI HARUS ADA DAN BENAR

class Project extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
    'customer_id',
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

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class)
            ->latest('activity_date');
    }
    public function tasks()
    {
        return $this->hasMany(
            ProjectTask::class
        );
    }
}