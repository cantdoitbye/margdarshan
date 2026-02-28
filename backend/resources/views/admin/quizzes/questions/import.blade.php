@extends('admin.layouts.app')

@section('title', 'Import Questions')
@section('page-title', 'Import Questions')

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}">Questions</a></li>
            <li class="breadcrumb-item active">Import</li>
        </ol>
    </nav>
</div>

<!-- Quiz Info Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">{{ $quiz->title }}</h4>
                <div class="text-muted">
                    <span class="me-3">
                        <i class="fas fa-graduation-cap me-1"></i>{{ $quiz->class->name }}
                    </span>
                    <span class="me-3">
                        <i class="fas fa-book me-1"></i>{{ $quiz->subject->name }}
                    </span>
                    <span class="me-3">
                        <i class="fas fa-bookmark me-1"></i>{{ $quiz->chapter->name }}
                    </span>
                    <span class="badge {{ $quiz->difficulty_badge_class }}">
                        {{ $quiz->difficulty_icon }} {{ ucfirst($quiz->difficulty_level) }}
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="text-muted small">Current Questions</div>
                <h3 class="mb-0">{{ $quiz->total_questions }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Import Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-file-upload me-2 text-primary"></i>Upload Questions File
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.quizzes.questions.import.process', $quiz->id) }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select File *</label>
                        <input type="file" class="form-control form-control-lg" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Accepted formats: Excel (.xlsx, .xls) or CSV (.csv) | Max size: 2 MB
                        </div>
                    </div>

                    <div id="filePreview" class="alert alert-info d-none mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-excel fa-2x me-3"></i>
                            <div>
                                <strong id="fileName"></strong>
                                <div class="small text-muted" id="fileSize"></div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Important Notes
                        </h6>
                        <ul class="mb-0 small">
                            <li>Questions will be added to the existing questions in this quiz</li>
                            <li>Invalid rows will be skipped, valid rows will be imported</li>
                            <li>Make sure your file follows the template format</li>
                            <li>Questions will be added with auto-incrementing order</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="importBtn">
                            <i class="fas fa-upload me-2"></i>Import Questions
                        </button>
                        <a href="{{ route('admin.quizzes.questions.index', $quiz->id) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions & Template -->
    <div class="col-lg-4">
        <!-- Download Template -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-download me-2"></i>Download Template
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-3">Get the sample template with example questions to understand the format.</p>
                <a href="{{ route('admin.quizzes.questions.template') }}" class="btn btn-success w-100" download>
                    <i class="fas fa-file-download me-2"></i>Download Template CSV
                </a>
            </div>
        </div>

        <!-- Quick Guide -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-question-circle me-2 text-info"></i>Quick Guide
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold small text-uppercase text-muted">Question Types</h6>
                    <ul class="small mb-0">
                        <li><code>single_correct</code> - One correct answer</li>
                        <li><code>multiple_correct</code> - Multiple correct answers</li>
                        <li><code>true_false</code> - True/False questions</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold small text-uppercase text-muted">Correct Answers Format</h6>
                    <ul class="small mb-0">
                        <li>Single: <code>A</code> or <code>B</code></li>
                        <li>Multiple: <code>A,B</code> or <code>A,C,D</code></li>
                        <li>Case insensitive: <code>a</code> → <code>A</code></li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold small text-uppercase text-muted">Required Columns</h6>
                    <ul class="small mb-0">
                        <li>question_type</li>
                        <li>question_text</li>
                        <li>option_a, option_b</li>
                        <li>correct_answers</li>
                    </ul>
                </div>

                <div>
                    <h6 class="fw-bold small text-uppercase text-muted">Optional Columns</h6>
                    <ul class="small mb-0">
                        <li>option_c, option_d</li>
                        <li>marks (default: 1)</li>
                        <li>negative_marks (default: 0)</li>
                        <li>explanation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sample Format Card -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-table me-2 text-success"></i>Sample Format
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>question_type</th>
                        <th>question_text</th>
                        <th>option_a</th>
                        <th>option_b</th>
                        <th>option_c</th>
                        <th>option_d</th>
                        <th>correct_answers</th>
                        <th>marks</th>
                        <th>negative_marks</th>
                        <th>explanation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>single_correct</code></td>
                        <td>What is 2 + 2?</td>
                        <td>3</td>
                        <td>4</td>
                        <td>5</td>
                        <td>6</td>
                        <td><code>B</code></td>
                        <td>1</td>
                        <td>0.25</td>
                        <td>Simple addition</td>
                    </tr>
                    <tr>
                        <td><code>multiple_correct</code></td>
                        <td>Which are even?</td>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                        <td>5</td>
                        <td><code>A,C</code></td>
                        <td>2</td>
                        <td>0.5</td>
                        <td>Divisible by 2</td>
                    </tr>
                    <tr>
                        <td><code>true_false</code></td>
                        <td>Earth is flat?</td>
                        <td>True</td>
                        <td>False</td>
                        <td></td>
                        <td></td>
                        <td><code>B</code></td>
                        <td>1</td>
                        <td>0</td>
                        <td>Earth is spherical</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // File input preview
    $('#fileInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            $('#fileName').text(file.name);
            $('#fileSize').text((file.size / 1024).toFixed(2) + ' KB');
            $('#filePreview').removeClass('d-none');
        } else {
            $('#filePreview').addClass('d-none');
        }
    });

    // Form submission
    $('#importForm').on('submit', function(e) {
        const fileInput = $('#fileInput')[0];
        const file = fileInput.files[0];
        
        // Validate file
        if (!file) {
            e.preventDefault();
            showToast('Error', 'Please select a file to import', 'danger');
            return false;
        }

        // Check file size (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            e.preventDefault();
            showToast('Error', 'File size exceeds 2 MB limit', 'danger');
            return false;
        }

        // Check file extension
        const allowedExtensions = ['xlsx', 'xls', 'csv'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            e.preventDefault();
            showToast('Error', 'Invalid file format. Please upload .xlsx, .xls, or .csv file', 'danger');
            return false;
        }

        // Show loading state
        $('#importBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Importing...');
    });
});
</script>
@endpush
