@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>User Details</h4>
                    <div>
                        @if(auth()->user()->isAdmin() || auth()->id() === $user->id)
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" 
                                 class="rounded-circle mb-3" width="150" height="150">
                            <h5>{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                            
                            <div class="mb-3">
                                <span class="badge badge-{{ $user->role == 'Admin' ? 'danger' : 'primary' }} me-2">
                                    {{ $user->role }}
                                </span>
                                <span class="badge badge-{{ $user->active ? 'success' : 'secondary' }}">
                                    {{ $user->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Name</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        <span class="badge badge-{{ $user->role == 'Admin' ? 'danger' : 'primary' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $user->active ? 'success' : 'secondary' }}">
                                            {{ $user->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email Verified</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <i class="fas fa-check-circle text-success"></i> 
                                            {{ $user->email_verified_at->format('M d, Y H:i') }}
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Not verified
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Member Since</th>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5>User Activity</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Reviews Written</h6>
                                    <h3 class="text-primary">{{ $user->reviews->count() }}</h3>
                                    <p class="text-muted">Product reviews</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Transactions</h6>
                                    <h3 class="text-success">{{ $user->transactions->count() }}</h3>
                                    <p class="text-muted">Total orders</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($user->reviews->count() > 0)
                        <hr>
                        <h5>Recent Reviews</h5>
                        <div class="row">
                            @foreach($user->reviews->take(3) as $review)
                                <div class="col-md-12 mb-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="card-title">{{ $review->product->name }}</h6>
                                                    <div class="mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                        <span class="ms-2">({{ $review->rating }}/5)</span>
                                                    </div>
                                                    <p class="card-text">{{ $review->comment }}</p>
                                                </div>
                                                <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge-primary { background-color: #007bff; }
.badge-danger { background-color: #dc3545; }
.badge-success { background-color: #28a745; }
.badge-secondary { background-color: #6c757d; }
</style>
@endpush
