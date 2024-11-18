$(document).ready(function() {
    $('#usersTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });
});

// Handle Add User Form Submission
$('#addUserForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'add_user.php',
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
                        $('#addUserModal').modal('hide');

                        // Reset the form and hide the success message
                        $('#addUserForm')[0].reset();
                        $('#successMessage').addClass('d-none');

                        // Reload the page to reflect the new user
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
            console.error('Error adding user:', error);
        }
    });
});

// Handle Edit User Modal
$('.edit-btn').on('click', function() {
    var userId = $(this).data('id'); // Get user_id from the button
    console.log("Selected User ID:", userId); // Log the user ID for debugging

    // Send an AJAX request to fetch the user details using the user ID
    $.ajax({
        url: 'get_user_details.php', // PHP file to fetch user data
        type: 'POST',
        data: { user_id: userId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Populate the form with user data
                $('#editUserId').val(response.data.user_id);  // Hidden field for user ID
                $('#editUsername').val(response.data.username);  
                $('#editFirstName').val(response.data.first_name);
                $('#editLastName').val(response.data.last_name);
                $('#editEmail').val(response.data.email);
                $('#editRole').val(response.data.role);
                $('#editOrganization').val(response.data.organization);  // Assuming organization_id is fetched
                // Show the modal
                $('#editUserModal').modal('show');
            } else {
                console.log("Error fetching data: ", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
});

// Handle Edit User Form Submission
$('#editUserForm').on('submit', function(event) {
    event.preventDefault(); 

    $.ajax({
        url: 'update_user.php', 
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
                        $('#editUserModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#editUserForm')[0].reset();
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
            console.error('Error updating user:', error);
            console.log(xhr.responseText);
        }
    });
});
