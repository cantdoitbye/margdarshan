@extends('admin.layouts.app')

@section('title', 'Classes Management')
@section('page-title', 'Classes Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Grade Levels / Classes</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="fas fa-plus me-2"></i>Add Class
        </button>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="classesTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Subjects Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                    <tr>
                        <td>{{ $class->id }}</td>
                        <td><strong>{{ $class->name }}</strong></td>
                        <td>{{ $class->sort_order }}</td>
                        <td>
                            <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $class->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $class->subjects()->count() }} subjects</span>
                        </td>
                        <td>{{ $class->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-class" data-id="{{ $class->id }}" data-name="{{ $class->name }}" data-sort="{{ $class->sort_order }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-{{ $class->is_active ? 'warning' : 'success' }} toggle-class" data-id="{{ $class->id }}">
                                <i class="fas fa-{{ $class->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addClassForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name *</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g., Class 13" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="15">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editClassForm">
                <input type="hidden" name="id" id="editClassId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class Name *</label>
                        <input type="text" class="form-control" name="name" id="editClassName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="editClassSort">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Class</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#classesTable').DataTable({
        order: [[2, 'asc']]
    });
    
    // Add class
    $('#addClassForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.classes.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Class added successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to add class', 'danger');
            }
        });
    });
    
    // Edit class
    $(document).on('click', '.edit-class', function() {
        $('#editClassId').val($(this).data('id'));
        $('#editClassName').val($(this).data('name'));
        $('#editClassSort').val($(this).data('sort'));
        $('#editClassModal').modal('show');
    });
    
    $('#editClassForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editClassId').val();
        $.ajax({
            url: `/admin/classes/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Class updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to update class', 'danger');
            }
        });
    });
    
    // Toggle class
    $(document).on('click', '.toggle-class', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/classes/${id}/toggle`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Class status updated', 'success');
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
