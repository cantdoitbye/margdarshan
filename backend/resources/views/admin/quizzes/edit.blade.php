@extends('admin.layouts.app')

@section('title', 'Edit Quiz')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Edit Quiz</h5>
            </div>
            
            <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Quiz Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               name="title" value="{{ old('title', $quiz->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Class *</label>
                            <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $quiz->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Subject *</label>
                            <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $quiz->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Chapter *</label>
                            <select name="chapter_id" id="chapter_id" class="form-select @error('chapter_id') is-invalid @enderror" required>
                                <option value="">Select Chapter</option>
                                @foreach($chapters as $chapter)
                                    <option value="{{ $chapter->id }}" {{ old('chapter_id', $quiz->chapter_id) == $chapter->id ? 'selected' : '' }}>
                                        {{ $chapter->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chapter_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label d-block">Difficulty Level *</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="difficulty_level" id="diff_easy" value="easy" {{ old('difficulty_level', $quiz->difficulty_level) == 'easy' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-success" for="diff_easy">
                                <i class="fas fa-circle text-success me-2"></i>Easy
                            </label>
                            
                            <input type="radio" class="btn-check" name="difficulty_level" id="diff_medium" value="medium" {{ old('difficulty_level', $quiz->difficulty_level) == 'medium' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="diff_medium">
                                <i class="fas fa-circle text-warning me-2"></i>Medium
                            </label>
                            
                            <input type="radio" class="btn-check" name="difficulty_level" id="diff_hard" value="hard" {{ old('difficulty_level', $quiz->difficulty_level) == 'hard' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger" for="diff_hard">
                                <i class="fas fa-circle text-danger me-2"></i>Hard
                            </label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Time Limit (minutes) *</label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                   name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="1" required>
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status', $quiz->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $quiz->status) == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This quiz has <strong>{{ $quiz->total_questions }}</strong> questions.
                        <a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}" class="alert-link">Manage Questions</a>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/js/quiz-form.js') }}"></script>
<script>
// Pre-populate subject and chapter dropdowns on edit
$(document).ready(function() {
    const classId = $('#class_id').val();
    const subjectId = '{{ old('subject_id', $quiz->subject_id) }}';
    const chapterId = '{{ old('chapter_id', $quiz->chapter_id) }}';
    
    if (classId && subjectId) {
        // Subject dropdown is already populated from server
        $('#subject_id').prop('disabled', false);
        
        // Load chapters
        if (subjectId) {
            loadChapters(classId, subjectId, chapterId);
        }
    }
});
</script>
@endpush
