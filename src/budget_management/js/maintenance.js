$(document).ready(function() {
    $('#maintenanceTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });   
});

function toggleCompletion(maintenanceId, isChecked) {
    $.ajax({
        url: 'update_completion.php',
        type: 'POST',
        data: {
            maintenance_id: maintenanceId,
            completion_status: isChecked ? 1 : 0
        },
        success: function(response) {
            console.log('Completion status updated successfully:', response);
        },
        error: function(xhr, status, error) {
            console.error('Error updating completion status:', error);
        }
    });
}