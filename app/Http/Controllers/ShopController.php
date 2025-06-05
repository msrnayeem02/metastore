<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // $dbName = \DB::connection()->getDatabaseName();
        // dd($dbName);
        $query = Product::where('status', 'active');

        // Apply category filter only if it's not empty
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply subcategory filter only if it's not empty
        if ($request->filled('subcategory')) {
            $query->where('subcategory_id', $request->subcategory);
        }

        $products = $query->paginate(12); // Paginate for better performance
        $categories = Category::with('subCategories')->get();

        return view('frontend.shop.shop', compact('products', 'categories'));
    }


    public function getSubcategories( $category)
    {
        try {
            $subcategories = SubCategory::where('category_id', $category->id)
                ->where('status', true)
                ->select('id', 'name')
                ->get();

            return response()->json($subcategories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching subcategories'], 500);
        }
    }

    //  Route::get('/{shop_name}/product/{id}', [ShopController::class, 'details'])->name('product.details');
    //make controller method for product details
    public function details( $id)
    {
        $product = Product::with(['category', 'subcategory'])
            ->where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4) // Limit to 4 related products
            ->get();

        return view('frontend.shop-detail.shopDetail', compact('product', 'relatedProducts'));
    }
}
