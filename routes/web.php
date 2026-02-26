<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::middleware(['auth', 'verified', 'nocache'])->group(function () {
    Route::post('products/import', [App\Http\Controllers\ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [App\Http\Controllers\ProductController::class, 'export'])->name('products.export');
    Route::post('products/{id}/restore', [App\Http\Controllers\ProductController::class, 'restore'])->name('products.restore');

    // Reviews
    Route::post('products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('products.reviews.store');

    // User Management Routes
    Route::get('users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
    Route::post('users/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('users.profile.update');
    
    // Admin-only user routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);
        Route::get('users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');

        // Reviews management
        Route::get('reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Transactions management
        Route::get('transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create', [App\Http\Controllers\TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [App\Http\Controllers\TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
        Route::post('transactions/{transaction}/status', [App\Http\Controllers\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::post('transactions/{transaction}/resend-receipt', [App\Http\Controllers\TransactionController::class, 'resendReceipt'])->name('transactions.resend-receipt');
        
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

// Public search using Laravel Scout
Route::get('search/products', [App\Http\Controllers\ProductController::class, 'search'])->name('products.search');
