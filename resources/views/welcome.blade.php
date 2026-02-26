@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<header class="bg-dark text-white py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h1 class="display-4 fw-bold mb-3">Welcome to OfficeOne</h1>
        <p class="lead opacity-75 mb-4">Professional inventory management system for modern businesses</p>
        <div class="d-flex gap-3 flex-wrap">
          @if(!auth()->check())
            <a href="{{ route('login') }}" class="btn btn-light">
              <i class="fas fa-sign-in-alt"></i> Login to Shop
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-light">
              <i class="fas fa-user-plus"></i> Register
            </a>
          @else
            <a href="{{ route('products.index') }}" class="btn btn-primary">
              <i class="fas fa-shopping-bag"></i> Browse Products
            </a>
          @endif
        </div>
      </div>
      <div class="col-lg-6 text-center">
        <i class="fas fa-shopping-cart fa-5x opacity-50"></i>
      </div>
    </div>
  </div>
</header>

<!-- Search and Filter Section -->
<section class="py-4 bg-light">
  <div class="container">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label mb-1">Category</label>
        <select id="categoryFilter" class="form-select">
          <option value="">All Categories</option>
          <option value="Product">Products</option>
          <option value="Service">Services</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label mb-1">Quick Search</label>
        <input type="text" id="searchInput" class="form-control" placeholder="Search products on this page...">
      </div>
      <div class="col-md-3">
        <label class="form-label mb-1">Sort</label>
        <select id="sortFilter" class="form-select">
          <option value="name">Sort by Name</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
        </select>
      </div>
      <div class="col-md-2 text-md-end">
        <label class="form-label mb-1 d-block">Advanced</label>
        <a href="{{ route('products.search') }}" class="btn btn-outline-dark w-100">
          <i class="fas fa-search"></i> Advanced Search
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Products Section -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">All Products</h2>
      <span id="productCount" class="badge bg-dark">Loading...</span>
    </div>
    
    <div id="productsGrid" class="row g-4">
      <!-- Products will be loaded here -->
    </div>
    
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5">
      <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3 text-muted">Loading products...</p>
    </div>
    
    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5 d-none">
      <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
      <h4>No products found</h4>
      <p class="text-muted">Try adjusting your search or filter criteria</p>
    </div>
  </div>
</section>

<!-- Product Detail Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductName">Product Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <img id="modalProductImage" src="" class="img-fluid rounded" alt="">
          </div>
          <div class="col-md-6">
            <h4 id="modalProductTitle"></h4>
            <p class="text-muted" id="modalProductCategory"></p>
            <p id="modalProductDescription"></p>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="h4 text-dark" id="modalProductPrice"></span>
              <span id="modalProductStatus" class="badge"></span>
            </div>
            <div class="mb-3">
              <label class="form-label">Quantity</label>
              <input type="number" id="modalQuantity" class="form-control" value="1" min="1">
            </div>
            <div class="mb-3">
              <label class="form-label">Total Price</label>
              <div class="h5 text-dark" id="modalTotalPrice">$0.00</div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        @if(auth()->check())
          <button type="button" class="btn btn-primary" id="addToCartBtn">
            <i class="fas fa-shopping-cart"></i> Add to Cart
          </button>
        @else
          <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('login') }}'">
            <i class="fas fa-sign-in-alt"></i> Login to Purchase
          </button>
        @endif
      </div>
    </div>
  </div>
</div>

<script>
let allProducts = [];
let filteredProducts = [];

// Load products from API
async function loadProducts() {
  try {
    const response = await fetch('/api/products');
    allProducts = await response.json();
    filteredProducts = [...allProducts];
    displayProducts(filteredProducts);
    updateProductCount();
  } catch (error) {
    console.error('Error loading products:', error);
    document.getElementById('loadingState').classList.add('d-none');
    document.getElementById('emptyState').classList.remove('d-none');
  }
}

