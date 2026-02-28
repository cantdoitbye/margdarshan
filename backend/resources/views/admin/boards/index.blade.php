@extends('admin.layouts.app')

@section('title', 'Boards Management')
@section('page-title', 'Boards Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Education Boards</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardModal">
            <i class="fas fa-plus me-2"></i>Add Board
        </button>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="boardsTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($boards as $board)
                    <tr>
                        <td>{{ $board->id }}</td>
                        <td>{{ $board->name }}</td>
                        <td>{{ $board->sort_order }}</td>
                        <td>
                            <span class="badge {{ $board->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $board->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $board->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-board" data-id="{{ $board->id }}" data-name="{{ $board->name }}" data-sort="{{ $board->sort_order }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-{{ $board->is_active ? 'warning' : 'success' }} toggle-board" data-id="{{ $board->id }}">
                                <i class="fas fa-{{ $board->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-board" data-id="{{ $board->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Board Modal -->
<div class="modal fade" id="addBoardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Board</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBoardForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Board Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Board</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Board Modal -->
<div class="modal fade" id="editBoardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Board</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBoardForm">
                <input type="hidden" name="id" id="editBoardId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Board Name *</label>
                        <input type="text" class="form-control" name="name" id="editBoardName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="editBoardSort">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Board</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#boardsTable').DataTable({
        order: [[2, 'asc']]
    });
    
    // Add board
    $('#addBoardForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.boards.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Board added successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to add board', 'danger');
            }
        });
    });
    
    // Edit board
    $(document).on('click', '.edit-board', function() {
        $('#editBoardId').val($(this).data('id'));
        $('#editBoardName').val($(this).data('name'));
        $('#editBoardSort').val($(this).data('sort'));
        $('#editBoardModal').modal('show');
    });
    
    $('#editBoardForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editBoardId').val();
        $.ajax({
            url: `/admin/boards/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Board updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to update board', 'danger');
            }
        });
    });
    
    // Toggle board
    $(document).on('click', '.toggle-board', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/boards/${id}/toggle`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Board status updated', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to update status', 'danger');
            }
        });
    });
    
    // Delete board
    $(document).on('click', '.delete-board', function() {
        if (!confirm('Are you sure you want to delete this board?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/boards/${id}`,
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                showToast('Success', 'Board deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to delete board', 'danger');
            }
        });
    });
});
</script>
@endpush
