@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Data Reservasi Meja</h1>
            <p class="text-muted mb-0">Data Reservasi Meja ICOS</p>
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
                    <form method="GET" action="{{ route('table-reservation.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="branch_id" class="form-label">Cabang</label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @if($branch->successful())
                                        @foreach($branch->json() as $branchItem)
                                            <option value="{{ $branchItem['id'] }}" 
                                                    {{ $selectedBranch == $branchItem['id'] ? 'selected' : '' }}>
                                                {{ $branchItem['branchName'] }} - {{ $branchItem['address'] ?? 'Alamat tidak tersedia' }}
                                            </option>
                                        @endforeach
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
        @php
            // Data sudah difilter di controller, jadi langsung gunakan $filteredReservations
        @endphp

        <!-- Search -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchReservation" placeholder="Cari nama customer, kode reservasi, atau nomor meja...">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="statusFilter">
                  <option value="">Semua Status</option>
                  <option value="0">Pending</option>
                  <option value="1">Confirmed</option>
                  <option value="2">Check-in</option>
                  <option value="3">Completed</option>
                  <option value="4">Cancelled</option>
                  </select>

            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-check me-2"></i>Data Reservasi Meja
                    </h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">Total: <span id="totalReservations">{{ count($filteredReservations) }}</span></span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($filteredReservations) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="reservationTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 60px;">
                                        <i class="fas fa-hashtag"></i> No
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-user me-1"></i>Customer
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-code me-1"></i>Kode Reservasi
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-table me-1"></i>Meja
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-calendar me-1"></i>Tanggal & Waktu
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-users me-1"></i>Tamu
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-info-circle me-1"></i>Status
                                    </th>
                                    <th scope="col" class="text-center" style="width: 150px;">
                                        <i class="fas fa-cogs me-1"></i>Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredReservations as $index => $reservation)
                                    <tr data-reservation-id="{{ $reservation['id'] }}">
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $reservation['customerName'] }}</h6>
                                                    <small class="text-muted">{{ $reservation['customerPhone'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $reservation['reservationCode'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-chair text-white"></i>
                                                </div>
                                                <span class="fw-bold">{{ $reservation['tableNumber'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ \Carbon\Carbon::parse($reservation['reservationDateTime'])->format('d/m/Y') }}</strong>
                                                <br>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $reservation['guestCount'] }} orang</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($reservation['status']) {
                                                      0 => 'bg-warning',   // Pending
                                                      1 => 'bg-success',   // Confirmed
                                                      2 => 'bg-primary',   // Check-in
                                                      3 => 'bg-info',      // Completed
                                                      4 => 'bg-danger',    // Cancelled
                                                      default => 'bg-secondary'
                                                };
                                                $statusText = match($reservation['status']) {
                                                      0 => 'Pending',
                                                      1 => 'Confirmed',
                                                      2 => 'Check-in',
                                                      3 => 'Completed',
                                                      4 => 'Cancelled',
                                                      default => 'Unknown'
                                                };
                                                @endphp
                                            <span class="badge {{ $statusClass }}" data-status="{{ $reservation['status'] }}">{{ $statusText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <!-- Detail Button -->
                                                <button type="button" 
                                                        class="btn btn-info btn-sm view-detail-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal"
                                                        data-reservation='@json($reservation)'
                                                        title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                @if($reservation['status'] == 0)
                                                <!-- Confirm Button for Pending -->
                                                <form method="POST" action="{{ route('table-reservation.confirm', $reservation['id']) }}" class="d-inline">
                                                      @csrf
                                                      <button type="submit" 
                                                            class="btn btn-success btn-sm" 
                                                            title="Konfirmasi"
                                                            onclick="return confirm('Yakin ingin mengkonfirmasi reservasi ini?')">
                                                            <i class="fas fa-check"></i>
                                                      </button>
                                                </form>
                                                
                                                <!-- Cancel Button for Pending -->
                                                <form method="POST" action="{{ route('table-reservation.cancel', $reservation['id']) }}" class="d-inline">
                                                      @csrf
                                                      <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Batalkan"
                                                            onclick="return confirm('Yakin ingin membatalkan reservasi ini?')">
                                                            <i class="fas fa-times"></i>
                                                      </button>
                                                </form>
                                                @elseif($reservation['status'] == 1)
                                                <!-- Check-in Button for Confirmed -->
                                                <form method="POST" action="{{ route('table-reservation.checkin', $reservation['id']) }}" class="d-inline">
                                                      @csrf
                                                      <button type="submit" 
                                                            class="btn btn-primary btn-sm" 
                                                            title="Check-in"
                                                            onclick="return confirm('Yakin ingin melakukan check-in?')">
                                                            <i class="fas fa-sign-in-alt"></i>
                                                      </button>
                                                </form>
                                                
                                                <!-- Cancel Button for Confirmed -->
                                                <form method="POST" action="{{ route('table-reservation.cancel', $reservation['id']) }}" class="d-inline">
                                                      @csrf
                                                      <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Batalkan"
                                                            onclick="return confirm('Yakin ingin membatalkan reservasi ini?')">
                                                            <i class="fas fa-times"></i>
                                                      </button>
                                                </form>
                                                @elseif($reservation['status'] == 2)
                                                <!-- Complete Button for Check-in -->
                                                <form method="POST" action="{{ route('table-reservation.complete', $reservation['id']) }}" class="d-inline">
                                                      @csrf
                                                      <button type="submit" 
                                                            class="btn btn-warning btn-sm" 
                                                            title="Selesaikan"
                                                            onclick="return confirm('Yakin ingin menyelesaikan reservasi ini?')">
                                                            <i class="fas fa-flag-checkered"></i>
                                                      </button>
                                                </form>
                                                @endif

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
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Data Reservasi</h4>
                            <p class="text-muted">Tidak ada data reservasi meja untuk cabang yang dipilih</p>
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
                <p class="text-muted">Silakan pilih cabang di atas untuk melihat data reservasi meja</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal Detail Reservation -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="detailModalLabel">Detail Reservasi Meja</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer:</strong>
                        <p id="detail_customer_name" class="mb-1"></p>
                        <small class="text-muted" id="detail_customer_phone"></small>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p id="detail_customer_email" class="mb-1"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Kode Reservasi:</strong>
                        <p id="detail_reservation_code" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Nomor Meja:</strong>
                        <p id="detail_table_number" class="mb-1"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p id="detail_status" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Jumlah Tamu:</strong>
                        <p id="detail_guest_count" class="mb-1"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tanggal & Waktu Reservasi:</strong>
                        <p id="detail_reservation_datetime" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Durasi:</strong>
                        <p id="detail_duration" class="mb-1"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Waktu Berakhir:</strong>
                        <p id="detail_end_time" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Dibuat oleh:</strong>
                        <p id="detail_created_by" class="mb-1"></p>
                    </div>
                </div>
                
                <hr>
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Catatan:</strong>
                        <p id="detail_notes" class="mb-1"></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <strong>Tanggal Dibuat:</strong>
                        <p id="detail_created_at" class="mb-1"></p>
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
    const searchInput = document.getElementById('searchReservation');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterReservations() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusTerm = statusFilter ? statusFilter.value : '';
        const tableRows = document.querySelectorAll('#reservationTable tbody tr');
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const customerName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const reservationCode = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const tableNumber = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const statusBadge = row.querySelector('td:nth-child(7) .badge');
            const status = statusBadge ? statusBadge.getAttribute('data-status') : '';
            
            const matchesSearch = customerName.includes(searchTerm) || 
                                reservationCode.includes(searchTerm) || 
                                tableNumber.includes(searchTerm);
            const matchesStatus = !statusTerm || status === statusTerm;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update visible count
        const totalElement = document.getElementById('totalReservations');
        if (totalElement) {
            totalElement.textContent = visibleCount;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterReservations);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterReservations);
    }

    // Detail button functionality
    const detailButtons = document.querySelectorAll('.view-detail-btn');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reservationData = JSON.parse(this.getAttribute('data-reservation'));
            
            // Fill modal with reservation details
            document.getElementById('detail_customer_name').textContent = reservationData.customerName;
            document.getElementById('detail_customer_phone').textContent = reservationData.customerPhone;
            document.getElementById('detail_customer_email').textContent = reservationData.customerEmail;
            document.getElementById('detail_reservation_code').textContent = reservationData.reservationCode;
            document.getElementById('detail_table_number').textContent = reservationData.tableNumber;
            document.getElementById('detail_guest_count').textContent = reservationData.guestCount + ' orang';
            document.getElementById('detail_duration').textContent = reservationData.durationHours + ' jam';
            document.getElementById('detail_notes').textContent = reservationData.notes || 'Tidak ada catatan';
            document.getElementById('detail_created_by').textContent = reservationData.createdBy;
            
            // Format dates
            const reservationDate = new Date(reservationData.reservationDateTime);
            const endDate = new Date(reservationData.reservationEndTime);
            const createdDate = new Date(reservationData.createdAt);
            
            document.getElementById('detail_reservation_datetime').textContent = reservationDate.toLocaleString('id-ID');
            document.getElementById('detail_end_time').textContent = endDate.toLocaleString('id-ID');
            document.getElementById('detail_created_at').textContent = createdDate.toLocaleString('id-ID');
            
            // Status
            const statusText = getStatusText(reservationData.status);
            const statusClass = getStatusClass(reservationData.status);
            document.getElementById('detail_status').innerHTML = `<span class="badge ${statusClass}">${statusText}</span>`;
        });
    });

    function getStatusClass(status) {
    switch(status) {
        case 0: return 'bg-warning';    // Pending
        case 1: return 'bg-success';    // Confirmed
        case 2: return 'bg-primary';    // Check-in
        case 3: return 'bg-info';       // Completed
        case 4: return 'bg-danger';     // Cancelled
        default: return 'bg-secondary';
    }
}

function getStatusText(status) {
    switch(status) {
        case 0: return 'Pending';
        case 1: return 'Confirmed';
        case 2: return 'Check-in';
        case 3: return 'Completed';
        case 4: return 'Cancelled';
        default: return 'Unknown';
    }
}
});

// Refresh data
function refreshData() {
    location.reload();
}
</script>

@endsection