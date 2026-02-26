@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Product Search</h4>
                    <a href="{{ url('/') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('products.search') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="q" class="form-label">Keyword</label>
                            <input type="text" name="q" id="q" class="form-control" placeholder="Search by name, code, or description" value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">All</option>
                                <option value="Product" {{ ($filters['category'] ?? '') === 'Product' ? 'selected' : '' }}>Product</option>
                                <option value="Service" {{ ($filters['category'] ?? '') === 'Service' ? 'selected' : '' }}>Service</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" name="brand" id="brand" class="form-control" value="{{ $filters['brand'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" name="type" id="type" class="form-control" value="{{ $filters['type'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price Range</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="price_min" class="form-control" placeholder="Min" value="{{ $filters['price_min'] ?? '' }}">
                                <span class="input-group-text">-</span>
                                <input type="number" step="0.01" name="price_max" class="form-control" placeholder="Max" value="{{ $filters['price_max'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('products.search') }}" class="btn btn-outline-secondary me-2">Clear</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Results</h5>
                        <span class="badge bg-dark">{{ $products->total() }} found</span>
                    </div>

                    <div class="row g-4">
                        @forelse($products as $product)
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100 border-0 shadow-sm hover-lift">
                                    <div class="position-relative">
                                        @php
                                            $primaryImage = $product->primaryImage;
                                        @endphp
                                        @if($primaryImage)
                                            <img src="{{ asset('storage/' . $primaryImage->photo_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                        <span class="position-absolute top-0 end-0 badge bg-{{ $product->active ? 'success' : 'secondary' }} m-2">
                                            {{ $product->active ? 'Available' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="text-muted small mb-1">{{ $product->category }} • {{ $product->brand ?? 'No brand' }}</p>
                                        <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($product->description, 80) }}</p>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="h5 text-dark mb-0">${{ number_format($product->unit_price, 2) }}</span>
                                                <span class="badge bg-dark">{{ $product->unit }}</span>
                                            </div>
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-dark btn-sm w-100">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5>No products found</h5>
                                <p class="text-muted">Try adjusting your search or filter criteria.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

