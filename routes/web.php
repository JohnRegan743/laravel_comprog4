<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('products/import', [App\Http\Controllers\ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [App\Http\Controllers\ProductController::class, 'export'])->name('products.export');
    Route::post('products/{id}/restore', [App\Http\Controllers\ProductController::class, 'restore'])->name('products.restore');

    // User Management Routes
    Route::get('users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
    Route::post('users/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('users.profile.update');
    
    // Admin-only user routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);
        Route::get('users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
        
        // Admin-only product routes
        Route::get('products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('products/{product}/photos/{photo}/set-primary', [App\Http\Controllers\ProductController::class, 'setPrimaryImage'])->name('products.photos.set-primary');
    });
});

Route::get('/api/products', function() {
    $products = App\Models\Product::with('primaryImage')
        ->where('active', true)
        ->get()
        ->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'description' => $product->description,
                'unit_price' => $product->unit_price,
                'unit' => $product->unit,
                'brand' => $product->brand,
                'type' => $product->type,
                'active' => $product->active,
                'primary_image' => $product->primaryImage ? $product->primaryImage->photo_path : null,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
        });
    
    return response()->json($products);
});

// Public product viewing routes
Route::middleware(['nocache'])->group(function () {
    Route::get('products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
});
