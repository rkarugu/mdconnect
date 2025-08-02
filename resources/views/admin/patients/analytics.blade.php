@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">
                <i class="fas fa-chart-bar mr-3"></i> Patient Analytics
            </h1>
            <p class="text-gray-600 mt-1">Insights and Reports</p>
        </div>
    </div>

    <!-- Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($analytics['total_patients']) }}</h3>
                <p>Total Patients</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($analytics['verified_patients']) }}</h3>
                <p>Verified Patients</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($analytics['new_this_month']) }}</h3>
                <p>New This Month</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($analytics['active_last_30_days']) }}</h3>
                <p>Active (30 days)</p>
            </div>
            <div class="icon">
                <i class="fas fa-heartbeat"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Gender Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-venus-mars"></i> Gender Distribution
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="genderChart" style="height: 300px;"></canvas>
                <div class="mt-3">
                    <div class="row">
                        @foreach($analytics['gender_distribution'] as $gender)
                        <div class="col-6 col-md-3">
                            <div class="text-center">
                                <span class="badge badge-primary">{{ $gender->gender ?: 'Not Specified' }}</span>
                                <div class="font-weight-bold">{{ $gender->count }}</div>
                                <small class="text-muted">{{ number_format(($gender->count / $analytics['total_patients']) * 100, 1) }}%</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blood Type Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tint"></i> Blood Type Distribution
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="bloodTypeChart" style="height: 300px;"></canvas>
                <div class="mt-3">
                    <div class="row">
                        @foreach($analytics['blood_type_distribution'] as $bloodType)
                        <div class="col-6 col-md-3">
                            <div class="text-center">
                                <span class="badge badge-danger">{{ $bloodType->blood_type ?: 'Unknown' }}</span>
                                <div class="font-weight-bold">{{ $bloodType->count }}</div>
                                <small class="text-muted">{{ number_format(($bloodType->count / $analytics['total_patients']) * 100, 1) }}%</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Age Groups and Registration Trends -->
<div class="row">
    <!-- Age Groups -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-birthday-cake"></i> Age Groups
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="ageGroupChart" style="height: 300px;"></canvas>
                <div class="mt-3">
                    <div class="row">
                        @foreach($analytics['age_groups'] as $ageGroup)
                        <div class="col-6 col-md-4">
                            <div class="text-center">
                                <span class="badge badge-info">{{ $ageGroup->age_group }}</span>
                                <div class="font-weight-bold">{{ $ageGroup->count }}</div>
                                <small class="text-muted">{{ number_format(($ageGroup->count / $analytics['total_patients']) * 100, 1) }}%</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Trends -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> Registration Trends (Last 12 Months)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="registrationTrendChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table"></i> Detailed Statistics
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Patients</td>
                            <td>{{ number_format($analytics['total_patients']) }}</td>
                            <td>100%</td>
                        </tr>
                        <tr>
                            <td>Verified Patients</td>
                            <td>{{ number_format($analytics['verified_patients']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['verified_patients'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Unverified Patients</td>
                            <td>{{ number_format($analytics['total_patients'] - $analytics['verified_patients']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format((($analytics['total_patients'] - $analytics['verified_patients']) / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Patients with Profile Pictures</td>
                            <td>{{ number_format($analytics['patients_with_pictures']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['patients_with_pictures'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Patients with Medical Conditions</td>
                            <td>{{ number_format($analytics['patients_with_conditions']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['patients_with_conditions'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Patients with Allergies</td>
                            <td>{{ number_format($analytics['patients_with_allergies']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['patients_with_allergies'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Patients with Emergency Contacts</td>
                            <td>{{ number_format($analytics['patients_with_emergency_contacts']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['patients_with_emergency_contacts'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>Active in Last 30 Days</td>
                            <td>{{ number_format($analytics['active_last_30_days']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['active_last_30_days'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td>New This Month</td>
                            <td>{{ number_format($analytics['new_this_month']) }}</td>
                            <td>{{ $analytics['total_patients'] > 0 ? number_format(($analytics['new_this_month'] / $analytics['total_patients']) * 100, 1) : 0 }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.patients.export', ['format' => 'csv']) }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="{{ route('admin.patients.export', ['format' => 'excel']) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.patients.list') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-list"></i> View All Patients
                    </a>
                    <a href="{{ route('admin.patients.create') }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-user-plus"></i> Add New Patient
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary btn-block">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i> Recent Activity
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @if($analytics['recent_registrations']->count() > 0)
                        @foreach($analytics['recent_registrations'] as $patient)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $patient->full_name }}</strong>
                                    <br>
                                    <small class="text-muted">Registered</small>
                                </div>
                                <small class="text-muted">{{ $patient->created_at->diffForHumans() }}</small>
                            </div>
                        </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-center text-muted">
                            No recent activity
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .small-box {
        border-radius: 10px;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .table th {
        border-top: none;
        font-weight: 600;
    }
    @media print {
        .card-tools, .btn, .sidebar, .main-header, .main-footer {
            display: none !important;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderData = @json($analytics['gender_distribution']);
    
    new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: genderData.map(item => item.gender || 'Not Specified'),
            datasets: [{
                data: genderData.map(item => item.count),
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        }
    });

    // Blood Type Distribution Chart
    const bloodTypeCtx = document.getElementById('bloodTypeChart').getContext('2d');
    const bloodTypeData = @json($analytics['blood_type_distribution']);
    
    new Chart(bloodTypeCtx, {
        type: 'doughnut',
        data: {
            labels: bloodTypeData.map(item => item.blood_type || 'Unknown'),
            datasets: [{
                data: bloodTypeData.map(item => item.count),
                backgroundColor: [
                    '#ff6384',
                    '#36a2eb',
                    '#ffce56',
                    '#4bc0c0',
                    '#9966ff',
                    '#ff9f40',
                    '#ff6384',
                    '#c9cbcf'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        }
    });

    // Age Groups Chart
    const ageGroupCtx = document.getElementById('ageGroupChart').getContext('2d');
    const ageGroupData = @json($analytics['age_groups']);
    
    new Chart(ageGroupCtx, {
        type: 'bar',
        data: {
            labels: ageGroupData.map(item => item.age_group),
            datasets: [{
                label: 'Patients',
                data: ageGroupData.map(item => item.count),
                backgroundColor: '#17a2b8',
                borderColor: '#117a8b',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            legend: {
                display: false
            }
        }
    });

    // Registration Trends Chart
    const registrationTrendCtx = document.getElementById('registrationTrendChart').getContext('2d');
    const registrationTrendData = @json($analytics['registration_trends']);
    
    new Chart(registrationTrendCtx, {
        type: 'line',
        data: {
            labels: registrationTrendData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'New Registrations',
                data: registrationTrendData.map(item => item.count),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@stop
