@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <!-- Total Tutors -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Tutors</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_tutors'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Tutors -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Active Tutors</p>
                        <h3 class="mb-0 fw-bold text-success">{{ $stats['active_tutors'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-user-check fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Review -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Pending Review</p>
                        <h3 class="mb-0 fw-bold text-warning">{{ $stats['pending_tutors'] }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-user-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Questions -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Questions</p>
                        <h3 class="mb-0 fw-bold text-info">{{ $stats['total_questions'] }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-question-circle fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Tutors by Status -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Tutors by Status</h5>
            </div>
            <div class="card-body">
                <canvas id="tutorsStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Questions by Subject -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Questions by Subject</h5>
            </div>
            <div class="card-body">
                <canvas id="questionsSubjectChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
    <!-- Recent Tutors -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Recent Tutors</h5>
                <a href="{{ route('admin.tutors.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_tutors as $tutor)
                            <tr>
                                <td>{{ $tutor->name }}</td>
                                <td>{{ $tutor->email }}</td>
                                <td>
                                    @if($tutor->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($tutor->status === 'under_review')
                                        <span class="badge bg-warning">Under Review</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($tutor->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $tutor->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No tutors found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Questions by Class -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Questions by Class</h5>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($questions_by_class as $class => $count)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="fw-semibold">Class {{ $class }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tutors Status Chart
const tutorsCtx = document.getElementById('tutorsStatusChart').getContext('2d');
new Chart(tutorsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Under Review', 'Inactive'],
        datasets: [{
            data: [
                {{ $stats['active_tutors'] }},
                {{ $stats['pending_tutors'] }},
                {{ $stats['inactive_tutors'] }}
            ],
            backgroundColor: ['#198754', '#ffc107', '#6c757d']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Questions Subject Chart
const questionsCtx = document.getElementById('questionsSubjectChart').getContext('2d');
new Chart(questionsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($questions_by_subject)) !!},
        datasets: [{
            label: 'Questions',
            data: {!! json_encode(array_values($questions_by_subject)) !!},
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
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
</script>
@endpush
