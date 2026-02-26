@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Create Transaction</h4>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
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

                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}" {{ old('status', 'completed') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Total Amount</label>
                                <div class="form-control-plaintext fw-bold" id="calculatedTotal">$0.00</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h5>Items</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;">Product</th>
                                        <th style="width: 15%;">Unit Price</th>
                                        <th style="width: 15%;">Quantity</th>
                                        <th style="width: 15%;">Line Total</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < max(1, old('product_id') ? count(old('product_id')) : 1); $i++)
                                        <tr>
                                            <td>
                                                <select name="product_id[]" class="form-select product-select">
                                                    <option value="">Select product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->unit_price }}"
                                                            {{ old('product_id.' . $i) == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }} - ${{ number_format($product->unit_price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <span class="unit-price">$0.00</span>
                                            </td>
                                            <td>
                                                <input type="number" name="quantity[]" class="form-control quantity-input" min="1" value="{{ old('quantity.' . $i, 1) }}">
                                            </td>
                                            <td>
                                                <span class="line-total">$0.00</span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-outline-dark btn-sm mb-3" id="addItemRow">
                            <i class="fas fa-plus"></i> Add Item
                        </button>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function recalcRow(row) {
        const select = row.querySelector('.product-select');
        const unitPriceElem = row.querySelector('.unit-price');
        const quantityInput = row.querySelector('.quantity-input');
        const lineTotalElem = row.querySelector('.line-total');

        const selectedOption = select.options[select.selectedIndex];
        const unitPrice = parseFloat(selectedOption?.dataset.price || 0);
        const quantity = parseInt(quantityInput.value || '1', 10);

        unitPriceElem.textContent = `$${unitPrice.toFixed(2)}`;
        const lineTotal = unitPrice * quantity;
        lineTotalElem.textContent = `$${lineTotal.toFixed(2)}`;
    }

    function recalcTotal() {
        let total = 0;
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
            const lineTotalElem = row.querySelector('.line-total');
            if (!lineTotalElem) return;
            const value = lineTotalElem.textContent.replace('$', '');
            total += parseFloat(value || 0);
        });
        document.getElementById('calculatedTotal').textContent = `$${total.toFixed(2)}`;
    }

    function bindRowEvents(row) {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const removeBtn = row.querySelector('.remove-row');

        if (select) {
            select.addEventListener('change', () => {
                recalcRow(row);
                recalcTotal();
            });
        }

        if (quantityInput) {
            quantityInput.addEventListener('input', () => {
                recalcRow(row);
                recalcTotal();
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                const tbody = document.querySelector('#itemsTable tbody');
                if (tbody.rows.length > 1) {
                    row.remove();
                    recalcTotal();
                }
            });
        }

        recalcRow(row);
        recalcTotal();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => bindRowEvents(row));

        document.getElementById('addItemRow').addEventListener('click', function () {
            const tbody = document.querySelector('#itemsTable tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.type === 'number') {
                    input.value = 1;
                } else {
                    input.value = '';
                }
            });

            newRow.querySelectorAll('.unit-price, .line-total').forEach(span => {
                span.textContent = '$0.00';
            });

            tbody.appendChild(newRow);
            bindRowEvents(newRow);
        });
    });
</script>
@endpush

