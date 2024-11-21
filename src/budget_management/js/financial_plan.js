$(document).ready(function() {
    document.getElementById('type').addEventListener('change', function () {
        const categoryField = document.getElementById('category');
        if (this.value === 'Expense') {
            categoryField.disabled = false;
        } else {
            categoryField.disabled = true;
            categoryField.value = ''; // Reset category value when type is not expense
        }
    });

    document.getElementById('category').addEventListener('change', function () {
        const dateField = document.getElementById('date');
        if (this.value === 'Activities') {
            dateField.disabled = false;
        } else {
            dateField.disabled = true;
            dateField.value = ''; // Reset date value when category is not activites
        }
    });

    document.getElementById('editType').addEventListener('change', function () {
        const editCategoryField = document.getElementById('editCategory');
        if (this.value === 'Expense') {
            editCategoryField.disabled = false;
        } else {
            editCategoryField.disabled = true;
            editCategoryField.value = ''; // Reset category value when type is not expense
        }
    });

    document.getElementById('editCategory').addEventListener('change', function () {
        const editDateField = document.getElementById('editDate');
        if (this.value === 'Activities') {
            editDateField.disabled = false;
        } else {
            editDateField.disabled = true;
            editDateField.value = ''; // Reset date value when category is not activities
        }
    });

  // Handle Add Event Form Submission
  $('#addPlanForm').on('submit', function(event) {
      event.preventDefault(); 

      $.ajax({
          url: 'add_plan.php',
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
                          $('#addPlanModal').modal('hide');

                          // Reset the form and hide the success message
                          $('#addPlanForm')[0].reset();
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

  $('.edit-btn').on('click', function () {
    // Get the Plan ID from the clicked button
    var planId = $(this).data('id'); 
    console.log("Selected Plan ID:", planId); // Log for debugging

    // Check if the Plan ID is valid
    if (!planId) {
        console.error("Plan ID is missing.");
        return;
    }

    // Send an AJAX request to fetch the plan details using the Plan ID
    $.ajax({
        url: 'get_plan_details.php', // PHP file to fetch plan data
        type: 'POST',
        data: { plan_id: planId },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                // Populate the edit modal with the fetched plan data
                $('#editPlanId').val(response.data.plan_id);  // Hidden field for Plan ID
                $('#editTitle').val(response.data.title);  
                $('#editDate').val(response.data.date);
                $('#editAmount').val(response.data.amount);
                $('#editType').val(response.data.type);

                // Enable or disable the Category field based on the Type
                if (response.data.type === "Expense") {
                    $('#editCategory').removeAttr('disabled').val(response.data.category);
                } else {
                    $('#editCategory').attr('disabled', 'disabled').val("");
                }

                // Show the edit modal
                $('#editPlanModal').modal('show');
            } else {
                console.error("Error fetching data: ", response.message);
                alert("Failed to fetch plan details. Please try again.");
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error: ", error);
            alert("An error occurred while fetching plan details. Please check your network or try again later.");
        }
    });
});

 

// Handle Edit Event Form Submission
$('#editPlanForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'update_plan.php', 
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
                        $('#editPlanModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#editPlanForm')[0].reset();
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
  $(document).on('click', '.delete-btn', function() {
      var planId = $(this).data('id'); // Get event_id from the button
      $('#deletePlanId').val(planId); // Store the event ID in the hidden input field
      $('#deleteModal').modal('show'); // Show the archive confirmation modal
      console.log('Selected Plan ID: ' + planId);
  });

  // Handle archive confirmation when "Archive" button in modal is clicked
  $('#confirmDeleteBtn').on('click', function() {
      var planId = $('#deletePlanId').val(); // Get the event ID from the hidden input field
      
      // Send an AJAX request to archive the event
      $.ajax({
          url: 'delete_plan.php', // PHP file to handle archiving
          type: 'POST',
          data: { plan_id: planId },
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
                        $('#deleteModal').modal('hide'); 

                        
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
});