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
            '1 Day' => '1 Day',
            '2 Days' => '2 Days',
            '3 Days' => '3 Days',
            '1 Week' => '1 Week',
            '15 Days' => '15 Days',
            '1 Month' => '1 Month',
            '2 Months' => '2 Months',
            '3 Months' => '3 Months',
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
                'location_id' => 'required|exists:locations,id',
            ]);

            $range = explode(' to ', $request->date_range);
            if (count($range) !== 2) {
                throw new \Exception("Invalid date range: " . $request->date_range);
            }

            $startDate = Carbon::parse($range[0]);
            $endDate = Carbon::parse($range[1]);

            return \DB::transaction(function () use ($request, $startDate, $endDate) {

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
                    // Throw exception to rollback transaction
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'location_id' => 'This location is already booked in that date range.',
                    ]);
                }

                $brand = Brand::create([
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'subscription_duration' => $request->subscription_duration,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'drive_link' => $request->drive_link,
                ]);

                $brand->locations()->attach($request->location_id, [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);

                return redirect()->route('dashboard')->with('success', 'Brand added successfully.');
            });
        } catch (Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function edit(Brand $brand)
    {
        $categories = Category::all();
        $availableLocations = Location::with(['brands'])->get();

        $durations = [
            '1 Day' => '1 Day',
            '2 Days' => '2 Days',
            '3 Days' => '3 Days',
            '1 Week' => '1 Week',
            '15 Days' => '15 Days',
            '1 Month' => '1 Month',
            '2 Months' => '2 Months',
            '3 Months' => '3 Months',
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
                'location_id' => 'required|exists:locations,id',
            ]);

            $range = explode(' to ', $request->date_range);
            if (count($range) !== 2) {
                throw new \Exception("Invalid date range: " . $request->date_range);
            }

            $startDate = \Carbon\Carbon::parse($range[0]);
            $endDate = \Carbon\Carbon::parse($range[1]);

            return \DB::transaction(function () use ($request, $brand, $startDate, $endDate) {

                $conflict = \DB::table('brand_location')
                    ->where('location_id', $request->location_id)
                    ->where('brand_id', '!=', $brand->id) // استثناء البراند الحالي
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
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'location_id' => 'This location is already booked in that date range.',
                    ]);
                }

                $brand->update([
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'subscription_duration' => $request->subscription_duration,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'drive_link' => $request->drive_link,
                ]);

                // Remove old pivot if any
                $brand->locations()->detach();

                // Add new location with updated pivot
                $brand->locations()->attach($request->location_id, [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);

                return redirect()->route('dashboard')->with('success', 'Brand updated successfully.');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
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
