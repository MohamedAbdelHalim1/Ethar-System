<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::with(['category', 'locations']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $brands = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('brands'));
    }
}
