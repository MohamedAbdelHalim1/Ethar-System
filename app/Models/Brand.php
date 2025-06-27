<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'owner_phone',
        'type',
        'rent_value',
        'percentage_value',
        'sales_name',
        'status',
        'category_id',
        'subscription_duration',
        'start_date',
        'end_date',
        'drive_link',
        'contract_file',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'brand_location')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }
}
