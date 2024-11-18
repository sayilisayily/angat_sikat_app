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

  function toggleAccomplishment(eventId, isChecked) {
      $.ajax({
          url: 'update_accomplishment.php',
          type: 'POST',
          data: {
              event_id: eventId,
              accomplishment_status: isChecked ? 1 : 0
          },
          success: function(response) {
              console.log('Accomplishment status updated successfully:', response);
          },
          error: function(xhr, status, error) {
              console.error('Error updating accomplishment status:', error);
          }
      });
  }


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
                      $('#errorMessage').addClass('d-none');

                      // Show success message
                      $('#successMessage').removeClass('d-none');

                      // Close the modal after a short delay
                      setTimeout(function() {
                          $('#addEventModal').modal('hide');

                          // Reset the form and hide the success message
                          $('#addEventForm')[0].reset();
                          $('#successMessage').addClass('d-none');

                          // Reload the page to reflect the new event
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
                    $('#errorMessage').addClass('d-none');

                    // Show success message
                    $('#successMessage').removeClass('d-none');

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#editEventModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#editEventForm')[0].reset();
                        $('#successMessage').addClass('d-none');
                        location.reload(); 
                    }, 2000); 
                } else {
                    // Show validation errors
                    $('#editsuccessMessage').addClass('d-none');

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
              if (response.success) {
                  // Show success message (optional)
                  console.log(response.message);

                  // Reload the DataTable to remove the archived event from the list
                  location.reload();// Reload without resetting pagination
              } else {
                  // Show error message (optional)
                  alert("Error archiving event: " + response.message);
              }

              // Close the modal after archiving
              $('#archiveModal').modal('hide');
          },
          error: function(xhr, status, error) {
              console.error("AJAX Error: ", error);
          }
      });
  });