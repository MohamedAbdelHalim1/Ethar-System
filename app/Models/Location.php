<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 
    ];


    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_location')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }
}
