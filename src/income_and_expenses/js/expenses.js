$(document).ready(function() {
    $('#expensesTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });
});

// Handle Add Expense Form Submission
$('#addExpenseForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'add_expense.php',
        type: 'POST',
        data: $(this).serialize(), // Required for FormData
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
                        $('#addExpenseModal').modal('hide');

                        // Reset the form and hide the success message
                        $('#addExpenseForm')[0].reset();
                        $('#successMessage').addClass('d-none');

                        // Reload the page to reflect the new expense
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
            console.error('Error adding expense:', error);
        }
    });
});

$('.edit-btn').on('click', function() {
      var expenseId = $(this).data('id'); // Get expense_id from the button
      console.log("Selected Expense ID:", expenseId); // Log the event ID for debugging

      // Send an AJAX request to fetch the event details using the expense ID
      $.ajax({
          url: 'get_expense_details.php', // PHP file to fetch expense data
          type: 'POST',
          data: { expense_id: expenseId },
          dataType: 'json',
          success: function(response) {
              if(response.success) {
                  // Populate the form with expense data
                  $('#editExpenseId').val(response.data.expense_id);  // Hidden field for event ID
                  $('#editTitle').val(response.data.title);  
                  $('#editAmount').val(response.data.amount);
                  $('#editReference').val(response.data.reference);
                  // Show the modal
                  $('#editExpensetModal').modal('show');
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
$('#editExpenseForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'update_expense.php', 
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
                        $('#editExpenseModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#editExpenseForm')[0].reset();
                        $('#successMessage').addClass('d-none');
                        location.reload(); 
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
            console.error('Error updating event:', error);
            console.log(xhr.responseText);
        }
    });
});