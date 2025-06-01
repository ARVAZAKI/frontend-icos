@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Master Category</h1>
            <p class="text-muted mb-0">Data Kategori ICOS</p>
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
                    <form method="GET" action="{{ route('category.index') }}">
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
                    <input type="text" class="form-control border-start-0" id="searchCategory" placeholder="Cari nama kategori...">
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
                    <i class="fas fa-plus me-1"></i>Tambah Kategori
                </button>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tags me-2"></i>Data Kategori
                    </h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">Total: <span id="totalCategories">{{ count($categories) }}</span></span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($categories) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="categoryTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 80px;">
                                        <i class="fas fa-hashtag"></i> No
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-tags me-1"></i>Nama Kategori
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-building me-1"></i>Cabang
                                    </th>
                                    <th scope="col" class="text-center" style="width: 120px;">
                                        Opsi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $index => $category)
                                    <tr data-category-id="{{ $category['id'] }}">
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-tags text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $category['categoryName'] }}</h6>
                                                    <small class="text-muted">ID: {{ $category['id'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $currentBranch = collect($branches)->firstWhere('id', $category['branchId']);
                                            @endphp
                                            <i class="fas fa-building text-primary me-2"></i>
                                            {{ $currentBranch['branchName'] ?? 'Unknown Branch' }}
                                            <br>
                                            <small class="text-muted">{{ $currentBranch['address'] ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm edit-category-btn me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    data-category-id="{{ $category['id'] }}"
                                                    data-category-name="{{ $category['categoryName'] }}"
                                                    data-branch-id="{{ $category['branchId'] }}">
                                                <i class="fas fa-edit"></i> 
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm delete-category-btn" 
                                                    data-category-id="{{ $category['id'] }}"
                                                    data-category-name="{{ $category['categoryName'] }}">
                                                <i class="fas fa-trash"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Data Kategori</h4>
                            <p class="text-muted">Tidak ada data kategori untuk cabang yang dipilih</p>
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
                <p class="text-muted">Silakan pilih cabang di atas untuk melihat data kategori</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal Add Kategori -->
<div class="modal fade" id="addKategoriModal" tabindex="-1" aria-labelledby="addKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addKategoriModalLabel">Tambah Kategori Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="categoryName" required>
                    </div>
                    <div class="mb-3">
                        <label for="branch_id_modal" class="form-label">Cabang</label>
                        <select name="branch_id" id="branch_id_modal" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch['id'] }}" 
                                        {{ $selectedBranch == $branch['id'] ? 'selected' : '' }}>
                                    {{ $branch['branchName'] }} - {{ $branch['address'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Kategori -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">Hapus Kategori</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" id="delete_category_id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kategori <strong id="delete_category_name"></strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Kategori</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_categoryName" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="categoryName" id="edit_categoryName" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_branch_id" class="form-label">Cabang</label>
                        <select name="branch_id" id="edit_branch_id" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch['id'] }}">
                                    {{ $branch['branchName'] }} - {{ $branch['address'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}

.table td {
    vertical-align: middle;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchCategory');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#categoryTable tbody tr');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const categoryName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const branchName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (categoryName.includes(searchTerm) || branchName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update visible count
            const totalElement = document.getElementById('totalCategories');
            if (totalElement) {
                totalElement.textContent = visibleCount;
            }
        });
    }

    // Edit button functionality
    const editButtons = document.querySelectorAll('.edit-category-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            const branchId = this.getAttribute('data-branch-id');
            
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_categoryName').value = categoryName;
            document.getElementById('edit_branch_id').value = branchId;
        });
    });

    // Delete button functionality
    const deleteButtons = document.querySelectorAll('.delete-category-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            
            document.getElementById('delete_category_id').value = categoryId;
            document.getElementById('delete_category_name').textContent = categoryName;
            
            // Show delete modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });

    // Auto-select branch in modal when adding
    const selectedBranch = '{{ $selectedBranch }}';
    if (selectedBranch) {
        document.getElementById('branch_id_modal').value = selectedBranch;
    }
});

// Refresh data
function refreshData() {
    location.reload();
}
</script>

@endsection