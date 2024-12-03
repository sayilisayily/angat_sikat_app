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

// Global variables to store event details
let maintenanceIdToUpdate;
let newStatus;

// Show confirmation modal and store event details
function showConfirmationModal(maintenanceId, isChecked) {
    maintenanceIdToUpdate = maintenanceId;  // Store the maintenance ID
    newStatus = isChecked ? 1 : 0;  // Store the new completion status

    // Show the confirmation modal
    $('#confirmationModal').modal('show');
}

// Handle confirmation when "Confirm" button in modal is clicked
$('#confirmUpdateBtn').on('click', function() {
    // Get event ID and new status from global variables
    var maintenanceId = maintenanceIdToUpdate;
    var status = newStatus;

    // Send an AJAX request to update the completion status
    $.ajax({
        url: 'm_update_completion.php', // PHP file to handle status update
        type: 'POST',
        data: {
            maintenance_id: maintenanceId,
            completion_status: status
        },
        dataType: 'json',
        success: function(response) {
            try {
                if (response.success) {
                    // Show success message
                    $('#successMessage').removeClass('d-none').text(response.message);
                    // Hide any existing error messages
                    $('#errorMessage').addClass('d-none');

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#confirmationModal').modal('hide');
                        // Optionally, you can reload the page or update the table if necessary
                        location.reload(); // or update the checkbox or table directly
                    }, 2000);
                } else {
                    // Show validation errors
                    $('#successMessage').addClass('d-none');
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
        error: function(xhr, status, error) {
            console.error('Error updating accomplishment status:', error);
            console.log(xhr.responseText);
        }
    });
});

// Reset modal and close when the cancel button is clicked
$('#confirmationModal .btn-secondary').on('click', function() {
    // Hide any error or success messages
    $('#successMessage').addClass('d-none');
    $('#errorMessage').addClass('d-none');

    setTimeout(function() {
        // Optionally, you can reload the page or update the table if necessary
        location.reload(); // or update the checkbox or table directly
    }, 500);
});

    // Add an event listener to the title selector dropdown
    document.getElementById("title").addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption && selectedOption.value) {
            // Extract data from the selected option
            const planId = selectedOption.getAttribute("data-plan-id") || "";
            const amount = selectedOption.getAttribute("data-amount") || "";

            // Populate the modal fields
            document.getElementById("plan_id").value = planId;
            document.getElementById("amount").value = amount;
        } else {
            // Clear the fields if no title is selected
            document.getElementById("plan_id").value = "";
            document.getElementById("amount").value = "";
        }
    });
    
// Handle Add Maintenance Form Submission
$('#addForm').on('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the default way

    $.ajax({
        url: 'add_maintenance.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            try {
                // Parse the JSON response (in case it's returned as a string)
                response = JSON.parse(response);

                if (response.success) {
                    // Hide any existing error messages
                    $('#addErrorMessage').addClass('d-none');

                    // Show success message
                    $('#addSuccessMessage').removeClass('d-none');

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#addModal').modal('hide');

                        // Reset the form and hide the success message
                        $('#addForm')[0].reset();
                        $('#addsuccessMessage').addClass('d-none');

                        // Reload the page to reflect the new Maintenance
                        location.reload();
                    }, 2000); // Adjust the delay as needed (2 seconds here)

                } else {
                    // Hide any existing success messages
                    $('#addSuccessMessage').addClass('d-none');

                    // Show error messages
                    $('#addErrorMessage').removeClass('d-none');
                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`;
                    }
                    $('#addErrorList').html(errorHtml);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding Maintenance:', error);
        }
    });
});

$('.edit-btn').on('click', function() {
    var maintenanceId = $(this).data('id');
    console.log("Selected MOE ID:", maintenanceId);

    $.ajax({
        url: 'get_maintenance_details.php',
        type: 'POST',
        data: { maintenance_id: maintenanceId },
        dataType: 'json',
        success: function(response) {
            console.log("AJAX Response:", response); // Log response
            if (response.success) {
                $('#editMaintenanceId').val(response.data.maintenance_id);
                $('#editMaintenanceTitle').val(response.data.title);
                $('#editModal').modal('show');
            } else {
                console.error("Error fetching data:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
});

// Handle Edit Maintenance Form Submission
$('#editForm').on('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    $.ajax({
        url: 'update_maintenance.php', // PHP script for updating the Maintenance
        type: 'POST',
        data: $(this).serialize(), // Serialize form data
        success: function (response) {
            try {
                // Parse the JSON response
                response = JSON.parse(response);
                console.log(response);

                if (response.success) {
                    // Hide error messages if present
                    $('#editErrorMessage').addClass('d-none');

                    // Show success message
                    $('#editSuccessMessage').removeClass('d-none').text(response.message);

                    // Close the modal and reload the page
                    setTimeout(function () {
                        $('#editModal').modal('hide'); // Hide modal
                        $('#editForm')[0].reset();    // Reset the form
                        $('#editSuccessMessage').addClass('d-none'); // Hide success message
                        location.reload(); // Reload page
                    }, 2000);
                } else {
                    // Show validation errors
                    $('#editSuccessMessage').addClass('d-none'); // Hide success message
                    $('#editErrorMessage').removeClass('d-none'); // Show error messages

                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`; // Build error list
                    }
                    $('#editErrorList').html(errorHtml); // Display errors
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error updating maintenance:', error);
            console.log(xhr.responseText); // Log response for debugging
        },
    });
});

// Maintenance delegation for dynamically loaded buttons (Archive)

$(document).on('click', '.archive-btn', function() {
    var maintenanceId = $(this).data('id'); // Get maintenance_id from the button

    // Debugging: Log the button and maintenanceId
    console.log("Archive button clicked:", this);
    console.log("Extracted Maintenance ID:", maintenanceId);

    // Check if maintenanceId exists
    if (maintenanceId) {
        $('#archiveMaintenanceId').val(maintenanceId); // Store the maintenance ID in the hidden input field
        $('#archiveModal').modal('show'); // Show the archive confirmation modal
    } else {
        console.error("Error: No maintenance ID found on the archive button.");
    }
});


// Handle archive confirmation when "Archive" button in modal is clicked
$(document).on('click', '#confirmArchiveBtn', function() {
    var maintenanceId = $('#archiveMaintenanceId').val(); // Get the maintenance ID from the hidden input field
    
    // Confirm maintenance ID is set before AJAX
    if (!maintenanceId) {
        console.error("Error: Maintenance ID not set in hidden input field.");
        return;
    }

    // Send an AJAX request to archive the maintenance
    $.ajax({
        url: 'archive_maintenance.php', // PHP file to handle archiving
        type: 'POST',
        data: { maintenance_id: maintenanceId },
        dataType: 'json',
        success: function(response) {
            try { 
                if (response.success) {
                  // Show success message (optional)
                  console.log(response.message);
                    // Hide any existing error messages
                    $('#archiveErrorMessage').addClass('d-none');

                    // Show success message
                    $('#archiveSuccessMessage').removeClass('d-none');

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#archiveModal').modal('hide'); 
                        $('#archiveSuccessMessage').addClass('d-none');
                        location.reload(); 
                    }, 2000);
              } else {
                  // Show validation errors
                  $('#archiveSuccessMessage').addClass('d-none');

                  $('#archiveErrorMessage').removeClass('d-none');
                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`;
                    }
                    $('#archiveErrorList').html(errorHtml);
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
