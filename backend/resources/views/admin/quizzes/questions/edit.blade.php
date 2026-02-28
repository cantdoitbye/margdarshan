@extends('admin.layouts.app')

@section('title', 'Edit Question')

@section('content')
<!-- Quiz Info -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h5 class="mb-2">Editing Question in: <strong>{{ $quiz->title }}</strong></h5>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-primary">{{ $quiz->class->name }}</span>
            <span class="badge bg-info">{{ $quiz->subject->name }}</span>
            <span class="badge bg-secondary">{{ $quiz->chapter->name }}</span>
        </div>
    </div>
</div>

<!-- Question Form -->
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Edit Question</h5>
            </div>
            
            <form action="{{ route('admin.quizzes.questions.update', [$quiz->id, $question->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- Question Type -->
                    <div class="mb-4">
                        <label class="form-label">Question Type *</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="question_type" id="type_single" value="single_correct" 
                                   {{ old('question_type', $question->question_type) == 'single_correct' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-primary" for="type_single">
                                <i class="fas fa-check-circle me-2"></i>Single Correct
                            </label>
                            
                            <input type="radio" class="btn-check" name="question_type" id="type_multiple" value="multiple_correct"
                                   {{ old('question_type', $question->question_type) == 'multiple_correct' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="type_multiple">
                                <i class="fas fa-check-double me-2"></i>Multiple Correct
                            </label>
                            
                            <input type="radio" class="btn-check" name="question_type" id="type_tf" value="true_false"
                                   {{ old('question_type', $question->question_type) == 'true_false' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="type_tf">
                                <i class="fas fa-toggle-on me-2"></i>True / False
                            </label>
                        </div>
                    </div>
                    
                    <!-- Question Text -->
                    <div class="mb-3">
                        <label class="form-label">Question Text *</label>
                        <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                  name="question_text" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Options -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option A *</label>
                            <input type="text" class="form-control @error('option_a') is-invalid @enderror" 
                                   name="option_a" value="{{ old('option_a', $question->option_a) }}" required>
                            @error('option_a')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option B *</label>
                            <input type="text" class="form-control @error('option_b') is-invalid @enderror" 
                                   name="option_b" value="{{ old('option_b', $question->option_b) }}" required>
                            @error('option_b')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option C</label>
                            <input type="text" class="form-control @error('option_c') is-invalid @enderror" 
                                   name="option_c" value="{{ old('option_c', $question->option_c) }}">
                            @error('option_c')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option D</label>
                            <input type="text" class="form-control @error('option_d') is-invalid @enderror" 
                                   name="option_d" value="{{ old('option_d', $question->option_d) }}">
                            @error('option_d')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Correct Answers -->
                    <div class="mb-3">
                        <label class="form-label">Correct Answer(s) *</label>
                        <div class="d-flex gap-3">
                            @php
                                $correctAnswers = old('correct_answers', $question->correct_answers);
                            @endphp
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="correct_answers[]" value="A" id="correct_a"
                                       {{ is_array($correctAnswers) && in_array('A', $correctAnswers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="correct_a">A</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="correct_answers[]" value="B" id="correct_b"
                                       {{ is_array($correctAnswers) && in_array('B', $correctAnswers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="correct_b">B</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="correct_answers[]" value="C" id="correct_c"
                                       {{ is_array($correctAnswers) && in_array('C', $correctAnswers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="correct_c">C</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="correct_answers[]" value="D" id="correct_d"
                                       {{ is_array($correctAnswers) && in_array('D', $correctAnswers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="correct_d">D</label>
                            </div>
                        </div>
                        @error('correct_answers')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Marks -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marks *</label>
                            <input type="number" step="0.01" class="form-control @error('marks') is-invalid @enderror" 
                                   name="marks" value="{{ old('marks', $question->marks) }}" min="0" required>
                            @error('marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Negative Marks</label>
                            <input type="number" step="0.01" class="form-control @error('negative_marks') is-invalid @enderror" 
                                   name="negative_marks" value="{{ old('negative_marks', $question->negative_marks) }}" min="0">
                            @error('negative_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Explanation -->
                    <div class="mb-3">
                        <label class="form-label">Explanation (Optional)</label>
                        <textarea class="form-control @error('explanation') is-invalid @enderror" 
                                  name="explanation" rows="3" placeholder="Explain the correct answer...">{{ old('explanation', $question->explanation) }}</textarea>
                        @error('explanation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Questions
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Question
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle question type change to adjust correct answer selection
$('input[name="question_type"]').on('change', function() {
    const type = $(this).val();
    const checkboxes = $('input[name="correct_answers[]"]');
    
    if (type === 'single_correct' || type === 'true_false') {
        // Convert to radio-like behavior
        checkboxes.on('change', function() {
            if (this.checked) {
                checkboxes.not(this).prop('checked', false);
            }
        });
    } else {
        // Allow multiple selections
        checkboxes.off('change');
    }
});

// Trigger on page load
$('input[name="question_type"]:checked').trigger('change');
</script>
@endpush
