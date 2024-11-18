$(document).ready(function() {
    $('#purchasesTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });   
});

function toggleCompletion(purchaseId, isChecked) {
    $.ajax({
        url: 'update_completion.php',
        type: 'POST',
        data: {
            purchase_id: purchaseId,
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


  // Handle Add purchase Form Submission
  $('#addPurchaseForm').on('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the default way

    $.ajax({
        url: 'add_purchase.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            try {
                // Parse the JSON response (in case it's returned as a string)
                response = JSON.parse(response);

                if (response.success) {
                    // Hide any existing error messages
                    $('#errorMessage').addClass('d-none');

                    // Show success message
                    $('#successMessage').removeClass('d-none');

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#addPurchaseModal').modal('hide');

                        // Reset the form and hide the success message
                        $('#addPurchaseForm')[0].reset();
                        $('#successMessage').addClass('d-none');

                        // Reload the page to reflect the new purchase
                        location.reload();
                    }, 2000); // Adjust the delay as needed (2 seconds here)

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
        error: function(xhr, status, error) {
            console.error('Error adding purchase:', error);
        }
    });
});

$('.edit-btn').on('click', function() {
    var purchaseId = $(this).data('id'); // Get purchase_id from the button
    console.log("Selected Purchase ID:", purchaseId); // Log the purchase ID for debugging

    // Send an AJAX request to fetch the purchase details using the purchase ID
    $.ajax({
        url: 'get_purchase_details.php', // PHP file to fetch purchase data
        type: 'POST',
        data: { purchase_id: purchaseId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Populate the form with purchase data
                
                $('#editPurchaseTitle').val(response.data.title);  
                
                // Show the modal
                $('#editPurchaseModal').modal('show');
            } else {
                console.log("Error fetching data: ", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
});


// Handle Edit Event Form Submission
$('#editPurchaseForm').on('submit', function(event) {
  event.preventDefault(); // Prevent the form from submitting the default way

  // Get the form data
  var formData = $(this).serialize();

  $.ajax({
      url: 'update_purchase.php', // PHP script that handles updating the purchase in the database
      type: 'POST',
      data: formData,  // Send form data to update_purchase.php
      success: function(response) {
          try {
              // Parse the JSON response (ensure it's valid JSON)
              response = JSON.parse(response);

              if (response.success) {
                  // Hide any existing error messages
                  $('#errorMessage').addClass('d-none');

                  // Show success message
                  $('#successMessage').removeClass('d-none').text(response.message);

                  // Close the modal after a short delay
                  setTimeout(function() {
                      $('#editPurchaseModal').modal('hide'); // Hide the modal
                      location.reload();                   
                  }, 2000); // Adjust delay as needed (e.g., 2 seconds)
              } else {
                  // Show validation errors
                  $('#errorMessage').removeClass('d-none').html(response.errors);
              }
          } catch (error) {
              console.error('Error parsing JSON response:', error);
          }
      },
      error: function(xhr, status, error) {
          console.error('Error updating Purchase:', error);
      }
  });
});

// Purchase delegation for dynamically loaded buttons (Archive)

$(document).on('click', '.archive-btn', function() {
    var purchaseId = $(this).data('id'); // Get purchase_id from the button

    // Debugging: Log the button and purchaseId
    console.log("Archive button clicked:", this);
    console.log("Extracted Purchase ID:", purchaseId);

    // Check if purchaseId exists
    if (purchaseId) {
        $('#archivePurchaseId').val(purchaseId); // Store the Purchase ID in the hidden input field
        $('#archiveModal').modal('show'); // Show the archive confirmation modal
    } else {
        console.error("Error: No purchase ID found on the archive button.");
    }
});


// Handle archive confirmation when "Archive" button in modal is clicked
$(document).on('click', '#confirmArchiveBtn', function() {
    var purchaseId = $('#archivePurchaseId').val(); // Get the Purchase ID from the hidden input field
    
    // Confirm purchase ID is set before AJAX
    if (!purchaseId) {
        console.error("Error: Purchase ID not set in hidden input field.");
        return;
    }

    // Send an AJAX request to archive the purchase
    $.ajax({
        url: 'archive_purchase.php', // PHP file to handle archiving
        type: 'POST',
        data: { purchase_id: purchaseId },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                console.log("Purchase archived successfully!");
                location.reload();
            } else {
                // Show error message if archiving fails
                console.error("Error archiving purchase:", response.message || "Unknown error.");
                alert("Error archiving purchase: " + (response.message || "Unknown error."));
            }
            // Close the modal after archiving
            $('#archiveModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error, xhr.responseText);
            alert("AJAX request failed: " + error);
        }
    });
});