// Display products in grid
function displayProducts(products) {
  const grid = document.getElementById('productsGrid');
  const loadingState = document.getElementById('loadingState');
  const emptyState = document.getElementById('emptyState');
  
  loadingState.classList.add('d-none');
  
  if (products.length === 0) {
    grid.innerHTML = '';
    emptyState.classList.remove('d-none');
    return;
  }
  
  emptyState.classList.add('d-none');
  
  grid.innerHTML = products.map(product => `
    <div class="col-sm-6 col-lg-4 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="position-relative">
          ${product.primary_image ? 
            `<img src="/storage/${product.primary_image}" class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">` :
            `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
              <i class="fas fa-image fa-2x text-muted"></i>
            </div>`
          }
          ${product.active ? 
            '<span class="position-absolute top-0 end-0 badge bg-success m-2">Available</span>' :
            '<span class="position-absolute top-0 end-0 badge bg-secondary m-2">Out of Stock</span>'
          }
        </div>
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">${product.name}</h5>
          <p class="text-muted small mb-2">${product.category}</p>
          <p class="card-text text-muted small">${product.description ? product.description.substring(0, 80) + '...' : 'No description available'}</p>
          <div class="mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="h5 text-dark mb-0">$${parseFloat(product.unit_price).toFixed(2)}</span>
              <span class="badge bg-dark">${product.unit}</span>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-outline-dark btn-sm" onclick="showProductDetails(${product.id})">
                <i class="fas fa-eye"></i> View Details
              </button>
              @if(auth()->check())
                <button class="btn btn-dark btn-sm" onclick="quickAddToCart(${product.id})">
                  <i class="fas fa-shopping-cart"></i> Quick Add
                </button>
              @else
                <button class="btn btn-dark btn-sm" onclick="window.location.href='{{ route('login') }}'">
                  <i class="fas fa-sign-in-alt"></i> Login to Buy
                </button>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}

// Show product details in modal
function showProductDetails(productId) {
  const product = allProducts.find(p => p.id === productId);
  if (!product) return;
  
  document.getElementById('modalProductName').textContent = product.name;
  document.getElementById('modalProductTitle').textContent = product.name;
  document.getElementById('modalProductCategory').textContent = product.category;
  document.getElementById('modalProductDescription').textContent = product.description || 'No description available';
  document.getElementById('modalProductPrice').textContent = `$${parseFloat(product.unit_price).toFixed(2)}`;
  document.getElementById('modalProductImage').src = product.primary_image ? `/storage/${product.primary_image}` : '/placeholder-image.jpg';
  
  const statusElement = document.getElementById('modalProductStatus');
  if (product.active) {
    statusElement.textContent = 'Available';
    statusElement.className = 'badge bg-success';
  } else {
    statusElement.textContent = 'Out of Stock';
    statusElement.className = 'badge bg-secondary';
  }
  
  updateModalPrice();
  new bootstrap.Modal(document.getElementById('productModal')).show();
}

// Update modal price based on quantity
function updateModalPrice() {
  const quantity = parseInt(document.getElementById('modalQuantity').value) || 1;
  const product = allProducts.find(p => p.name === document.getElementById('modalProductName').textContent);
  if (product) {
    const total = quantity * parseFloat(product.unit_price);
    document.getElementById('modalTotalPrice').textContent = `$${total.toFixed(2)}`;
  }
}

// Filter and search functions
function filterProducts() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const category = document.getElementById('categoryFilter').value;
  const sortBy = document.getElementById('sortFilter').value;
  
  filteredProducts = allProducts.filter(product => {
    const matchesSearch = !searchTerm || 
      product.name.toLowerCase().includes(searchTerm) ||
      (product.description && product.description.toLowerCase().includes(searchTerm));
    const matchesCategory = !category || product.category === category;
    return matchesSearch && matchesCategory;
  });
  
  // Sort products
  switch(sortBy) {
    case 'price-low':
      filteredProducts.sort((a, b) => parseFloat(a.unit_price) - parseFloat(b.unit_price));
      break;
    case 'price-high':
      filteredProducts.sort((a, b) => parseFloat(b.unit_price) - parseFloat(a.unit_price));
      break;
    default:
      filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
  }
  
  displayProducts(filteredProducts);
  updateProductCount();
}

function updateProductCount() {
  document.getElementById('productCount').textContent = `${filteredProducts.length} Products`;
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterProducts);
document.getElementById('categoryFilter').addEventListener('change', filterProducts);
document.getElementById('sortFilter').addEventListener('change', filterProducts);
document.getElementById('modalQuantity').addEventListener('input', updateModalPrice);

// Initialize
document.addEventListener('DOMContentLoaded', loadProducts);

// Placeholder functions for cart functionality
function quickAddToCart(productId) {
  // This would integrate with a cart system
  alert('Cart functionality would be implemented here');
}

document.getElementById('addToCartBtn').addEventListener('click', function() {
  // This would integrate with a cart system
  alert('Product added to cart!');
});
</script>
@endsection
