@extends('admin.layouts.app')

@section('title', 'Manage Questions')

@section('content')
<!-- Quiz Info Header -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">{{ $quiz->title }}</h4>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="badge bg-primary">{{ $quiz->class->name }}</span>
                    <span class="badge bg-info">{{ $quiz->subject->name }}</span>
                    <span class="badge bg-secondary">{{ $quiz->chapter->name }}</span>
                    <span class="badge {{ $quiz->difficulty_badge_class }}">
                        {{ $quiz->difficulty_icon }} {{ ucfirst($quiz->difficulty_level) }}
                    </span>
                    <span class="badge {{ $quiz->status_badge_class }}">
                        {{ ucfirst($quiz->status) }}
                    </span>
                    <span class="badge bg-dark">{{ $quiz->time_limit }} minutes</span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Quizzes
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Questions List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            Questions 
            <span class="badge bg-primary">{{ $quiz->questions->count() }} total</span>
        </h5>
        <a href="{{ route('admin.quizzes.questions.create', $quiz->id) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Question
        </a>
    </div>
    
    <div class="card-body">
        @if($quiz->questions->count() > 0)
        <div class="alert alert-info">
            <i class="fas fa-hand-pointer me-2"></i>
            <strong>Tip:</strong> Drag the <i class="fas fa-grip-vertical"></i> handle to reorder questions
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="30"></th>
                        <th width="50">#</th>
                        <th>Question</th>
                        <th width="120">Type</th>
                        <th width="80">Marks</th>
                        <th width="80">-ve Marks</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody id="questions-tbody">
                    @foreach($quiz->questions as $index => $question)
                    <tr class="question-row" data-question-id="{{ $question->id }}">
                        <td class="text-center">
                            <i class="fas fa-grip-vertical drag-handle text-muted" style="cursor: move;"></i>
                        </td>
                        <td><strong>{{ $index + 1 }}</strong></td>
                        <td>
                            <div class="question-text">
                                {{ Str::limit($question->question_text, 100) }}
                            </div>
                            <small class="text-muted">
                                Correct: <strong>{{ $question->correct_answers_display }}</strong>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $question->question_type_display }}</span>
                        </td>
                        <td><span class="badge bg-success">+{{ $question->marks }}</span></td>
                        <td>
                            @if($question->negative_marks > 0)
                                <span class="badge bg-danger">-{{ $question->negative_marks }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" 
                                   class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete"
                                            onclick="return confirm('Delete this question?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-question-circle fa-4x mb-3 d-block"></i>
            <h5>No questions yet</h5>
            <p>Start adding questions to this quiz</p>
            <a href="{{ route('admin.quizzes.questions.create', $quiz->id) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add First Question
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.drag-handle {
    cursor: move;
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}

.sortable-ghost {
    opacity: 0.4;
    background: #f0f0f0;
}

.question-row:hover {
    background-color: #f8f9fa;
}

.question-text {
    font-size: 0.95rem;
    line-height: 1.4;
}
</style>
@endpush

@push('scripts')
<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="{{ asset('admin/js/quiz-questions.js') }}"></script>
<script>
// Initialize drag & drop
const quizId = {{ $quiz->id }};
</script>
@endpush
