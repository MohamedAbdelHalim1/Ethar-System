<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class BrandController extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $availableLocations = Location::with(['brands'])->get();
        $durations = [
            'day' => '1 Day',
            '2days' => '2 Days',
            '3days' => '3 Days',
            'week' => '1 Week',
            '15days' => '15 Days',
            'month' => '1 Month',
            '2months' => '2 Months',
            '3months' => '3 Months',
        ];

        return view('brands.create', compact('categories', 'availableLocations', 'durations'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'subscription_duration' => 'required|string',
                'location_id' => 'required|exists:locations,id'
            ]);

            $durations = [
                'day' => 1,
                '2days' => 2,
                '3days' => 3,
                'week' => 7,
                '15days' => 15,
                'month' => 30,
                '2months' => 60,
                '3months' => 90,
            ];

            $range = explode(' to ', $request->date_range);

            // تأكد من أن النتيجة فيها قيمتين فعلاً
            if (count($range) !== 2) {
                throw new \Exception("Invalid date range: " . $request->date_range);
            }

            $startDate = Carbon::parse($range[0]);
            $endDate = Carbon::parse($range[1]);

            $brand = Brand::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'subscription_duration' => $request->subscription_duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'drive_link' => $request->drive_link,
            ]);

            $conflict = \DB::table('brand_location')
                ->where('location_id', $request->location_id)
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q2) use ($startDate, $endDate) {
                            $q2->where('start_date', '<', $startDate)
                                ->where('end_date', '>', $endDate);
                        });
                })
                ->exists();

            if ($conflict) {
                return back()->withErrors(['location_id' => 'This location is already booked in that date range.']);
            }

            $brand->locations()->attach($request->location_id, [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);


            return redirect()->route('dashboard')->with('success', 'Brand added successfully.');
        } catch (Throwable $e) {
            // تطبع الخطأ للتصحيح
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }

    public function edit(Brand $brand)
    {
        $categories = Category::all();
        $availableLocations = Location::whereNull('brand_id')->orWhere('brand_id', $brand->id)->get();

        $durations = [
            'day' => '1 Day',
            '2days' => '2 Days',
            '3days' => '3 Days',
            'week' => '1 Week',
            '15days' => '15 Days',
            'month' => '1 Month',
            '2months' => '2 Months',
            '3months' => '3 Months',
        ];




        return view('brands.edit', compact('brand', 'categories', 'availableLocations', 'durations'));
    }



    public function update(Request $request, Brand $brand)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'subscription_duration' => 'required|string',
                'location_id' => 'required|exists:locations,id'
            ]);

            $range = explode(' to ', $request->date_range);

            if (count($range) !== 2) {
                throw new \Exception("Invalid date range: " . $request->date_range);
            }

            $startDate = \Carbon\Carbon::parse($range[0]);
            $endDate = \Carbon\Carbon::parse($range[1]);

            $brand->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'subscription_duration' => $request->subscription_duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'drive_link' => $request->drive_link,
            ]);

            // Free previous location
            \App\Models\Location::where('brand_id', $brand->id)->update(['brand_id' => null]);

            $location = \App\Models\Location::findOrFail($request->location_id);
            $location->brand_id = $brand->id;
            $location->save();

            return redirect()->route('dashboard')->with('success', 'Brand updated successfully.');
        } catch (\Throwable $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }
    }


    public function destroy(Brand $brand)
    {
        // إلغاء حجز اللوكيشن
        if ($brand->location) {
            $brand->location->brand_id = null;
            $brand->location->save();
        }

        $brand->delete();
        return redirect()->route('dashboard')->with('success', 'Brand deleted successfully.');
    }
}
