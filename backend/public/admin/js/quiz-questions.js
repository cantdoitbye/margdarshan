// Drag & Drop Question Reordering
$(document).ready(function() {
    const tbody = document.getElementById('questions-tbody');
    
    if (tbody && typeof Sortable !== 'undefined') {
        const sortable = new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                // Get new order
                const order = [];
                const rows = document.querySelectorAll('.question-row');
                
                rows.forEach((row, index) => {
                    order.push({
                        id: parseInt(row.dataset.questionId),
                        sort_order: index + 1
                    });
                    
                    // Update question number display
                    row.querySelector('td:nth-child(2) strong').textContent = index + 1;
                });
                
                // Send AJAX request to update order
                $.ajax({
                    url: `/admin/quizzes/${quizId}/questions/reorder`,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({ order: order }),
                    success: function(response) {
                        showToast('Success', 'Question order updated successfully', 'success');
                    },
                    error: function(xhr) {
                        showToast('Error', 'Failed to update order. Please refresh the page.', 'danger');
                        console.error('Reorder error:', xhr.responseJSON);
                    }
                });
            }
        });
    }
});

// Toast notification function (if not already in admin.js)
function showToast(title, message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const container = $('#toast-container');
    container.append(toastHtml);
    
    const toastElement = container.find('.toast').last();
    const toast = new bootstrap.Toast(toastElement[0], { delay: 3000 });
    toast.show();
    
    // Remove toast after it's hidden
    toastElement.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
