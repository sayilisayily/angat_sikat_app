$(document).ready(function() {
    $('#eventsTable').DataTable({
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
let eventIdToUpdate;
let newStatus;

// Show confirmation modal and store event details
function showConfirmationModal(eventId, isChecked) {
    eventIdToUpdate = eventId;  // Store the event ID
    newStatus = isChecked ? 1 : 0;  // Store the new accomplishment status

    // Show the confirmation modal
    $('#confirmationModal').modal('show');
}

// Handle confirmation when "Confirm" button in modal is clicked
$('#confirmUpdateBtn').on('click', function() {
    // Get event ID and new status from global variables
    var eventId = eventIdToUpdate;
    var status = newStatus;

    // Send an AJAX request to update the accomplishment status
    $.ajax({
        url: 'update_accomplishment.php', // PHP file to handle status update
        type: 'POST',
        data: {
            event_id: eventId,
            accomplishment_status: status
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

// function toggleAccomplishment(eventId, isChecked) {
//     $.ajax({
//         url: 'update_accomplishment.php',
//         type: 'POST',
//         data: {
//             event_id: eventId,
//             accomplishment_status: isChecked ? 1 : 0
//         },
//         success: function(response) {
//             console.log('Accomplishment status updated successfully:', response);
//         },
//         error: function(xhr, status, error) {
//             console.error('Error updating accomplishment status:', error);
//         }
//     });
// }


  // Handle Add Event Form Submission
  $('#addEventForm').on('submit', function(event) {
      event.preventDefault(); 

      $.ajax({
          url: 'add_event.php',
          type: 'POST',
          data: $(this).serialize(),
          success: function(response) {
              try {
                  // Parse the JSON response (in case it's returned as a string)
                  response = JSON.parse(response);
                  console.log(response);

                  if (response.success) {
                      // Hide any existing error messages
                      $('#errorMessage1').addClass('d-none');

                      // Show success message
                      $('#successMessage1').removeClass('d-none');

                      // Close the modal after a short delay
                      setTimeout(function() {
                          $('#addEventModal').modal('hide');

                          // Reset the form and hide the success message
                          $('#addEventForm')[0].reset();
                          $('#successMessage1').addClass('d-none');

                          // Reload the page to reflect the new event
                          location.reload();
                      }, 2000); // Adjust the delay as needed (2 seconds here)

                  } else {
                      // Hide any existing success messages
                      $('#successMessage1').addClass('d-none');

                      // Show error messages
                      $('#errorMessage1').removeClass('d-none');
                      let errorHtml = '';
                      for (let field in response.errors) {
                          errorHtml += `<li>${response.errors[field]}</li>`;
                      }
                      $('#errorList1').html(errorHtml);
                  }
              } catch (error) {
                  console.error('Error parsing JSON:', error);
              }
          },
          error: function(xhr, status, error) {
              console.error('Error adding event:', error);
          }
      });
  });

  $('.edit-btn').on('click', function() {
      var eventId = $(this).data('id'); // Get event_id from the button
      console.log("Selected Event ID:", eventId); // Log the event ID for debugging

      // Send an AJAX request to fetch the event details using the event ID
      $.ajax({
          url: 'get_event_details.php', // PHP file to fetch event data
          type: 'POST',
          data: { event_id: eventId },
          dataType: 'json',
          success: function(response) {
              if(response.success) {
                  // Populate the form with event data
                  $('#editEventId').val(response.data.event_id);  // Hidden field for event ID
                  $('#editEventTitle').val(response.data.title);  
                  $('#editEventVenue').val(response.data.event_venue);
                  $('#editEventStartDate').val(response.data.event_start_date);
                  $('#editEventEndDate').val(response.data.event_end_date);
                  $('#editEventType').val(response.data.event_type);

                  // Show the modal
                  $('#editEventModal').modal('show');
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
$('#editEventForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'update_event.php', 
        type: 'POST',
        data: $(this).serialize(), 
        success: function(response) {
            try {
                // Parse the JSON response (ensure it's valid JSON)
                response = JSON.parse(response);
                console.log(response);

                if (response.success) {
                    // Hide any existing error messages
                    $('#errorMessage2').addClass('d-none');

                    // Show success message
                    $('#successMessage2').removeClass('d-none');

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#editEventModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#editEventForm')[0].reset();
                        $('#successMessage2').addClass('d-none');
                        location.reload(); 
                    }, 2000); 
                } else {
                    // Show validation errors
                    $('#successMessage2').addClass('d-none');

                    $('#errorMessage2').removeClass('d-none');
                      let errorHtml = '';
                      for (let field in response.errors) {
                          errorHtml += `<li>${response.errors[field]}</li>`;
                      }
                      $('#errorList2').html(errorHtml);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating event:', error);
            console.log(xhr.responseText);
        }
    });
});

  // Event delegation for dynamically loaded buttons (Archive)
  $(document).on('click', '.archive-btn', function() {
      var eventId = $(this).data('id'); // Get event_id from the button
      $('#archiveEventId').val(eventId); // Store the event ID in the hidden input field
      $('#archiveModal').modal('show'); // Show the archive confirmation modal
      console.log('Selected Event ID: ' + eventId);
  });

  // Handle archive confirmation when "Archive" button in modal is clicked
  $('#confirmArchiveBtn').on('click', function() {
      var eventId = $('#archiveEventId').val(); // Get the event ID from the hidden input field
      
      // Send an AJAX request to archive the event
      $.ajax({
          url: 'archive_event.php', // PHP file to handle archiving
          type: 'POST',
          data: { event_id: eventId },
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

                        // Reset the form and hide the success message
                        $('#editEventForm')[0].reset();
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