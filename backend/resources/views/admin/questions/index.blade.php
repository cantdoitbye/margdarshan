@extends('admin.layouts.app')

@section('title', 'Questions Management')
@section('page-title', 'Questions Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">All Questions</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="fas fa-plus me-2"></i>Add Question
        </button>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <select class="form-select" id="filterSubject">
                    <option value="">All Subjects</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">Science</option>
                    <option value="English">English</option>
                    <option value="Social Studies">Social Studies</option>
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
            <div class="col-md-3">
                <select class="form-select" id="filterDifficulty">
                    <option value="">All Difficulties</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
        </div>
        
        <!-- Questions Table -->
        <div class="table-responsive">
            <table id="questionsTable" class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Difficulty</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                    <tr>
                        <td>{{ $question->id }}</td>
                        <td>
                            <div class="text-truncate" style="max-width: 300px;" title="{{ $question->question_text }}">
                                {{ $question->question_text }}
                            </div>
                        </td>
                        <td><span class="badge bg-info">{{ $question->subject }}</span></td>
                        <td><span class="badge bg-secondary">Class {{ $question->class }}</span></td>
                        <td>
                            @if($question->difficulty === 'easy')
                                <span class="badge bg-success">Easy</span>
                            @elseif($question->difficulty === 'medium')
                                <span class="badge bg-warning">Medium</span>
                            @else
                                <span class="badge bg-danger">Hard</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($question->question_type) }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary view-question" data-id="{{ $question->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning edit-question" data-id="{{ $question->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-question" data-id="{{ $question->id }}">
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

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addQuestionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <select class="form-select" name="subject" required>
                            <option value="">Select Subject</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="English">English</option>
                            <option value="Social Studies">Social Studies</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <select class="form-select" name="class" required>
                            <option value="">Select Class</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">Class {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Question Type</label>
                        <select class="form-select" name="question_type" required>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Difficulty</label>
                        <select class="form-select" name="difficulty" required>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea class="form-control" name="question_text" rows="3" required></textarea>
                    </div>
                    
                    <div id="optionsContainer">
                        <label class="form-label">Options</label>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="options[]" placeholder="Option 1" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="options[]" placeholder="Option 2" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="options[]" placeholder="Option 3" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="options[]" placeholder="Option 4" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" name="correct_answer" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Question Modal -->
<div class="modal fade" id="viewQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="questionDetails">
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
    const table = $('#questionsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
    
    // Filter by subject
    $('#filterSubject').on('change', function() {
        table.column(2).search(this.value).draw();
    });
    
    // Filter by class
    $('#filterClass').on('change', function() {
        table.column(3).search(this.value ? 'Class ' + this.value : '').draw();
    });
    
    // Filter by difficulty
    $('#filterDifficulty').on('change', function() {
        table.column(4).search(this.value).draw();
    });
    
    // Add question
    $('#addQuestionForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.questions.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#addQuestionModal').modal('hide');
                showToast('Success', 'Question added successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showToast('Error', 'Failed to add question', 'danger');
            }
        });
    });
    
    // View question
    $(document).on('click', '.view-question', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/questions/${id}`,
            method: 'GET',
            success: function(question) {
                let html = `
                    <div class="mb-3">
                        <strong>Subject:</strong> ${question.subject}<br>
                        <strong>Class:</strong> ${question.class}<br>
                        <strong>Difficulty:</strong> ${question.difficulty}<br>
                        <strong>Type:</strong> ${question.question_type}
                    </div>
                    <div class="mb-3">
                        <strong>Question:</strong><br>
                        ${question.question_text}
                    </div>
                    <div class="mb-3">
                        <strong>Options:</strong><br>
                        <ul>
                            ${question.options.map(opt => `<li>${opt}</li>`).join('')}
                        </ul>
                    </div>
                    <div class="alert alert-success">
                        <strong>Correct Answer:</strong> ${question.correct_answer}
                    </div>
                `;
                $('#questionDetails').html(html);
                $('#viewQuestionModal').modal('show');
            }
        });
    });
    
    // Delete question
    $(document).on('click', '.delete-question', function() {
        if (!confirm('Are you sure you want to delete this question?')) return;
        
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/questions/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                showToast('Success', 'Question deleted successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            },
            error: function() {
                showToast('Error', 'Failed to delete question', 'danger');
            }
        });
    });
});
</script>
@endpush
