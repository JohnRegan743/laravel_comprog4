@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Transaction Details</h4>
                    <div>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Transaction Info</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%;">Number</th>
                                    <td>{{ $transaction->transaction_number }}</td>
                                </tr>
                                <tr>
                                    <th>Customer</th>
                                    <td>{{ $transaction->user->name ?? 'N/A' }} ({{ $transaction->user->email ?? 'N/A' }})</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $badgeClass = match($transaction->status) {
                                                'completed' => 'success',
                                                'processing' => 'info',
                                                'cancelled' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <td class="fw-bold">${{ number_format($transaction->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Meta</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 35%;">Created At</th>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $transaction->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $transaction->notes ?: '—' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5>Items</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaction->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('transactions.update-status', $transaction) }}" method="POST" class="row g-2 align-items-center">
                                @csrf
                                <div class="col-auto">
                                    <label for="status" class="col-form-label">Update Status:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="status" id="status" class="form-select form-select-sm @error('status') is-invalid @enderror">
                                        @foreach($statusOptions as $status)
                                            <option value="{{ $status }}" {{ $transaction->status === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </div>
                                @error('status')
                                    <div class="col-12">
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    </div>
                                @enderror
                            </form>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <form action="{{ route('transactions.resend-receipt', $transaction) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark btn-sm">
                                    <i class="fas fa-envelope"></i> Resend Receipt Email
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

