<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        //get db name not from config or env
        // $dbName = \DB::connection()->getDatabaseName();
        // dd($dbName);
        $products = Product::where('status', 'active')->get();
        return view('frontend.home.index', compact('products'));
    }

}
