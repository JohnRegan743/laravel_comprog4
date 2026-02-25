@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Product Details</h4>
                    <div>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Item Code</th>
                                    <td>{{ $product->item_code }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>
                                        <span class="badge badge-{{ $product->category == 'Product' ? 'primary' : 'info' }}">
                                            {{ $product->category }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit</th>
                                    <td>{{ $product->unit }}</td>
                                </tr>
                                <tr>
                                    <th>Unit Price</th>
                                    <td>${{ number_format($product->unit_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Brand</th>
                                    <td>{{ $product->brand ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ $product->type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $product->active ? 'success' : 'danger' }}">
                                            {{ $product->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $product->description ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h5>Product Photos</h5>
                            @if($product->imagePhotos->count() > 0)
                                <div id="productCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @php
                                            $imagePhotos = $product->imagePhotos;
                                            $primaryImage = $product->primaryImage;
                                            $orderedPhotos = collect();
                                            
                                            if($primaryImage) {
                                                $orderedPhotos->push($primaryImage);
                                                $remainingPhotos = $imagePhotos->where('id', '!=', $primaryImage->id);
                                                $orderedPhotos = $orderedPhotos->merge($remainingPhotos);
                                            } else {
                                                $orderedPhotos = $imagePhotos;
                                            }
                                        @endphp
                                        @foreach($orderedPhotos as $index => $photo)
                                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                     class="d-block w-100 rounded" alt="{{ $photo->photo_name }}"
                                                     style="height: 300px; object-fit: cover;">
                                                <div class="carousel-caption d-none d-md-block">
                                                    <h6>{{ $photo->photo_name }}</h6>
                                                    @if($product->primaryImage && $product->primaryImage->id == $photo->id)
                                                        <span class="badge bg-primary">Primary</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($orderedPhotos->count() > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Thumbnail Gallery with Admin Controls -->
                                @if($orderedPhotos->count() > 1)
                                <div class="row">
                                    @foreach($orderedPhotos as $index => $photo)
                                        <div class="col-4 mb-2 position-relative">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                 class="img-thumbnail clickable-thumbnail" 
                                                 alt="{{ $photo->photo_name }}"
                                                 data-bs-target="#productCarousel" 
                                                 data-bs-slide-to="{{ $index }}"
                                                 style="height: 60px; width: 100%; object-fit: cover; cursor: pointer;">
                                            
                                            @if(auth()->user()->isAdmin())
                                                @if($product->primaryImage && $product->primaryImage->id == $photo->id)
                                                    <span class="position-absolute top-0 start-0 badge bg-primary m-1">
                                                        <i class="fas fa-star"></i> Primary
                                                    </span>
                                                @else
                                                    <form action="{{ route('products.photos.set-primary', [$product, $photo]) }}" method="POST" class="position-absolute top-0 start-0 m-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" 
                                                                title="Set as primary image"
                                                                onclick="return confirm('Set this as the primary image?')">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if(auth()->user()->isAdmin() && $product->imagePhotos->count() > 1)
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-info-circle"></i> 
                                        As an admin, you can click the star button on any image to set it as the primary image that customers will see first.
                                    </div>
                                @endif
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No photos available.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <h5>Customer Reviews</h5>
                    @if($product->reviews->count() > 0)
                        <div class="row">
                            @foreach($product->reviews as $review)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="card-title">{{ $review->user->name }}</h6>
                                                    <div class="mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                        <span class="ms-2">({{ $review->rating }}/5)</span>
                                                    </div>
                                                    <p class="card-text">{{ $review->comment }}</p>
                                                </div>
                                                <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No reviews yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge-primary { background-color: #007bff; }
.badge-info { background-color: #17a2b8; }
.badge-success { background-color: #28a745; }
.badge-danger { background-color: #dc3545; }

.clickable-thumbnail:hover {
    opacity: 0.7;
    transform: scale(1.05);
    transition: all 0.3s ease;
}

.carousel-item img {
    border-radius: 8px;
}

.carousel-caption {
    background: rgba(0,0,0,0.5);
    border-radius: 4px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel
    const carousel = new bootstrap.Carousel(document.getElementById('productCarousel'), {
        interval: 5000,
        wrap: true
    });
});
</script>
@endpush
