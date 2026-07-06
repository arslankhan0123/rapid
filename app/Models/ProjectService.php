<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class ProjectService extends Model
{
    use HasFactory;
    protected $table = 'project_services'; // Define table name if it doesn't follow the default convention
    protected $fillable = ['project_id', 'ref_no', 'category_id', 'service_id', 'unit_price','item_group_id'];

    // Relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Relationship with Service
    // public function service()
    // {
    //     return $this->belongsTo(Service::class, 'service_id');
    // }
    public function service()
    {
        return $this->belongsTo(Item::class, 'category_id','item_group_id', );
    }
    public function categories()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
