@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Data Transaksi</h1>
            <p class="text-muted mb-0">Data Transaksi/Order ICOS</p>
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
                    <form method="GET" action="{{ route('order.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="branch_id" class="form-label">Cabang</label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @if($branches->successful())
                                        @foreach($branches->json() as $branch)
                                            <option value="{{ $branch['id'] }}" 
                                                    {{ $selectedBranch == $branch['id'] ? 'selected' : '' }}>
                                                {{ $branch['branchName'] }} - {{ $branch['address'] }}
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
        <!-- Search -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchTransaction" placeholder="Cari nama customer, kode transaksi, atau status...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="Paid">Paid</option>
                        <option value="Waiting Payment">Waiting Payment</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-receipt me-2"></i>Data Transaksi
                    </h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">Total: <span id="totalTransactions">{{ count($filteredTransactions) }}</span></span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($filteredTransactions) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="transactionTable">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 60px;">
                                        <i class="fas fa-hashtag"></i> No
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-user me-1"></i>Customer
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-code me-1"></i>Kode Transaksi
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-money-bill-wave me-1"></i>Total
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-info-circle me-1"></i>Status
                                    </th>
                                    <th scope="col">
                                        <i class="fas fa-clock me-1"></i>Tanggal
                                    </th>
                                    <th scope="col" class="text-center">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredTransactions as $index => $transaction)
                                    <tr data-transaction-id="{{ $transaction['id'] }}">
                                        <td class="text-center fw-bold">
                                            <span class="badge bg-light text-dark">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $transaction['name'] }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $transaction['transactionCode'] }}</span>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 fw-bold text-success">Rp {{ number_format($transaction['total'], 0, ',', '.') }}</h6>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($transaction['status']) {
                                                    'Paid' => 'bg-success',
                                                    'Waiting Payment' => 'bg-warning',
                                                    'Pending' => 'bg-info',
                                                    'Cancelled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $transaction['status'] }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ \Carbon\Carbon::parse($transaction['createdAt'])->format('d/m/Y') }}</strong>
                                                <br>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-info btn-sm view-detail-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal"
                                                    data-transaction='@json($transaction)'>
                                                <i class="fas fa-eye"></i>
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
                            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Data Transaksi</h4>
                            <p class="text-muted">Tidak ada data transaksi untuk cabang yang dipilih</p>
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
                <p class="text-muted">Silakan pilih cabang di atas untuk melihat data transaksi</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal Detail Transaction -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="detailModalLabel">Detail Transaksi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Customer:</strong>
                        <p id="detail_customer_name" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Kode Transaksi:</strong>
                        <p id="detail_transaction_code" class="mb-1"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p id="detail_status" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tanggal:</strong>
                        <p id="detail_date" class="mb-1"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Dibuat oleh:</strong>
                        <p id="detail_created_by" class="mb-1"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Total:</strong>
                        <p id="detail_total" class="mb-1 text-success fw-bold"></p>
                    </div>
                </div>
                
                <hr>
                <h6>Item Transaksi:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detail_cart_items">
                        </tbody>
                    </table>
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
    const searchInput = document.getElementById('searchTransaction');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterTransactions() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusTerm = statusFilter ? statusFilter.value.toLowerCase() : '';
        const tableRows = document.querySelectorAll('#transactionTable tbody tr');
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const customerName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const transactionCode = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            
            const matchesSearch = customerName.includes(searchTerm) || 
                                transactionCode.includes(searchTerm) || 
                                status.includes(searchTerm);
            const matchesStatus = !statusTerm || status.includes(statusTerm);
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update visible count
        const totalElement = document.getElementById('totalTransactions');
        if (totalElement) {
            totalElement.textContent = visibleCount;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTransactions);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTransactions);
    }

    // Detail button functionality
    const detailButtons = document.querySelectorAll('.view-detail-btn');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const transactionData = JSON.parse(this.getAttribute('data-transaction'));
            
            // Fill modal with transaction details
            document.getElementById('detail_customer_name').textContent = transactionData.name;
            document.getElementById('detail_transaction_code').textContent = transactionData.transactionCode;
            document.getElementById('detail_status').innerHTML = `<span class="badge ${getStatusClass(transactionData.status)}">${transactionData.status}</span>`;
            document.getElementById('detail_date').textContent = new Date(transactionData.createdAt).toLocaleString('id-ID');
            document.getElementById('detail_created_by').textContent = transactionData.createdBy;
            document.getElementById('detail_total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(transactionData.total);
            
            // Fill cart items
            const cartItemsContainer = document.getElementById('detail_cart_items');
            cartItemsContainer.innerHTML = '';
            
            transactionData.cartItems.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.productName}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.productPrice)}</td>
                    <td>${item.quantity}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                `;
                cartItemsContainer.appendChild(row);
            });
        });
    });

    function getStatusClass(status) {
        switch(status) {
            case 'Paid': return 'bg-success';
            case 'Waiting Payment': return 'bg-warning';
            case 'Pending': return 'bg-info';
            case 'Cancelled': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
});

// Refresh data
function refreshData() {
    location.reload();
}
</script>

@endsection