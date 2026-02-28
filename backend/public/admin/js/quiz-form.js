// Cascading Dropdowns for Quiz Form
$(document).ready(function() {
    const classSelect = $('#class_id');
    const subjectSelect = $('#subject_id');
    const chapterSelect = $('#chapter_id');
    
    // When class changes, load subjects
    classSelect.on('change', function() {
        const classId = $(this).val();
        
        // Reset dependent dropdowns
        subjectSelect.html('<option value="">Select Subject</option>').prop('disabled', true);
        chapterSelect.html('<option value="">Select Chapter</option>').prop('disabled', true);
        
        if (classId) {
            // Show loading
            subjectSelect.html('<option value="">Loading...</option>');
            
            // Fetch subjects for this class via class-subject pivot
            $.ajax({
                url: `/admin/api/subjects-by-class/${classId}`,
                method: 'GET',
                success: function(subjects) {
                    subjectSelect.html('<option value="">Select Subject</option>');
                    subjects.forEach(function(subject) {
                        subjectSelect.append(`<option value="${subject.id}">${subject.name}</option>`);
                    });
                    subjectSelect.prop('disabled', false);
                },
                error: function() {
                    subjectSelect.html('<option value="">Error loading subjects</option>');
                }
            });
        }
    });
    
    // When subject changes, load chapters
    subjectSelect.on('change', function() {
        const classId = classSelect.val();
        const subjectId = $(this).val();
        
        // Reset chapter dropdown
        chapterSelect.html('<option value="">Select Chapter</option>').prop('disabled', true);
        
        if (classId && subjectId) {
            loadChapters(classId, subjectId);
        }
    });
});

// Function to load chapters (can be called from edit page too)
function loadChapters(classId, subjectId, selectedChapterId = null) {
    const chapterSelect = $('#chapter_id');
    
    // Show loading
    chapterSelect.html('<option value="">Loading...</option>');
    
    // Fetch chapters for this class + subject
    $.ajax({
        url: '/admin/chapters/by-class-subject',
        method: 'GET',
        data: {
            class_id: classId,
            subject_id: subjectId
        },
        success: function(chapters) {
            chapterSelect.html('<option value="">Select Chapter</option>');
            chapters.forEach(function(chapter) {
                const selected = selectedChapterId && chapter.id == selectedChapterId ? 'selected' : '';
                chapterSelect.append(`<option value="${chapter.id}" ${selected}>${chapter.name}</option>`);
            });
            chapterSelect.prop('disabled', false);
        },
        error: function() {
            chapterSelect.html('<option value="">Error loading chapters</option>');
        }
    });
}

// API endpoint for subjects by class (needs to be added to routes)
// This will use the class-subject pivot table
