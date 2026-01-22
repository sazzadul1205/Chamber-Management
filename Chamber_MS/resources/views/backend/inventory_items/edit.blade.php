@extends('backend.layout.structure')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    <h4>Edit Inventory Item</h4>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('backend.inventory-items.update', $inventoryItem->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="item_code" class="form-label">Item Code</label>
                            <input type="text" name="item_code" id="item_code" class="form-control" value="{{ old('item_code', $inventoryItem->item_code) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $inventoryItem->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category', $inventoryItem->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="subcategory" class="form-label">Subcategory</label>
                            <select name="subcategory" id="subcategory" class="form-control">
                                <option value="">Select Subcategory</option>
                                @foreach($subcategories as $cat => $subs)
                                    @foreach($subs as $sub)
                                        <option value="{{ $sub }}" {{ old('subcategory', $inventoryItem->subcategory) == $sub ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$sub)) }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select name="unit" id="unit" class="form-control" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $key => $label)
                                    <option value="{{ $key }}" {{ old('unit', $inventoryItem->unit) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reorder_level" class="form-label">Reorder Level</label>
                            <input type="number" name="reorder_level" id="reorder_level" class="form-control" value="{{ old('reorder_level', $inventoryItem->reorder_level ?? 0) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="optimum_level" class="form-label">Optimum Level</label>
                            <input type="number" name="optimum_level" id="optimum_level" class="form-control" value="{{ old('optimum_level', $inventoryItem->optimum_level ?? 0) }}">
                        </div>

                        <div class="mb-3">
                            <label for="manufacturer" class="form-label">Manufacturer</label>
                            <input type="text" name="manufacturer" id="manufacturer" class="form-control" value="{{ old('manufacturer', $inventoryItem->manufacturer) }}">
                        </div>

                        <div class="mb-3">
                            <label for="supplier" class="form-label">Supplier</label>
                            <input type="text" name="supplier" id="supplier" class="form-control" value="{{ old('supplier', $inventoryItem->supplier) }}">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description', $inventoryItem->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" {{ old('status', $inventoryItem->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $inventoryItem->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="discontinued" {{ old('status', $inventoryItem->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('backend.inventory-items.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Item</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
