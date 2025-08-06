@extends('layouts.admin')

@section('title', 'Shift Application Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shift Application #{{ $shiftApplication->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.shift-applications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Worker Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td>{{ $shiftApplication->medicalWorker->first_name ?? 'Unknown' }} {{ $shiftApplication->medicalWorker->last_name ?? 'Worker' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $shiftApplication->medicalWorker->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $shiftApplication->medicalWorker->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Specialization:</th>
                                    <td>{{ $shiftApplication->medicalWorker->specialization ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Shift Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Facility:</th>
                                    <td>{{ $shiftApplication->shift->facility->facility_name ?? 'Unknown Facility' }}</td>
                                </tr>
                                <tr>
                                    <th>Title:</th>
                                    <td>{{ $shiftApplication->shift->title ?? 'Shift' }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $shiftApplication->shift->start_datetime->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time:</th>
                                    <td>{{ $shiftApplication->shift->start_datetime->format('H:i') }} - {{ $shiftApplication->shift->end_datetime->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Pay Rate:</th>
                                    <td>${{ number_format($shiftApplication->shift->pay_rate, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Shift Status:</th>
                                    <td>
                                        @switch($shiftApplication->shift->status)
                                            @case('open')
                                                <span class="badge bg-info fs-6">Open</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-warning fs-6">In Progress</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success fs-6">Completed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary fs-6">{{ ucfirst($shiftApplication->shift->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Shift Timing Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Shift Timing</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="20%">Scheduled Start:</th>
                                    <td>{{ $shiftApplication->shift->start_datetime->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Scheduled End:</th>
                                    <td>{{ $shiftApplication->shift->end_datetime->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @if($shiftApplication->shift->actual_start_time)
                                <tr>
                                    <th>Actual Start:</th>
                                    <td>{{ $shiftApplication->shift->actual_start_time->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($shiftApplication->shift->actual_end_time)
                                <tr>
                                    <th>Actual End:</th>
                                    <td>{{ $shiftApplication->shift->actual_end_time->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($shiftApplication->shift->duration_display)
                                <tr>
                                    <th>Duration:</th>
                                    <td><strong>{{ $shiftApplication->shift->duration_display }}</strong></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Application Status</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="15%">Current Status:</th>
                                    <td>
                                        @switch($shiftApplication->status)
                                            @case('waiting')
                                                <span class="badge bg-warning fs-6">Pending Approval</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success fs-6">Approved</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger fs-6">Rejected</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-info fs-6">In Progress</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary fs-6">Completed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary fs-6">{{ ucfirst($shiftApplication->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Applied At:</th>
                                    <td>{{ $shiftApplication->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @if($shiftApplication->selected_at)
                                <tr>
                                    <th>Decision Made At:</th>
                                    <td>{{ $shiftApplication->selected_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($shiftApplication->started_at)
                                <tr>
                                    <th>Shift Started At:</th>
                                    <td>{{ $shiftApplication->started_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($shiftApplication->completed_at)
                                <tr>
                                    <th>Shift Completed At:</th>
                                    <td>{{ $shiftApplication->completed_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Action Center -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="fas fa-cogs"></i> Action Center</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Application Management -->
                                        @if($shiftApplication->status === 'waiting')
                                        <div class="col-md-6">
                                            <h6 class="text-primary"><i class="fas fa-user-check"></i> Application Management</h6>
                                            <div class="btn-group-vertical d-grid gap-2" role="group">
                                                <form action="{{ route('admin.shift-applications.approve', $shiftApplication) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this application? This will reject all other applications for this shift.')">
                                                        <i class="fas fa-check"></i> Approve Application
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.shift-applications.reject', $shiftApplication) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this application?')">
                                                        <i class="fas fa-times"></i> Reject Application
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Shift Management -->
                                        <div class="col-md-6">
                                            <h6 class="text-info"><i class="fas fa-calendar-alt"></i> Shift Management</h6>
                                            <div class="btn-group-vertical d-grid gap-2" role="group">
                                                <a href="{{ route('admin.shift-applications.index', ['shift_id' => $shiftApplication->shift->id]) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-users"></i> View All Applications
                                                </a>
                                                @if($shiftApplication->shift->status === 'completed')
                                                <button class="btn btn-outline-success" disabled>
                                                    <i class="fas fa-check-circle"></i> Shift Completed
                                                </button>
                                                @elseif($shiftApplication->shift->status === 'in_progress')
                                                <button class="btn btn-outline-warning" disabled>
                                                    <i class="fas fa-clock"></i> Shift In Progress
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($shiftApplication->status === 'completed' || $shiftApplication->shift->status === 'completed')
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-success"><i class="fas fa-chart-line"></i> Shift Analytics</h6>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-info" onclick="alert('Feature coming soon: Generate shift report')">
                                                    <i class="fas fa-file-pdf"></i> Generate Report
                                                </button>
                                                <button class="btn btn-outline-secondary" onclick="alert('Feature coming soon: Export shift data')">
                                                    <i class="fas fa-download"></i> Export Data
                                                </button>
                                                <button class="btn btn-outline-primary" onclick="alert('Feature coming soon: Send feedback request')">
                                                    <i class="fas fa-star"></i> Request Feedback
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Quick Stats -->
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-secondary"><i class="fas fa-info-circle"></i> Quick Stats</h6>
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <div class="small-box bg-info">
                                                        <div class="inner">
                                                            <h4>{{ $shiftApplication->shift->applications()->count() }}</h4>
                                                            <p>Total Applications</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="small-box bg-success">
                                                        <div class="inner">
                                                            <h4>{{ $shiftApplication->shift->applications()->where('status', 'approved')->count() }}</h4>
                                                            <p>Approved</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="small-box bg-primary">
                                                        <div class="inner">
                                                            <h4>{{ $shiftApplication->shift->applications()->where('status', 'completed')->count() }}</h4>
                                                            <p>Completed</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="small-box bg-warning">
                                                        <div class="inner">
                                                            <h4>${{ number_format($shiftApplication->shift->pay_rate, 0) }}</h4>
                                                            <p>Pay Rate</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
