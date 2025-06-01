@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Master Product</h1>
            <p class="text-muted mb-0">Data Produk ICOS</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Branch Selection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Pilih Cabang</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('product.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="branch_id" class="form-label">Cabang</label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch['id'] }}" 
                                                {{ $selectedBranch == $branch['id'] ? 'selected' : '' }}>
                                            {{ $branch['branchName'] }} - {{ $branch['address'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($selectedBranch)
        <!-- Search and Add Button -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchProduct" placeholder="Cari nama produk...">
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-1"></i>Tambah Produk
                </button>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box me-2"></i>Data Produk
                    </h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">Total: <span id="totalProducts">{{ count($products) }}</span></span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($products) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="productTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 60px;">
                                        <i class="fas fa-hashtag"></i> No
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-box me-1"></i>Produk
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-tags me-1"></i>Kategori
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-dollar-sign me-1"></i>Harga
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-cubes me-1"></i>Stok
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-building me-1"></i>Cabang
                                    </th>
                                    <th scope="col" class="text-center" style="width: 150px;">
                                        Opsi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                    <tr data-product-id="{{ $product['id'] }}">
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if(!empty($product['imageUrl']))
                                                        <img src="{{ $product['imageUrl'] }}" 
                                                             alt="{{ $product['name'] }}" 
                                                             class="rounded" 
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar-sm bg-primary rounded d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-box text-white"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $product['name'] }}</h6>
                                                    <small class="text-muted">ID: {{ $product['id'] }}</small>
                                                    @if(!empty($product['description']))
                                                        <br><small class="text-muted">{{ Str::limit($product['description'], 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $currentCategory = collect($categories)->firstWhere('id', $product['categoryId']);
                                            @endphp
                                            <span class="badge bg-info">
                                                {{ $currentCategory['categoryName'] ?? 'Unknown Category' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                Rp {{ number_format($product['price'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($product['stock'] > 10)
                                                <span class="badge bg-success">{{ $product['stock'] }}</span>
                                            @elseif($product['stock'] > 0)
                                                <span class="badge bg-warning">{{ $product['stock'] }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $product['stock'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $currentBranch = collect($branches)->firstWhere('id', $product['branchId']);
                                            @endphp
                                            <i class="fas fa-building text-primary me-2"></i>
                                            {{ $currentBranch['branchName'] ?? 'Unknown Branch' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-info btn-sm view-product-btn" 
                                                        data-product-id="{{ $product['id'] }}"
                                                        title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-warning btn-sm edit-product-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editProductModal"
                                                        data-product-id="{{ $product['id'] }}"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm delete-product-btn" 
                                                        data-product-id="{{ $product['id'] }}"
                                                        data-product-name="{{ $product['name'] }}"
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-box fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Data Produk</h4>
                            <p class="text-muted">Tidak ada data produk untuk cabang yang dipilih</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                <h4 class="text-muted">Pilih Cabang Terlebih Dahulu</h4>
                <p class="text-muted">Silakan pilih cabang di atas untuk melihat data produk</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal Add Product -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addProductModalLabel">Tambah Produk Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="price" id="price" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" id="stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryId" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="categoryId" id="categoryId" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category['id'] }}">
                                            {{ $category['categoryName'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ImageFile" class="form-label">URL Gambar</label>
                        <input type="file" class="form-control" name="ImageFile" id="ImageFile">
                        <div class="form-text">Masukkan URL gambar produk (opsional)</div>
                    </div>
                    <input type="hidden" name="branch_id" value="{{ $selectedBranch }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editProductModalLabel">Edit Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="editProductForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_product_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_price" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="price" id="edit_price" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_stock" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" id="edit_stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_categoryId" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="categoryId" id="edit_categoryId" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category['id'] }}">
                                            {{ $category['categoryName'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ImageFile" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" name="ImageFile" id="edit_ImageFile">
                        <div class="form-text">Biarkan kosong jika tidak ingin mengubah gambar</div>
                        <div id="current_image_preview" class="mt-2" style="display: none;">
                            <label class="form-label">Gambar Saat Ini:</label>
                            <br>
                            <img id="current_image" src="" alt="Current Image" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                        </div>
                    </div>
                    <input type="hidden" name="branch_id" id="edit_branch_id" value="{{ $selectedBranch }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal View Product Detail -->
<div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="viewProductModalLabel">Detail Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productDetailContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 50px;
    height: 50px;
}

.table td {
    vertical-align: middle;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 2px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchProduct');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#productTable tbody tr');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const categoryName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const branchName = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || categoryName.includes(searchTerm) || branchName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update visible count
            const totalElement = document.getElementById('totalProducts');
            if (totalElement) {
                totalElement.textContent = visibleCount;
            }
        });
    }

    // View product detail
    const viewButtons = document.querySelectorAll('.view-product-btn');
    viewButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const productId = this.getAttribute('data-product-id');
            const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
            
            modal.show();
            
            try {
                const response = await fetch(`/product/${productId}`);
                const data = await response.json();
                
                if (response.ok) {
                    const product = data;
                    const category = @json($categories).find(c => c.id == product.categoryId);
                    const branch = @json($branches).find(b => b.id == product.branchId);
                    
                    document.getElementById('productDetailContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-4 text-center">
                                ${product.imageUrl ? 
                                    `<img src="${product.imageUrl}" alt="${product.name}" class="img-fluid rounded mb-3" style="max-height: 200px;">` :
                                    `<div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                        <i class="fas fa-box fa-3x text-muted"></i>
                                    </div>`
                                }
                            </div>
                            <div class="col-md-8">
                                <h4>${product.name}</h4>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID:</strong></td>
                                        <td>${product.id}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Harga:</strong></td>
                                        <td class="text-success fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stok:</strong></td>
                                        <td>
                                            <span class="badge ${product.stock > 10 ? 'bg-success' : product.stock > 0 ? 'bg-warning' : 'bg-danger'}">
                                                ${product.stock}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kategori:</strong></td>
                                        <td><span class="badge bg-info">${category ? category.categoryName : 'Unknown'}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cabang:</strong></td>
                                        <td>${branch ? branch.branchName : 'Unknown'}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge ${product.isActive ? 'bg-success' : 'bg-secondary'}">
                                                ${product.isActive ? 'Aktif' : 'Tidak Aktif'}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                ${product.description ? `
                                    <div class="mt-3">
                                        <strong>Deskripsi:</strong>
                                        <p class="mt-2">${product.description}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('productDetailContent').innerHTML = `
                        <div class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>${data.error || 'Gagal memuat detail produk'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('productDetailContent').innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>Terjadi kesalahan saat memuat detail produk</p>
                    </div>
                `;
            }
        });
    });

    // Refresh data function
    window.refreshData = function() {
        const selectedBranch = new URLSearchParams(window.location.search).get('branch_id');
        if (selectedBranch) {
            window.location.href = `{{ route('product.index') }}?branch_id=${selectedBranch}`;
        } else {
            window.location.reload();
        }
    };

    // Show alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Insert alert at the top of the container
        const container = document.querySelector('.container-fluid');
        const firstChild = container.firstElementChild;
        const alertDiv = document.createElement('div');
        alertDiv.innerHTML = alertHtml;
        container.insertBefore(alertDiv.firstElementChild, firstChild.nextSibling);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showAlert('error', 'Mohon lengkapi semua field yang wajib diisi');
            }
        });
    });

    // Real-time form validation
    const inputs = document.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Price formatting for display
    const priceInputs = document.querySelectorAll('input[name="price"]');
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Remove non-numeric characters except decimal point
            let value = this.value.replace(/[^\d.]/g, '');
            
            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            this.value = value;
        });
    });

    // Auto-focus first input when modal opens
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const firstInput = this.querySelector('input:not([type="hidden"]):not([readonly])');
            if (firstInput) {
                firstInput.focus();
            }
        });
    });

    // Clear form when add modal is closed
    const addModal = document.getElementById('addProductModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) {
                form.reset();
                // Remove validation classes
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
            }
        });
    }

    // Initialize tooltips if Bootstrap tooltips are available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    const deleteButtons = document.querySelectorAll('.delete-product-btn');
deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        const productName = this.getAttribute('data-product-name');
        
        // Show confirmation dialog
        if (confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
            // Create and submit delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/product/${productId}`;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Add branch_id to maintain current state
            const selectedBranch = new URLSearchParams(window.location.search).get('branch_id');
            if (selectedBranch) {
                const branchInput = document.createElement('input');
                branchInput.type = 'hidden';
                branchInput.name = 'branch_id';
                branchInput.value = selectedBranch;
                form.appendChild(branchInput);
            }
            
            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
        }
    });
});    

    // Add loading state to buttons during form submission
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.closest('form').addEventListener('submit', function() {
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            
            // Re-enable after 5 seconds as fallback
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 5000);
        });
    });
});

// Additional utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatNumber(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}
const editButtons = document.querySelectorAll('.edit-product-btn');
editButtons.forEach(button => {
    button.addEventListener('click', async function() {
        const productId = this.getAttribute('data-product-id');
        
        try {
            // Fetch product data
            const response = await fetch(`/product/${productId}`);
            const product = await response.json();
            
            if (response.ok) {
                // Populate form fields
                document.getElementById('edit_product_id').value = product.id;
                document.getElementById('edit_name').value = product.name;
                document.getElementById('edit_price').value = product.price;
                document.getElementById('edit_stock').value = product.stock;
                document.getElementById('edit_categoryId').value = product.categoryId;
                document.getElementById('edit_description').value = product.description || '';
                document.getElementById('edit_branch_id').value = product.branchId;
                
                // Set form action dengan product ID yang benar
                const form = document.getElementById('editProductForm');
                form.action = `{{ url('product') }}/${product.id}`;
                
                // Show current image if exists
                const currentImagePreview = document.getElementById('current_image_preview');
                const currentImage = document.getElementById('current_image');
                
                if (product.imageUrl) {
                    currentImage.src = product.imageUrl;
                    currentImagePreview.style.display = 'block';
                } else {
                    currentImagePreview.style.display = 'none';
                }
                
            } else {
                alert('Gagal memuat data produk: ' + (product.error || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error fetching product data:', error);
            alert('Terjadi kesalahan saat memuat data produk');
        }
    });
});

// Clear edit form when modal is closed
const editModal = document.getElementById('editProductModal');
if (editModal) {
    editModal.addEventListener('hidden.bs.modal', function() {
        const form = this.querySelector('form');
        if (form) {
            form.reset();
            // Remove validation classes
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            // Hide current image preview
            document.getElementById('current_image_preview').style.display = 'none';
            // Reset form action
            form.action = '';
        }
    });
}
// Export functions for potential external use
window.ProductManager = {
    refreshData: window.refreshData,
    formatCurrency: formatCurrency,
    formatNumber: formatNumber
};
</script>

@endsection