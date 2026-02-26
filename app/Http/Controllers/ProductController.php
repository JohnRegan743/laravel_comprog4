<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Laravel\Scout\Builder as ScoutBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ProductController extends Controller
{
    public function __construct()
    {
        // Require admin for all product management operations
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy', 'import', 'export', 'restore']);
    }

    public function index()
    {
        $products = Product::with('photos')->where('active', true)->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_code' => 'required|unique:products,item_code',
            'name' => 'required|string|max:255',
            'category' => 'required|in:Product,Service',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'active' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::create($request->except('photos'));

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('product_photos', 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'photo_path' => $path,
                    'photo_name' => $photo->getClientOriginalName(),
                    'order' => $index
                ]);
            }
        }

        // Clear any cached product data
        Cache::forget('products.active');
        Cache::forget('products.all');

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['photos', 'reviews.user']);

        $user = auth()->user();
        $canReview = false;
        $existingReview = null;

        if ($user) {
            $existingReview = $product->reviews()
                ->where('user_id', $user->id)
                ->first();

            $canReview = TransactionItem::where('product_id', $product->id)
                ->whereHas('transaction', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('status', 'completed');
                })
                ->exists();
        }

        return view('products.show', compact('product', 'canReview', 'existingReview'));
    }

    public function edit(Product $product)
    {
        $product->load('photos');
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'item_code' => 'required|unique:products,item_code,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'required|in:Product,Service',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'active' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product->update($request->except('photos'));

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('product_photos', 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'photo_path' => $path,
                    'photo_name' => $photo->getClientOriginalName(),
                    'order' => $index
                ]);
            }
        }

        // Clear any cached product data
        Cache::forget('products.active');
        Cache::forget('products.all');
        Cache::forget('product.' . $product->id);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        // Clear any cached product data
        Cache::forget('products.active');
        Cache::forget('products.all');
        Cache::forget('product.' . $product->id);
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        
        // Clear any cached product data
        Cache::forget('products.active');
        Cache::forget('products.all');
        Cache::forget('product.' . $product->id);
        
        return redirect()->route('products.index')
            ->with('success', 'Product restored successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        Excel::import(new ProductsImport, $request->file('excel_file'));

        // Clear all product-related caches
        Cache::forget('products.active');
        Cache::forget('products.all');
        
        return redirect()->route('products.index')
            ->with('success', 'Products imported successfully.');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function setPrimaryImage(Request $request, Product $product, ProductPhoto $photo)
    {
        // Verify the photo belongs to the product
        if ($photo->product_id !== $product->id) {
            return redirect()->back()
                ->with('error', 'This photo does not belong to this product.');
        }

        // Set the primary image
        $product->update(['primary_image_id' => $photo->id]);

        // Clear cache
        Cache::forget('products.active');
        Cache::forget('products.all');
        Cache::forget('product.' . $product->id);

        return redirect()->back()
            ->with('success', 'Primary image updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('q');
        $category = $request->input('category');
        $brand = $request->input('brand');
        $type = $request->input('type');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');

        if ($search) {
            $results = Product::search($search)->get();
        } else {
            $results = Product::where('active', true)->get();
        }

        $filtered = $results->filter(function (Product $product) use ($category, $brand, $type, $priceMin, $priceMax) {
            if (!$product->active) {
                return false;
            }

            if ($category && $product->category !== $category) {
                return false;
            }

            if ($brand && (! $product->brand || stripos($product->brand, $brand) === false)) {
                return false;
            }

            if ($type && (! $product->type || stripos($product->type, $type) === false)) {
                return false;
            }

            if ($priceMin !== null && $product->unit_price < (float) $priceMin) {
                return false;
            }

            if ($priceMax !== null && $product->unit_price > (float) $priceMax) {
                return false;
            }

            return true;
        })->values();

        $page = Paginator::resolveCurrentPage('page');
        $perPage = 12;

        $items = $filtered->forPage($page, $perPage)->values();

        $products = new LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('products.search', [
            'products' => $products,
            'search' => $search,
            'filters' => [
                'category' => $category,
                'brand' => $brand,
                'type' => $type,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
            ],
        ]);
    }
}
