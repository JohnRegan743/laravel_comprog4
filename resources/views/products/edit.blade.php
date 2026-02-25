@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Product/Service</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="item_code" class="form-label">Item Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('item_code') is-invalid @enderror" 
                                           id="item_code" name="item_code" value="{{ old('item_code', $product->item_code) }}" required>
                                    @error('item_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Product" {{ old('category', $product->category) == 'Product' ? 'selected' : '' }}>Product</option>
                                        <option value="Service" {{ old('category', $product->category) == 'Service' ? 'selected' : '' }}>Service</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                           id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" 
                                           id="unit_price" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                           id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                    @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                           id="type" name="type" value="{{ old('type', $product->type) }}">
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" 
                                       {{ old('active', $product->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="photos" class="form-label">Add New Photos</label>
                            <input type="file" class="form-control @error('photos') is-invalid @enderror" 
                                   id="photos" name="photos[]" multiple accept="image/*">
                            <div class="form-text">You can upload multiple photos (JPEG, PNG, JPG, GIF - Max 2MB each)</div>
                            @error('photos.*')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($product->photos->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">Current Photos</label>
                                <div class="row">
                                    @foreach($product->photos as $photo)
                                        <div class="col-md-3 mb-2 position-relative">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                 class="img-thumbnail" alt="{{ $photo->photo_name }}">
                                            <small class="d-block text-muted">{{ $photo->photo_name }}</small>
                                            
                                            @if($product->primaryImage && $product->primaryImage->id == $photo->id)
                                                <span class="position-absolute top-0 end-0 badge bg-primary m-1">
                                                    <i class="fas fa-star"></i> Primary
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="alert alert-info mt-2">
                                    <i class="fas fa-info-circle"></i> 
                                    To set a primary image, go to the product detail page and click the star button on your preferred image.
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
