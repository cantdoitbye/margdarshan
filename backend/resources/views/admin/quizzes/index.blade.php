@extends('admin.layouts.app')

@section('title', 'Quizzes Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Quizzes Management</h5>
        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Quiz
        </a>
    </div>
    
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-2">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Difficulty</label>
                <select name="difficulty_level" class="form-select">
                    <option value="">All Difficulties</option>
                    <option value="easy" {{ request('difficulty_level') == 'easy' ? 'selected' : '' }}>🟢 Easy</option>
                    <option value="medium" {{ request('difficulty_level') == 'medium' ? 'selected' : '' }}>🟠 Medium</option>
                    <option value="hard" {{ request('difficulty_level') == 'hard' ? 'selected' : '' }}>🔴 Hard</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Chapter</th>
                        <th>Difficulty</th>
                        <th>Questions</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                    <tr>
                        <td>{{ $quiz->id }}</td>
                        <td><strong>{{ $quiz->title }}</strong></td>
                        <td><span class="badge bg-primary">{{ $quiz->class->name }}</span></td>
                        <td><span class="badge bg-info">{{ $quiz->subject->name }}</span></td>
                        <td><small>{{ $quiz->chapter->name }}</small></td>
                        <td>
                            <span class="badge {{ $quiz->difficulty_badge_class }}">
                                {{ $quiz->difficulty_icon }} {{ ucfirst($quiz->difficulty_level) }}
                            </span>
                        </td>
                        <td><span class="badge bg-secondary">{{ $quiz->total_questions }}</span></td>
                        <td>{{ $quiz->time_limit }} min</td>
                        <td>
                            <span class="badge {{ $quiz->status_badge_class }}">
                                {{ ucfirst($quiz->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}" class="btn btn-outline-primary" title="Manage Questions">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($quiz->status == 'draft')
                                <form action="{{ route('admin.quizzes.publish', $quiz->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Publish" onclick="return confirm('Publish this quiz?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.quizzes.unpublish', $quiz->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning" title="Unpublish">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.quizzes.duplicate', $quiz->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info" title="Duplicate" onclick="return confirm('Duplicate this quiz?')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this quiz? This will also delete all questions.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-clipboard-question fa-3x mb-3 d-block"></i>
                            No quizzes found. Create your first quiz!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $quizzes->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge-success { background-color: #d4edda !important; color: #155724 !important; }
.badge-warning { background-color: #fff3cd !important; color: #856404 !important; }
.badge-danger { background-color: #f8d7da !important; color: #721c24 !important; }
</style>
@endpush
