<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $availability = $request->input('availability');

        $locations = Location::with(['brands'])->get();

        if ($from && $to && in_array($availability, ['available', 'not_available'])) {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);

            $locations = $locations->filter(function ($location) use ($fromDate, $toDate, $availability) {
                $hasBooking = $location->brands->contains(function ($brand) use ($fromDate, $toDate) {
                    $start = Carbon::parse($brand->pivot->start_date);
                    $end = Carbon::parse($brand->pivot->end_date);

                    return $start->lte($toDate) && $end->gte($fromDate);
                });

                return $availability === 'available' ? !$hasBooking : $hasBooking;
            });
        }

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
