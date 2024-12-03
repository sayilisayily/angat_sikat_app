$(document).ready(function() {
    $('#budgetApprovalsArchive').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });
});

$(document).on('click', '.recover-btn', function () {
    var approvalId = $(this).data('id'); // Get the approval_id from the button's data-id attribute
    $('#recover_approval_id').val(approvalId); // Set the value of the hidden input
});

$(document).on('click', '.delete-btn', function () {
    var approvalId = $(this).data('id'); // Get the approval_id from the button's data-id attribute
    $('#delete_approval_id').val(approvalId); // Set the value of the hidden input
});

$('#confirmRecoverBtn').on('click', function() {
    var approvalId = $('#recover_approval_id').val(); // Get the item ID from the hidden input field
    
    // Send an AJAX request to delete the item
    $.ajax({
        url: 'recover_approval.php', // PHP file to handle deletion
        type: 'POST',
        data: { approval_id: approvalId },
        dataType: 'json',
        success: function(response) {
            try { 
                if (response.success) {
                    // Show success message (optional)
                    console.log(response.message);
                    
                    // Hide any existing error messages
                    $('#recoverErrorMessage').addClass('d-none');

                    // Show success message
                    $('#recoverSuccessMessage').removeClass('d-none').text(response.message);

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#recoverModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#recoverSuccessMessage').addClass('d-none');
                        location.reload(); 
                    }, 2000);
                } else {
                    // Show validation errors
                    $('#recoverSuccessMessage').addClass('d-none');

                    $('#recoverErrorMessage').removeClass('d-none');
                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`;
                    }
                    $('#recoverErrorList').html(errorHtml);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting item:', error);
            console.log(xhr.responseText);
        }
    });
});

$('#confirmDeleteBtn').on('click', function() {
    var approvalId = $('#delete_approval_id').val(); // Get the item ID from the hidden input field
    
    // Send an AJAX request to delete the item
    $.ajax({
        url: 'delete_approval.php', // PHP file to handle deletion
        type: 'POST',
        data: { approval_id: approvalId },
        dataType: 'json',
        success: function(response) {
            try { 
                if (response.success) {
                    // Show success message (optional)
                    console.log(response.message);
                    
                    // Hide any existing error messages
                    $('#deleteErrorMessage').addClass('d-none');

                    // Show success message
                    $('#deleteSuccessMessage').removeClass('d-none').text(response.message);

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#deleteModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#deleteSuccessMessage').addClass('d-none');
                        location.reload(); 
                    }, 2000);
                } else {
                    // Show validation errors
                    $('#deleteSuccessMessage').addClass('d-none');

                    $('#deleteErrorMessage').removeClass('d-none');
                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`;
                    }
                    $('#deleteErrorList').html(errorHtml);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting item:', error);
            console.log(xhr.responseText);
        }
    });
});