      @extends('layouts.app')

      @section('content')
      <div class="container-fluid">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div>
                  <h1 class="h3 mb-0 text-gray-800">Data Meja</h1>
                  <p class="text-muted mb-0">Data Meja ICOS</p>
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

      <!-- Branch Selection -->
      <div class="row mb-4">
            <div class="col-md-6">
                  <div class="card">
                  <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-building me-2"></i>Pilih Cabang</h6>
                  </div>
                  <div class="card-body">
                        <form method="GET" action="{{ route('table.index') }}">
                              <div class="row align-items-end">
                              <div class="col-md-8">
                                    <label for="branch_id" class="form-label">Cabang</label>
                                    <select name="branch_id" id="branch_id" class="form-select" required>
                                          <option value="">-- Pilih Cabang --</option>
                                          @if($branches->successful())
                                          @php
                                                $branchData = $branches->json();
                                                $branchList = isset($branchData['data']) ? $branchData['data'] : $branchData;
                                          @endphp
                                          @if(is_array($branchList))
                                                @foreach($branchList as $branch)
                                                      <option value="{{ $branch['id'] }}" 
                                                            {{ $selectedBranch == $branch['id'] ? 'selected' : '' }}>
                                                      @if(isset($branch['address']))
                                                            {{ $branch['branchName'] }} - {{ $branch['address'] }}
                                                      @else
                                                            {{ $branch['branchName'] }}
                                                      @endif
                                                      </option>
                                                @endforeach
                                          @endif
                                          @endif
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
            <!-- Search -->
            <div class="row mb-4">
                  <div class="col-md-8">
                  <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                              <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchTable" placeholder="Cari nomor meja, kapasitas, atau status...">
                  </div>
                  </div>
                  <div class="col-md-4">
                  <div class="d-flex gap-2">
                        <select class="form-select" id="statusFilter">
                              <option value="">Semua Status</option>
                              <option value="Available">Tersedia</option>
                              <option value="Occupied">Terisi</option>
                        </select>
                  </div>
                  </div>
            </div>
            
            <!-- Data Table Card -->
            <div class="card shadow mb-4">
                  <div class="card-header py-3 bg-light">
                  <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                              <i class="fas fa-table me-2"></i>Data Meja
                        </h6>
                        <div class="d-flex gap-2">
                              <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createTableModal">
                              <i class="fas fa-plus me-1"></i>Tambah Meja
                              </button>
                              <span class="badge bg-primary">Total: <span id="totalTables">{{ isset($filteredTables) ? count($filteredTables) : 0 }}</span></span>
                              <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                              <i class="fas fa-sync-alt"></i>
                              </button>
                        </div>
                  </div>
                  </div>
                  <div class="card-body p-0">
                  @if(isset($filteredTables) && count($filteredTables) > 0)
                        <div class="table-responsive">
                              <table class="table table-hover mb-0" id="tableTable">
                              <thead class="table-dark">
                                    <tr>
                                          <th scope="col" class="text-center" style="width: 60px;">
                                          <i class="fas fa-hashtag"></i> No
                                          </th>
                                          <th scope="col">
                                          <i class="fas fa-table me-1"></i>Nomor Meja
                                          </th>
                                          <th scope="col">
                                          <i class="fas fa-users me-1"></i>Kapasitas
                                          </th>
                                          <th scope="col">
                                          <i class="fas fa-align-left me-1"></i>Deskripsi
                                          </th>
                                          <th scope="col">
                                          <i class="fas fa-clock me-1"></i>Dibuat
                                          </th>
                                          <th scope="col" class="text-center">
                                          <i class="fas fa-eye me-1"></i>Aksi
                                          </th>
                                    </tr>
                              </thead>
                              <tbody>
                                    @foreach($filteredTables as $index => $table)
                                          <tr data-table-id="{{ $table['id'] }}">
                                          <td class="text-center fw-bold">
                                                <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                                          </td>
                                          <td>
                                                <div class="d-flex align-items-center">
                                                      <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                      <i class="fas fa-table text-white"></i>
                                                      </div>
                                                      <div>
                                                      <h6 class="mb-0 fw-bold">{{ $table['tableNumber'] }}</h6>
                                                      </div>
                                                </div>
                                          </td>
                                          <td>
                                                <span class="badge bg-info">{{ $table['capacity'] }} Orang</span>
                                          </td>
                                          <td>
                                                <span class="text-muted">{{ $table['description'] ?? '-' }}</span>
                                          </td>
                                          <td>
                                                <div>
                                                      <strong>{{ \Carbon\Carbon::parse($table['createdAt'])->format('d/m/Y') }}</strong>
                                                      <br>
                                                </div>
                                          </td>
                                          <td class="text-center">
                                          <div class="btn-group" role="group">
                                                <button type="button" 
                                                      class="btn btn-info btn-sm view-detail-btn" 
                                                      data-bs-toggle="modal" 
                                                      data-bs-target="#detailModal"
                                                      data-table='@json($table)'
                                                      title="Lihat Detail">
                                                      <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" 
                                                      class="btn btn-warning btn-sm edit-table-btn" 
                                                      data-bs-toggle="modal" 
                                                      data-bs-target="#editTableModal"
                                                      data-table='@json($table)'
                                                      title="Edit Meja">
                                                      <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                      class="btn btn-danger btn-sm delete-table-btn" 
                                                      data-table-id="{{ $table['id'] }}"
                                                      data-table-number="{{ $table['tableNumber'] }}"
                                                      title="Hapus Meja">
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
                              <i class="fas fa-table fa-4x text-muted mb-3"></i>
                              <h4 class="text-muted">Belum Ada Data Meja</h4>
                              <p class="text-muted">Tidak ada data meja untuk cabang yang dipilih</p>
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
                  <p class="text-muted">Silakan pilih cabang di atas untuk melihat data meja</p>
                  </div>
            </div>
      @endif
      </div>

      <!-- Modal Detail Table -->
      <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
                  <div class="modal-header">
                  <h1 class="modal-title fs-5" id="detailModalLabel">Detail Meja</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                  <div class="row mb-3">
                        <div class="col-md-6">
                              <strong>Nomor Meja:</strong>
                              <p id="detail_table_number" class="mb-1"></p>
                        </div>
                        <div class="col-md-6">
                              <strong>Kapasitas:</strong>
                              <p id="detail_capacity" class="mb-1"></p>
                        </div>
                  </div>
                  <div class="row mb-3">
                        <div class="col-md-6">
                              <strong>Status:</strong>
                              <p id="detail_status" class="mb-1"></p>
                        </div>
                        <div class="col-md-6">
                              <strong>Cabang:</strong>
                              <p id="detail_branch" class="mb-1"></p>
                        </div>
                  </div>
                  <div class="row mb-3">
                        <div class="col-md-12">
                              <strong>Deskripsi:</strong>
                              <p id="detail_description" class="mb-1"></p>
                        </div>
                  </div>
                  <div class="row mb-3">
                        <div class="col-md-6">
                              <strong>Dibuat:</strong>
                              <p id="detail_created_at" class="mb-1"></p>
                        </div>
                        <div class="col-md-6">
                              <strong>Diperbarui:</strong>
                              <p id="detail_updated_at" class="mb-1"></p>
                        </div>
                  </div>
                  </div>
                  <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  </div>
            </div>
      </div>
      </div>
