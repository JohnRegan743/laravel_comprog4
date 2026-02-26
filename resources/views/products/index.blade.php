@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Products & Services</h4>
                    @if(auth()->user()->isAdmin())
                        <div>
                            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-excel"></i> Import Excel
                            </button>
                            <a href="{{ route('products.export') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> Export Excel
                            </a>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Item Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Unit Price</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                    @if(auth()->user()->isAdmin())
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->primaryImage)
                                                <img src="{{ asset('storage/' . $product->primaryImage->photo_path) }}" 
                                                     alt="{{ $product->name }}" class="img-thumbnail" width="60" height="60">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $product->item_code }}</td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $product->category == 'Product' ? 'primary' : 'info' }}">
                                                {{ $product->category }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($product->unit_price, 2) }}</td>
                                        <td>{{ $product->brand ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $product->active ? 'success' : 'danger' }}">
                                                {{ $product->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('products.show', $product) }}" class="btn btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($product->trashed())
                                                        <form action="{{ route('products.restore', $product->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success" onclick="return confirm('Restore this product?')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this product?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdmin() ? '8' : '7' }}" class="text-center">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Products from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Excel File</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" required accept=".xlsx,.xls,.csv">
                        <div class="form-text">
                            Upload an Excel file with columns: Item Code, Name, Category, Unit, Unit Price, Description, Brand, Type, Active
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.badge-primary { background-color: #007bff; }
.badge-info { background-color: #17a2b8; }
.badge-success { background-color: #28a745; }
.badge-danger { background-color: #dc3545; }
.badge-secondary { background-color: #6c757d; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']]
    });
});
</script>
@endpush
