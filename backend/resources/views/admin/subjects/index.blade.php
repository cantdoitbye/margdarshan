@extends('admin.layouts.app')

@section('title', 'Subjects Management')
@section('page-title', 'Subjects Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Subjects</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="fas fa-plus me-2"></i>Add Subject
        </button>
    </div>
    
    <div class="card-body">
        <!-- Filter -->
        <div class="mb-3">
            <select class="form-select" id="filterType" style="max-width: 200px;">
                <option value="">All Types</option>
                <option value="academic">Academic</option>
                <option value="activity">Activity</option>
            </select>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover" id="subjectsTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                    <tr>
                        <td>{{ $subject->id }}</td>
                        <td><strong>{{ $subject->name }}</strong></td>
                        <td>{{ $subject->category }}</td>
                        <td>
                            <span class="badge {{ $subject->type === 'academic' ? 'bg-primary' : 'bg-purple' }}">
                                {{ ucfirst($subject->type) }}
                            </span>
                        </td>
                        <td>{{ $subject->sort_order }}</td>
                        <td>
                            <span class="badge {{ $subject->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $subject->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-subject" 
                                data-id="{{ $subject->id }}" 
                                data-name="{{ $subject->name }}" 
                                data-category="{{ $subject->category }}"
                                data-type="{{ $subject->type }}"
                                data-sort="{{ $subject->sort_order }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-{{ $subject->is_active ? 'warning' : 'success' }} toggle-subject" data-id="{{ $subject->id }}">
                                <i class="fas fa-{{ $subject->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubjectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="category" placeholder="e.g., Science, Language">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select class="form-select" name="type" required>
                            <option value="academic">Academic</option>
                            <option value="activity">Activity</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubjectForm">
                <input type="hidden" name="id" id="editSubjectId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Name *</label>
                        <input type="text" class="form-control" name="name" id="editSubjectName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="category" id="editSubjectCategory">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select class="form-select" name="type" id="editSubjectType" required>
                            <option value="academic">Academic</option>
                            <option value="activity">Activity</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="editSubjectSort">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#subjectsTable').DataTable({
        order: [[4, 'asc']]
    });
    
    // Filter by type
    $('#filterType').on('change', function() {
        table.column(3).search(this.value).draw();
    });
    
    // Add subject
    $('#addSubjectForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.subjects.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Subject added successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to add subject', 'danger');
            }
        });
    });
    
    // Edit subject
    $(document).on('click', '.edit-subject', function() {
        $('#editSubjectId').val($(this).data('id'));
        $('#editSubjectName').val($(this).data('name'));
        $('#editSubjectCategory').val($(this).data('category'));
        $('#editSubjectType').val($(this).data('type'));
        $('#editSubjectSort').val($(this).data('sort'));
        $('#editSubjectModal').modal('show');
    });
    
    $('#editSubjectForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editSubjectId').val();
        $.ajax({
            url: `/admin/subjects/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Subject updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to update subject', 'danger');
            }
        });
    });
    
    // Toggle subject
    $(document).on('click', '.toggle-subject', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/subjects/${id}/toggle`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Subject status updated', 'success');
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
