<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;


    public static $rules = [
        'name' => 'required|unique:service_categories,name'
    ];
    protected $fillable = [
        'name',
        'description'
    ];
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string'
    ];


    public function services(){
        return $this->hasMany(Item::class,'item_group_id');
    }

}
