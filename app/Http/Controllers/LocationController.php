<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['brands'])->get();
        return view('locations.index', compact('locations'));
    }

    public function calendar(Location $location)
    {
        $bookings = $location->brands->map(function ($brand) {
            return [
                'title' => $brand->name,
                'start' => $brand->pivot->start_date,
                'end' => \Carbon\Carbon::parse($brand->pivot->end_date)->addDay()->toDateString(), // fullCalendar expects exclusive end
                'color' => 'red',
            ];
        });

        return view('locations.calendar', compact('location', 'bookings'));
    }
}
