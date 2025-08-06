@extends('layouts.admin')

@section('title', 'Shift Applications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shift Applications</h3>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Worker</th>
                                    <th>Facility</th>
                                    <th>Shift</th>
                                    <th>Shift Time</th>
                                    <th>Pay Rate</th>
                                    <th>Status</th>
                                    <th>Applied At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr>
                                        <td>{{ $application->id }}</td>
                                        <td>
                                            <strong>{{ $application->medicalWorker->first_name ?? 'Unknown' }} {{ $application->medicalWorker->last_name ?? 'Worker' }}</strong><br>
                                            <small class="text-muted">{{ $application->medicalWorker->email ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $application->shift->facility->facility_name ?? 'Unknown Facility' }}</td>
                                        <td>{{ $application->shift->title ?? 'Shift' }}</td>
                                        <td>
                                            <strong>{{ $application->shift->start_datetime->format('M d, Y') }}</strong><br>
                                            <small>{{ $application->shift->start_datetime->format('H:i') }} - {{ $application->shift->end_datetime->format('H:i') }}</small>
                                        </td>
                                        <td>${{ number_format($application->shift->pay_rate, 2) }}</td>
                                        <td>
                                            @switch($application->status)
                                                @case('waiting')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge bg-info">In Progress</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-primary">Completed</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($application->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $application->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group-vertical d-grid gap-1" role="group">
                                                <a href="{{ route('admin.shift-applications.show', $application) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-cogs"></i> Action Center
                                                </a>
                                                @if($application->status === 'waiting')
                                                    <div class="btn-group" role="group">
                                                        <form action="{{ route('admin.shift-applications.approve', $application) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this application?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.shift-applications.reject', $application) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this application?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No shift applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush
