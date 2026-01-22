@extends('backend.layout.structure')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Inventory Item Details</h4>
                        <div>
                            <a href="{{ route('backend.inventory-items.edit', $inventoryItem->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('backend.inventory-items.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Item Code:</strong> <span
                                    class="badge bg-info">{{ $inventoryItem->item_code }}</span><br>
                                <strong>Item Name:</strong> {{ $inventoryItem->name }}<br>
                                <strong>Manufacturer:</strong> {{ $inventoryItem->manufacturer ?? 'N/A' }}<br>
                                <strong>Supplier:</strong> {{ $inventoryItem->supplier ?? 'N/A' }}<br>
                                <strong>Description:</strong> {{ $inventoryItem->description ?? 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Category:</strong> {{ $inventoryItem->category_name }}<br>
                                <strong>Subcategory:</strong> {{ $inventoryItem->subcategory ?? 'N/A' }}<br>
                                <strong>Unit:</strong> {{ $inventoryItem->unit_name }}<br>
                                <strong>Reorder Level:</strong> {{ $inventoryItem->reorder_level }}<br>
                                <strong>Optimum Level:</strong> {{ $inventoryItem->optimum_level ?? 0 }}<br>
                                <strong>Status:</strong>
                                <span
                                    class="badge bg-{{ $inventoryItem->status == 'active' ? 'success' : ($inventoryItem->status == 'inactive' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($inventoryItem->status) }}
                                </span>
                            </div>
                        </div>

                        <hr>

                        <!-- Stock Info -->
                        <div class="mb-4">
                            <h5>Stock Information</h5>
                            <p>
                                <strong>Current Stock:</strong> {{ $inventoryItem->current_stock }}
                                {{ $inventoryItem->unit_name }}<br>
                                <strong>Stock Status:</strong>
                                <span class="badge bg-{{ $inventoryItem->stock_status_color }}">
                                    {{ $inventoryItem->stock_status_text }}
                                </span>
                            </p>
                        </div>

                        <!-- Transactions -->
                        <div class="mb-4">
                            <h5>Latest Transactions</h5>
                            @if($inventoryItem->transactions && $inventoryItem->transactions->count() > 0)
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryItem->transactions as $index => $tx)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                                <td>{{ ucfirst($tx->type ?? 'N/A') }}</td>
                                                <td>{{ $tx->quantity ?? 0 }} {{ $inventoryItem->unit_name }}</td>
                                                <td>{{ $tx->remarks ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No transactions found.</p>
                            @endif
                        </div>

                        <!-- Usages -->
                        <div class="mb-4">
                            <h5>Latest Usages</h5>
                            @if($inventoryItem->usages && $inventoryItem->usages->count() > 0)
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Quantity Used</th>
                                            <th>Purpose</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryItem->usages as $index => $usage)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $usage->created_at->format('Y-m-d H:i') }}</td>
                                                <td>{{ $usage->quantity ?? 0 }} {{ $inventoryItem->unit_name }}</td>
                                                <td>{{ $usage->purpose ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No usages found.</p>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection