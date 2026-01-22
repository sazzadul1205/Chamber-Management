@extends('backend.layout.structure')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <!-- Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Inventory Items Management</h4>
                    <div>
                        <a href="{{ route('backend.inventory-items.export') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('backend.inventory-items.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Item
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $totalItems }}</h5>
                                    <p class="card-text">Total Items</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $activeItems }}</h5>
                                    <p class="card-text">Active Items</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $lowStockItems }}</h5>
                                    <p class="card-text">Low Stock Items</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ count($categories) }}</h5>
                                    <p class="card-text">Categories</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('backend.inventory-items.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Search by code, name, manufacturer"
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-control">
                                    <option value="all">All Categories</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="stock_status" class="form-control">
                                    <option value="all">All Stock</option>
                                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="all">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Stock Status</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryItems as $item)
                                    <tr>
                                        <td>{{ ($inventoryItems->currentPage()-1)*$inventoryItems->perPage()+$loop->iteration }}</td>
                                        <td><span class="badge bg-info">{{ $item->item_code }}</span></td>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if($item->manufacturer)
                                                <br><small class="text-muted">Manufacturer: {{ $item->manufacturer }}</small>
                                            @endif
                                            @if($item->description)
                                                <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->category_name }}</span>
                                            @if($item->subcategory)
                                                <br><small>{{ $item->subcategory }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $item->current_stock ?? 0 }}</strong>
                                            <br><small class="text-muted">{{ $item->unit_name }}</small>
                                        </td>
                                        <td class="text-center">{{ $item->reorder_level ?? 0 }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $item->stock_status_color }}">
                                                {{ $item->stock_status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $item->status == 'active' ? 'success' : ($item->status == 'inactive' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.inventory-items.show', $item->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('backend.inventory-items.edit', $item->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-sm btn-danger delete-item" 
                                                    data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No inventory items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $inventoryItems->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Inventory Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="deleteItemName"></span>"?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteItemForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // Delete modal
    $('.delete-item').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#deleteItemForm').attr('action', '/inventory-items/' + id);
        $('#deleteItemName').text(name);
        $('#deleteItemModal').modal('show');
    });

});
</script>
@endsection
