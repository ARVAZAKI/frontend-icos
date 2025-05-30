@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Master Branch</h1>
            <p class="text-muted mb-0">Data Cabang ICOS</p>
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
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="searchBranch" placeholder="Cari nama branch atau alamat...">
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
            </select>
        </div>
    </div>
<button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#addBranchModal">
  Tambah Cabang
</button>
    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building me-2"></i>Data Branch
                </h6>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary">Total: <span id="totalBranches">{{ isset($data) ? count($data) : 0 }}</span></span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
    @if(isset($data) && count($data) > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="branchTable">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="text-center" style="width: 80px;">
                            <i class="fas fa-hashtag"></i> No
                        </th>
                        <th scope="col">
                            <i class="fas fa-building me-1"></i>Nama Branch
                        </th>
                        <th scope="col">
                            <i class="fas fa-map-marker-alt me-1"></i>Alamat
                        </th>
                        <th scope="col" class="text-center" style="width: 120px;">
                            <i class="fas fa-info-circle me-1"></i>Status
                        </th>
                        <th scope="col" class="text-center" style="width: 120px;">
                            Opsi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $branch)
                        <tr data-branch-id="{{ $branch['id'] }}">
                            <td class="text-center fw-bold">
                                <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $branch['branchName'] }}</h6>
                                        <small class="text-muted">Branch {{ str_pad($branch['id'], 3, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                {{ $branch['address'] }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Aktif
                                </span>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-warning btn-sm edit-branch-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal"
                                        data-branch-id="{{ $branch['id'] }}"
                                        data-branch-name="{{ $branch['branchName'] }}"
                                        data-branch-address="{{ $branch['address'] }}">
                                    <i class="fas fa-edit"></i> 
                                </button>
                                <span class="badge bg-danger">
                                    <form action="{{ route('branch.destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus cabang ini?')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $branch['id'] }}">
                                        <button type="submit" class="btn btn-sm text-white p-0 border-0" style="background: transparent;">
                                            <i class="fas fa-trash"></i> 
                                        </button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-building fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum Ada Data Branch</h4>
                <p class="text-muted">Tidak ada data branch yang tersedia</p>
            </div>
        </div>
    @endif
</div>
    </div>
</div>

<!-- Modal Edit Branch - DIPINDAHKAN KE LUAR CONTAINER -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Branch</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBranchForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editBranchName" class="form-label">Nama Branch</label>
                        <input type="text" class="form-control" id="editBranchName" name="branchName" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editBranchAddress" class="form-label">Alamat</label>
                        <textarea class="form-control" id="editBranchAddress" name="address" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Branch -->
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addBranchModalLabel">Tambah Branch Baru</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{route('branch.store')}}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="branchName" class="form-label">Nama Cabang</label>
                <input type="text" class="form-control" name="branchName" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" name="address" rows="3" required></textarea>
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
    console.log('DOM loaded'); // Debug log
    
    // Handle edit button clicks dengan event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-branch-btn')) {
            e.preventDefault();
            const button = e.target.closest('.edit-branch-btn');
            
            console.log('Edit button clicked'); // Debug log
            
            // Get data from button attributes
            const branchId = button.getAttribute('data-branch-id');
            const branchName = button.getAttribute('data-branch-name');
            const branchAddress = button.getAttribute('data-branch-address');
            
            console.log('Branch data:', {branchId, branchName, branchAddress}); // Debug log
            
            // Update form action dengan route yang benar
            const form = document.getElementById('editBranchForm');
            form.action = `{{ url('branch') }}/${branchId}`;
            
            // Populate modal fields
            document.getElementById('editBranchName').value = branchName;
            document.getElementById('editBranchAddress').value = branchAddress;
            
            // Update modal title
            document.getElementById('editModalLabel').textContent = `Edit Branch - ${branchName}`;
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchBranch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#branchTable tbody tr');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const branchName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const address = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (branchName.includes(searchTerm) || address.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update visible count
            const totalElement = document.getElementById('totalBranches');
            if (totalElement) {
                totalElement.textContent = visibleCount;
            }
        });
    }

    // Filter by status
    const filterSelect = document.getElementById('filterStatus');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#branchTable tbody tr');
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const statusBadge = row.querySelector('td:nth-child(4) .badge');
                const statusText = statusBadge ? statusBadge.textContent.toLowerCase() : '';
                
                if (filterValue === '' || statusText.includes(filterValue === 'active' ? 'aktif' : 'tidak aktif')) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update visible count
            const totalElement = document.getElementById('totalBranches');
            if (totalElement) {
                totalElement.textContent = visibleCount;
            }
        });
    }

    // Reset filters when page loads
    if (searchInput) searchInput.value = '';
    if (filterSelect) filterSelect.value = '';
});

// Refresh data
function refreshData() {
    location.reload();
}
</script>

@endsection