<div class="modal fade" id="createTableModal" tabindex="-1" aria-labelledby="createTableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('table.store') }}" method="POST" id="createTableForm">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h1 class="modal-title fs-5" id="createTableModalLabel">
                        <i class="fas fa-plus me-2"></i>Tambah Meja Baru
                    </h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tableNumber" class="form-label">
                                <i class="fas fa-table me-1"></i>Nomor Meja <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('tableNumber') is-invalid @enderror" 
                                   id="tableNumber" 
                                   name="tableNumber" 
                                   value="{{ old('tableNumber') }}"
                                   placeholder="Masukkan nomor meja"
                                   required>
                            @error('tableNumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="capacity" class="form-label">
                                <i class="fas fa-users me-1"></i>Kapasitas <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('capacity') is-invalid @enderror" 
                                   id="capacity" 
                                   name="capacity" 
                                   value="{{ old('capacity') }}"
                                   min="1" 
                                   max="20"
                                   placeholder="Jumlah orang"
                                   required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="branchId" class="form-label">
                                <i class="fas fa-building me-1"></i>Cabang <span class="text-danger">*</span>
                            </label>
                            <select name="branchId" 
                                    id="branchId" 
                                    class="form-select @error('branchId') is-invalid @enderror" 
                                    required>
                                <option value="">-- Pilih Cabang --</option>
                                @if($branches->successful())
                                    @php
                                        $branchData = $branches->json();
                                        $branchList = isset($branchData['data']) ? $branchData['data'] : $branchData;
                                    @endphp
                                    @if(is_array($branchList))
                                        @foreach($branchList as $branch)
                                            <option value="{{ $branch['id'] }}" 
                                                    {{ old('branchId') == $branch['id'] ? 'selected' : '' }}>
                                                @if(isset($branch['address']))
                                                    {{ $branch['branchName'] }} - {{ $branch['address'] }}
                                                @else
                                                    {{ $branch['branchName'] }}
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            @error('branchId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Deskripsi <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Masukkan deskripsi meja (contoh: Dekat jendela, Area VIP, dll)"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Pastikan nomor meja belum ada di cabang yang dipilih.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Simpan Meja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTableModal" tabindex="-1" aria-labelledby="editTableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" id="editTableForm">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark">
                    <h1 class="modal-title fs-5" id="editTableModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Meja
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_tableNumber" class="form-label">
                                <i class="fas fa-table me-1"></i>Nomor Meja <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="edit_tableNumber" 
                                   name="tableNumber" 
                                   placeholder="Masukkan nomor meja"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_capacity" class="form-label">
                                <i class="fas fa-users me-1"></i>Kapasitas <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="edit_capacity" 
                                   name="capacity" 
                                   min="1" 
                                   max="20"
                                   placeholder="Jumlah orang"
                                   required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="edit_branchId" class="form-label">
                                <i class="fas fa-building me-1"></i>Cabang <span class="text-danger">*</span>
                            </label>
                            <select name="branchId" 
                                    id="edit_branchId" 
                                    class="form-select" 
                                    required>
                                <option value="">-- Pilih Cabang --</option>
                                @if($branches->successful())
                                    @php
                                        $branchData = $branches->json();
                                        $branchList = isset($branchData['data']) ? $branchData['data'] : $branchData;
                                    @endphp
                                    @if(is_array($branchList))
                                        @foreach($branchList as $branch)
                                            <option value="{{ $branch['id'] }}">
                                                @if(isset($branch['address']))
                                                    {{ $branch['branchName'] }} - {{ $branch['address'] }}
                                                @else
                                                    {{ $branch['branchName'] }}
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="edit_description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Deskripsi <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="edit_description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Masukkan deskripsi meja"
                                      required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="editSubmitBtn">
                        <i class="fas fa-save me-1"></i>Update Meja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteTableModal" tabindex="-1" aria-labelledby="deleteTableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h1 class="modal-title fs-5" id="deleteTableModalLabel">
                    <i class="fas fa-trash me-2"></i>Konfirmasi Hapus
                </h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>
                <h5>Apakah Anda yakin?</h5>
                <p class="text-muted">Anda akan menghapus meja <strong id="delete_table_number"></strong>. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form action="" method="POST" id="deleteTableForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteSubmitBtn">
                        <i class="fas fa-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
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
    const searchInput = document.getElementById('searchTable');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterTables() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusTerm = statusFilter ? statusFilter.value.toLowerCase() : '';
        const tableRows = document.querySelectorAll('#tableTable tbody tr');
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const tableNumber = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const capacity = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            const matchesSearch = tableNumber.includes(searchTerm) || 
                                capacity.includes(searchTerm) || 
                                status.includes(searchTerm);
            
            let matchesStatus = true;
            if (statusTerm) {
                if (statusTerm === 'available') {
                    matchesStatus = status.includes('tersedia');
                } else if (statusTerm === 'occupied') {
                    matchesStatus = status.includes('terisi');
                }
            }
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update visible count
        const totalElement = document.getElementById('totalTables');
        if (totalElement) {
            totalElement.textContent = visibleCount;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTables);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTables);
    }

    // Detail button functionality
    const detailButtons = document.querySelectorAll('.view-detail-btn');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tableData = JSON.parse(this.getAttribute('data-table'));
            
            // Fill modal with table details
            document.getElementById('detail_table_number').textContent = tableData.tableNumber;
            document.getElementById('detail_capacity').textContent = tableData.capacity + ' Orang';
            document.getElementById('detail_status').innerHTML = `<span class="badge ${tableData.isAvailable ? 'bg-success' : 'bg-danger'}">${tableData.isAvailable ? 'Tersedia' : 'Terisi'}</span>`;
            document.getElementById('detail_branch').textContent = tableData.branchName;
            document.getElementById('detail_description').textContent = tableData.description || '-';
            document.getElementById('detail_created_at').textContent = new Date(tableData.createdAt).toLocaleString('id-ID');
            document.getElementById('detail_updated_at').textContent = new Date(tableData.updatedAt).toLocaleString('id-ID');
        });
    });

    // Create Form handling
    const createForm = document.getElementById('createTableForm');
    const submitBtn = document.getElementById('submitBtn');

    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            submitBtn.disabled = true;
        });
    }

    // Reset create form when modal is closed
    const createModal = document.getElementById('createTableModal');
    if (createModal) {
        createModal.addEventListener('hidden.bs.modal', function () {
            if (createForm) {
                createForm.reset();
                const inputs = createForm.querySelectorAll('.is-invalid');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
            }
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Meja';
                submitBtn.disabled = false;
            }
        });
    }

    // Auto-set selected branch in create modal
    const branchSelect = document.getElementById('branch_id');
    const createBranchSelect = document.getElementById('branchId');

    if (branchSelect && createBranchSelect) {
        const selectedBranchId = branchSelect.value;
        if (selectedBranchId) {
            createBranchSelect.value = selectedBranchId;
        }
    }

    // Edit Table Modal
    const editButtons = document.querySelectorAll('.edit-table-btn');
    const editForm = document.getElementById('editTableForm');
    const editSubmitBtn = document.getElementById('editSubmitBtn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tableData = JSON.parse(this.getAttribute('data-table'));
            
            // Set form action dengan URL yang benar
            const updateUrl = '{{ url("table") }}/' + tableData.id;
            editForm.action = updateUrl;
            
            // Fill form with table data
            document.getElementById('edit_tableNumber').value = tableData.tableNumber;
            document.getElementById('edit_capacity').value = tableData.capacity;
            document.getElementById('edit_branchId').value = tableData.branchId;
            document.getElementById('edit_description').value = tableData.description || '';
        });
    });

    // Edit form submit handling
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (editSubmitBtn) {
                editSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memperbarui...';
                editSubmitBtn.disabled = true;
            }
        });
    }

    // Reset edit form when modal is closed
    const editModal = document.getElementById('editTableModal');
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function () {
            if (editForm) {
                editForm.reset();
            }
            if (editSubmitBtn) {
                editSubmitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Update Meja';
                editSubmitBtn.disabled = false;
            }
        });
    }

    // Delete Table Modal
    const deleteButtons = document.querySelectorAll('.delete-table-btn');
    const deleteForm = document.getElementById('deleteTableForm');
    const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');
    const deleteModalElement = document.getElementById('deleteTableModal');
    
    let deleteModal;
    if (deleteModalElement) {
        deleteModal = new bootstrap.Modal(deleteModalElement);
    }

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tableId = this.getAttribute('data-table-id');
            const tableNumber = this.getAttribute('data-table-number');
            
            // Set form action dengan URL yang benar
            const deleteUrl = '{{ url("table") }}/' + tableId;
            deleteForm.action = deleteUrl;
            
            // Set table number in modal
            document.getElementById('delete_table_number').textContent = tableNumber;
            
            // Show modal
            if (deleteModal) {
                deleteModal.show();
            }
        });
    });

    // Delete form submit handling
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            if (deleteSubmitBtn) {
                deleteSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
                deleteSubmitBtn.disabled = true;
            }
        });
    }

    // Reset delete form when modal is closed
    if (deleteModalElement) {
        deleteModalElement.addEventListener('hidden.bs.modal', function () {
            if (deleteSubmitBtn) {
                deleteSubmitBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Ya, Hapus';
                deleteSubmitBtn.disabled = false;
            }
        });
    }
});

// Refresh data
function refreshData() {
    location.reload();
}
</script>

      @endsection