<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::with(['category', 'locations'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('dashboard', compact('brands'));
    }
}
