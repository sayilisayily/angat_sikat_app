$(document).ready(function () {
    $('#budgetApprovalsTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });
});

// Add an event listener to the title selector dropdown
document.getElementById("title").addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];

    if (selectedOption && selectedOption.value) {
        // Extract data from the selected option
        const id = selectedOption.getAttribute("data-id") || "";

        // Populate the modal fields
        document.getElementById("id").value = id;
    } else {
        // Clear the fields if no title is selected
        document.getElementById("id").value = "";
    }
});


// Add Budget Approval Form Submission via AJAX
$('#addBudgetApprovalForm').on('submit', function (e) {
    e.preventDefault();

    // Create FormData object to include file uploads
    let formData = new FormData(this);

    $.ajax({
        url: 'add_budget_approval.php', // Add form submission PHP file
        type: 'POST',
        data: formData, // Use formData object
        contentType: false, // Important for file upload
        processData: false, // Important for file upload
        success: function (response) {
            try {
                response = JSON.parse(response);
                if (response.success) {
                    // Hide any existing error messages
                    $('#errorMessage').addClass('d-none');

                    // Show success message
                    $('#successMessage').removeClass('d-none');

                    setTimeout(function () {
                        $('#budgetApprovalModal').modal('hide'); // Hide modal after success

                        // Reset the form and hide the success message
                        $('#addBudgetApprovalForm')[0].reset();
                        $('#successMessage').addClass('d-none');

                        location.reload();
                    }, 2000); // Reload after 2 seconds
                } else {
                    // Hide any existing success messages
                    $('#successMessage').addClass('d-none');

                    // Show error messages
                    $('#errorMessage').removeClass('d-none');
                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`;
                    }
                    $('#errorList').html(errorHtml);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error adding event:', error);
        }
    });
});


$(document).on('click', '.edit-btn', function () {
    var approvalId = $(this).data('id');

    // Use AJAX to get the budget approval data
    $.ajax({
        url: 'get_budget_approval.php',  // Modify to match your actual PHP file path
        type: 'POST',
        data: { approval_id: approvalId },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Populate the form fields in the modal
                $('#editApprovalId').val(approvalId);
                $('#editTitle').val(response.title);
                $('#currentAttachment').html('<strong>Current Attachment:</strong> ' + response.attachment);

                // Show the modal
                $('#editBudgetApprovalModal').modal('show');
            } else {
                alert('Failed to fetch data for editing.');
            }
        },
        error: function () {
            alert('Error occurred while fetching budget approval data.');
        }
    });
});

$('#editBudgetApprovalForm').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'update_budget_approval.php', // Edit form submission PHP file
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            var result = JSON.parse(response);
            if (result.success) {
                $('#editMessage').removeClass('d-none alert-danger').addClass('alert-success').text(result.message);
                setTimeout(function () {
                    $('#editBudgetApprovalModal').modal('hide'); // Hide modal after success
                    location.reload(); // Reload the page
                }, 2000); // Reload after 2 seconds
            } else {
                $('#editMessage').removeClass('d-none alert-success').addClass('alert-danger').text(result.message);
            }
        },
        error: function () {
            $('#editMessage').removeClass('d-none alert-success').addClass('alert-danger').text('Error submitting form.');
        }
    });
});

// Event delegation for dynamically loaded archive buttons (for budget approval)
$(document).on('click', '.archive-btn', function () {
    var budgetApprovalId = $(this).data('id'); // Get the budget approval ID from the button
    $('#archiveBudgetApprovalId').val(budgetApprovalId); // Store the ID in the hidden input field
    $('#archiveModal').modal('show'); // Show the archive confirmation modal
    console.log('Selected Event ID: ' + budgetApprovalId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$('#confirmArchiveBtn').on('click', function() {
    var budgetApprovalId = $('#archiveBudgetApprovalId').val(); // Get the event ID from the hidden input field
    
    // Send an AJAX request to archive the event
    $.ajax({
        url: 'archive_budget_approval.php', // PHP file to handle archiving
        type: 'POST',
        data: { approval_id: budgetApprovalId },
        dataType: 'json',
        success: function(response) {
            try { 
              if (response.success) {
                // Show success message (optional)
                console.log(response.message);
                  // Hide any existing error messages
                  $('#errorMessage3').addClass('d-none');

                  // Show success message
                  $('#successMessage3').removeClass('d-none');

                  // Close the modal after a short delay
                  setTimeout(function() {
                      $('#archiveModal').modal('hide'); 
                      $('#successMessage3').addClass('d-none');
                      location.reload(); 
                  }, 2000);
            } else {
                // Show validation errors
                $('#editsuccessMessage3').addClass('d-none');

                $('#errorMessage3').removeClass('d-none');
                  let errorHtml = '';
                  for (let field in response.errors) {
                      errorHtml += `<li>${response.errors[field]}</li>`;
                  }
                  $('#errorList3').html(errorHtml);
              }
          } catch (error) {
              console.error('Error parsing JSON:', error);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error archiving event:', error);
          console.log(xhr.responseText);
        }
    });
});