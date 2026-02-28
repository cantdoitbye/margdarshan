@extends('admin.layouts.app')

@section('title', 'Tutors Management')
@section('page-title', 'Tutors Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold">All Tutors</h5>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterClass">
                    <option value="">All Classes</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">Class {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        
        <!-- Tutors Table -->
        <div class="table-responsive">
            <table id="tutorsTable" class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Classes</th>
                        <th>Status</th>
                        <th>Test Score</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tutors as $tutor)
                    <tr>
                        <td>{{ $tutor->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ strtoupper(substr($tutor->name, 0, 1)) }}
                                </div>
                                {{ $tutor->name }}
                            </div>
                        </td>
                        <td>{{ $tutor->email }}</td>
                        <td>{{ $tutor->tutorProfile->phone_number ?? 'N/A' }}</td>
                        <td>
                            @if($tutor->tutorProfile)
                                @if($tutor->tutorProfile->tutor_type === 'academic')
                                    @if($tutor->tutorProfile->qualifications && count($tutor->tutorProfile->qualifications) > 0)
                                        <span class="badge bg-primary">{{ count($tutor->tutorProfile->qualifications) }} qualifications</span>
                                    @else
                                        <span class="text-muted">No qualifications</span>
                                    @endif
                                @else
                                    @php
                                        $skills = is_array($tutor->tutorProfile->activity_skills) 
                                            ? $tutor->tutorProfile->activity_skills 
                                            : json_decode($tutor->tutorProfile->activity_skills, true);
                                    @endphp
                                    @if($skills && count($skills) > 0)
                                        <span class="badge bg-purple">{{ count($skills) }} skills</span>
                                    @else
                                        <span class="text-muted">No skills</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($tutor->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($tutor->status === 'under_review')
                                <span class="badge bg-warning">Under Review</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($tutor->tutorProfile && $tutor->tutorProfile->test_score !== null)
                                <span class="badge {{ $tutor->tutorProfile->test_score >= 70 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $tutor->tutorProfile->test_score }}%
                                </span>
                            @else
                                <span class="text-muted">Not taken</span>
                            @endif
                        </td>
                        <td>{{ $tutor->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary view-tutor" data-id="{{ $tutor->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($tutor->status === 'under_review')
                                <button class="btn btn-sm btn-outline-success approve-tutor" data-id="{{ $tutor->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger reject-tutor" data-id="{{ $tutor->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-warning toggle-status" data-id="{{ $tutor->id }}" data-status="{{ $tutor->status }}">
                                    <i class="fas fa-sync"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Tutor Modal -->
<div class="modal fade" id="viewTutorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tutor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="tutorDetails">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#tutorsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
    
    // Filter by status
    $('#filterStatus').on('change', function() {
        table.column(5).search(this.value).draw();
    });
    
    // Filter by class
    $('#filterClass').on('change', function() {
        table.column(4).search(this.value).draw();
    });
    
    // View tutor details
    $(document).on('click', '.view-tutor', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/tutors/${id}`,
            method: 'GET',
            success: function(response) {
                const tutor = response.data;
                const profile = tutor.tutor_profile || {};
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Personal Information</h6>
                            <p><strong>Name:</strong> ${tutor.name}</p>
                            <p><strong>Email:</strong> ${tutor.email}</p>
                            <p><strong>Phone:</strong> ${tutor.phone || 'N/A'}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge ${tutor.status === 'active' ? 'bg-success' : tutor.status === 'under_review' ? 'bg-warning' : 'bg-secondary'}">
                                    ${tutor.status.replace('_', ' ').toUpperCase()}
                                </span>
                            </p>
                            <p><strong>Joined:</strong> ${new Date(tutor.created_at).toLocaleDateString()}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Professional Information</h6>
                            <p><strong>Type:</strong> <span class="badge bg-info">${profile.tutor_type || 'N/A'}</span></p>
                            <p><strong>Education:</strong> ${profile.education || 'N/A'}</p>
                            <p><strong>Experience:</strong> ${profile.experience_years || 0} years</p>
                            <p><strong>Hourly Rate:</strong> ₹${profile.hourly_rate || 'N/A'}</p>
                            <p><strong>Language:</strong> ${profile.language || 'N/A'}</p>
                        </div>
                    </div>
                    <hr>
                `;
                
                // Show qualifications for academic tutors
                if (profile.tutor_type === 'academic' && profile.grouped_qualifications) {
                    html += `
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Teaching Qualifications</h6>
                    `;
                    
                    Object.keys(profile.grouped_qualifications).forEach(boardName => {
                        const qualifications = profile.grouped_qualifications[boardName];
                        html += `<div class="mb-3"><h6 class="text-primary">${boardName}</h6>`;
                        
                        // Group by class
                        const byClass = {};
                        qualifications.forEach(qual => {
                            const className = qual.class_level?.name || 'Unknown';
                            if (!byClass[className]) byClass[className] = [];
                            byClass[className].push(qual);
                        });
                        
                        Object.keys(byClass).forEach(className => {
                            html += `<div class="ms-3 mb-2"><strong>${className}:</strong> `;
                            byClass[className].forEach(qual => {
                                html += `<span class="badge bg-primary me-1">${qual.subject?.name}</span>`;
                            });
                            html += `</div>`;
                        });
                        
                        html += `</div>`;
                    });
                    
                    html += `<p class="text-muted"><small>Total: ${profile.qualifications?.length || 0} board-class-subject combinations</small></p></div></div><hr>`;
                }
                
                // Show activity skills for activity tutors
                if (profile.tutor_type === 'activity' && profile.activity_skills) {
                    const skills = Array.isArray(profile.activity_skills) ? profile.activity_skills : JSON.parse(profile.activity_skills || '[]');
                    html += `
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Activity Skills</h6>
                                <div>
                    `;
                    skills.forEach(skill => {
                        html += `<span class="badge bg-purple me-1 mb-1">${skill}</span>`;
                    });
                    html += `</div></div></div><hr>`;
                }
                
                // Show documents
                if (tutor.documents && tutor.documents.length > 0) {
                    html += `
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Documents</h6>
                                <ul class="list-group">
                    `;
                    tutor.documents.forEach(doc => {
                        html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${doc.document_type.replace('_', ' ').toUpperCase()}</span>
                                <a href="${doc.document_path}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                            </li>
                        `;
                    });
                    html += `</ul></div></div><hr>`;
                }
                
                // Show skill tests
                if (tutor.skill_tests && tutor.skill_tests.length > 0) {
                    html += `
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Skill Test Results</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Score</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                    `;
                    tutor.skill_tests.forEach(test => {
                        html += `
                            <tr>
                                <td>${test.subject}</td>
                                <td>${test.score}% (${test.correct_answers}/${test.total_questions})</td>
                                <td><span class="badge ${test.status === 'passed' ? 'bg-success' : 'bg-danger'}">${test.status.toUpperCase()}</span></td>
                            </tr>
                        `;
                    });
                    html += `</tbody></table></div></div></div>`;
                }
                
                $('#tutorDetails').html(html);
                $('#viewTutorModal').modal('show');
            },
            error: function() {
                showToast('Error', 'Failed to load tutor details', 'danger');
            }
        });
    });
    
    // Approve tutor
    $(document).on('click', '.approve-tutor', function() {
        if (!confirm('Are you sure you want to approve this tutor?')) return;
        
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/tutors/${id}/approve`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                showToast('Success', 'Tutor approved successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to approve tutor', 'danger');
            }
        });
    });
    
    // Reject tutor
    $(document).on('click', '.reject-tutor', function() {
        if (!confirm('Are you sure you want to reject this tutor?')) return;
        
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/tutors/${id}/reject`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                showToast('Success', 'Tutor rejected', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to reject tutor', 'danger');
            }
        });
    });
    
    // Toggle status
    $(document).on('click', '.toggle-status', function() {
        const id = $(this).data('id');
        const currentStatus = $(this).data('status');
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        if (!confirm(`Change tutor status to ${newStatus}?`)) return;
        
        $.ajax({
            url: `/admin/tutors/${id}/status`,
            method: 'POST',
            data: { status: newStatus },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                showToast('Success', 'Status updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to update status', 'danger');
            }
        });
    });
});
</script>
@endpush
