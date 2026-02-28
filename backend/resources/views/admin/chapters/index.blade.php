@extends('admin.layouts.app')

@section('title', 'Chapters Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Chapters Management</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChapterModal">
            <i class="fas fa-plus me-2"></i>Add Chapter
        </button>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select" id="filterClass">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select" id="filterSubject">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by chapter name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Quizzes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chapters as $chapter)
                    <tr>
                        <td>{{ $chapter->id }}</td>
                        <td><strong>{{ $chapter->name }}</strong></td>
                        <td><span class="badge bg-primary">{{ $chapter->class->name }}</span></td>
                        <td><span class="badge bg-info">{{ $chapter->subject->name }}</span></td>
                        <td>{{ $chapter->sort_order }}</td>
                        <td>
                            <span class="badge {{ $chapter->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $chapter->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ $chapter->quizzes()->count() }} quizzes</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-chapter" 
                                data-id="{{ $chapter->id }}"
                                data-name="{{ $chapter->name }}"
                                data-class="{{ $chapter->class_id }}"
                                data-subject="{{ $chapter->subject_id }}"
                                data-description="{{ $chapter->description }}"
                                data-sort="{{ $chapter->sort_order }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-{{ $chapter->is_active ? 'warning' : 'success' }} toggle-chapter" data-id="{{ $chapter->id }}">
                                <i class="fas fa-{{ $chapter->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                            @if($chapter->quizzes()->count() == 0)
                            <button class="btn btn-sm btn-outline-danger delete-chapter" data-id="{{ $chapter->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No chapters found. Add your first chapter!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $chapters->links() }}
        </div>
    </div>
</div>

<!-- Add Chapter Modal -->
<div class="modal fade" id="addChapterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Chapter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.chapters.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class *</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chapter Name *</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g., Quadratic Equations" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Optional description..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Chapter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Chapter Modal -->
<div class="modal fade" id="editChapterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Chapter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editChapterForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class *</label>
                        <select name="class_id" id="editClass" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" id="editSubject" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chapter Name *</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="editSort">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Chapter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Edit chapter
    $(document).on('click', '.edit-chapter', function() {
        const id = $(this).data('id');
        $('#editName').val($(this).data('name'));
        $('#editClass').val($(this).data('class'));
        $('#editSubject').val($(this).data('subject'));
        $('#editDescription').val($(this).data('description'));
        $('#editSort').val($(this).data('sort'));
        $('#editChapterForm').attr('action', `/admin/chapters/${id}`);
        $('#editChapterModal').modal('show');
    });
    
    // Toggle chapter
    $(document).on('click', '.toggle-chapter', function() {
        const id = $(this).data('id');
        if(confirm('Are you sure you want to change the status of this chapter?')) {
            $.ajax({
                url: `/admin/chapters/${id}/toggle`,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function() {
                    location.reload();
                }
            });
        }
    });
    
    // Delete chapter
    $(document).on('click', '.delete-chapter', function() {
        const id = $(this).data('id');
        if(confirm('Are you sure you want to delete this chapter? This action cannot be undone.')) {
            $.ajax({
                url: `/admin/chapters/${id}`,
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete chapter'));
                }
            });
        }
    });
});
</script>
@endpush
