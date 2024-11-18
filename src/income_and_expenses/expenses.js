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
