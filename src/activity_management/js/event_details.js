
$(document).ready(function () {
    //Submit Add Item Form
    $('#addItemForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting in the traditional way

            $.ajax({
                url: 'add_item.php', // The PHP file that processes adding the item
                type: 'POST',
                data: $(this).serialize(), // Serialize the form data
                success: function(response) {
                    try {
                        // Parse the JSON response
                        response = JSON.parse(response);

                        if (response.success) {
                            // Hide any error messages
                            $('#errorMessage').addClass('d-none');

                            // Show success message
                            $('#successMessage').removeClass('d-none');

                            // Close the modal after a short delay
                            setTimeout(function() {
                                $('#addItemModal').modal('hide');

                                // Reset the form and hide the success message
                                $('#addItemForm')[0].reset();
                                $('#successMessage').addClass('d-none');

                                // Reload the page to reflect the new item
                                location.reload();
                            }, 2000); // Adjust the delay as needed
                        } else {
                            // Hide any success messages
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
                    console.error('Error adding item:', error);
                }
            });
        });
    
        // Add an event listener to the item selector dropdown
        document.getElementById("item_selector").addEventListener("change", function () {
            const selectedItem = this.value;

            if (selectedItem) {
                // Parse the selected item's JSON data
                const itemData = JSON.parse(selectedItem);

                // Populate the modal fields with the item data
                document.getElementById("summary_description").value = itemData.description || "";
                document.getElementById("summary_quantity").value = itemData.quantity || "";
                document.getElementById("summary_unit").value = itemData.unit || "";
                document.getElementById("summary_amount").value = itemData.amount || "";
            } else {
                // Clear the fields if no item is selected
                document.getElementById("summary_description").value = "";
                document.getElementById("summary_quantity").value = "";
                document.getElementById("summary_unit").value = "";
                document.getElementById("summary_amount").value = "";
            }
        });


    // Submit Add Item Form
    $('#summaryAddItemForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting in the traditional way

        // Create a FormData object to include file inputs
        const formData = new FormData(this);

        $.ajax({
            url: 'add_summary_item.php', // The PHP file that processes adding the item
            type: 'POST',
            data: formData, // Use FormData for the request
            processData: false, // Prevent jQuery from automatically processing data
            contentType: false, // Prevent jQuery from overriding the Content-Type header
            success: function(response) {
                try {
                    // Parse the JSON response
                    response = JSON.parse(response);

                    if (response.success) {
                        // Hide any error messages
                        $('#errorMessage4').addClass('d-none');

                        // Show success message
                        $('#successMessage4').removeClass('d-none');

                        // Close the modal after a short delay
                        setTimeout(function() {
                            $('#summaryAddItemModal').modal('hide');

                            // Reset the form and hide the success message
                            $('#summaryAddItemForm')[0].reset();
                            $('#successMessage4').addClass('d-none');

                            // Reload the page to reflect the new item
                            location.reload();
                        }, 2000); // Adjust the delay as needed
                    } else {
                        // Hide any success messages
                        $('#successMessage4').addClass('d-none');

                        // Show error messages
                        $('#errorMessage4').removeClass('d-none');
                        let errorHtml = '';
                        for (let field in response.errors) {
                            errorHtml += `<li>${response.errors[field]}</li>`;
                        }
                        $('#errorList4').html(errorHtml);
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    console.log(response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error adding item:', error);
                console.log(xhr.responseText);
            }
        });
    });


    // Fetch and populate Edit Item Modal
    $('.edit-btn').on('click', function() {
        var itemId = $(this).data('id'); // Corrected to retrieve item_id from the button
        console.log("Selected Item ID:", itemId); // Log the item ID for debugging

        $.ajax({
            url: 'get_item_details.php', // Your PHP script for fetching the item details
            type: 'POST',
            data: {item_id: itemId},
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.success) {
                    // Populate the modal fields with the item data
                    $('#edit_item_id').val(response.data.item_id);
                    $('#edit_description').val(response.data.description);
                    $('#edit_quantity').val(response.data.quantity);
                    $('#edit_unit').val(response.data.unit);
                    $('#edit_amount').val(response.data.amount);

                    // Show the modal
                    $('#editItemModal').modal('show');
                } else {
                    // Display an error message if fetching failed
                    console.log("Error fetching data: ", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: ", error);
                console.log("Status:", status);
                console.log("Response Text:", xhr.responseText); // Log full response for debugging
            }
        });
    });

    // Fetch and populate Edit Item Modal
    $('.summary-edit-btn').on('click', function() {
        var itemId = $(this).data('id'); // Retrieve item_id from the button
        console.log("Selected Item ID:", itemId); // Log the item ID for debugging

        $.ajax({
            url: 'get_summary_item_details.php', // Your PHP script for fetching the item details
            type: 'POST',
            data: { item_id: itemId },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.success) {
                    // Populate the modal fields with the item data
                    $('#summary_edit_item_id').val(response.data.summary_item_id);
                    $('#summary_edit_description').val(response.data.description);
                    $('#summary_edit_quantity').val(response.data.quantity);
                    $('#summary_edit_unit').val(response.data.unit);
                    $('#summary_edit_amount').val(response.data.amount);

                    // Handle reference file display (optional if reference details are required)
                    $('#currentAttachment').html('<strong>Current Attachment:</strong> ' + response.data.reference);

                    // Show the modal
                    $('#summaryEditItemModal').modal('show');
                } else {
                    // Display an error message if fetching failed
                    console.error("Error fetching data: ", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
                console.log("Status:", status);
                console.log("Response Text:", xhr.responseText); // Log full response for debugging
            }
        });
    });



// Handle form submission for updating the item
$('#summaryEditItemForm').on('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this); // Use FormData to handle file uploads

    $.ajax({
        url: 'update_summary_item.php', // URL of your PHP script for updating the item
        type: 'POST',
        data: formData,
        processData: false, // Required for FormData
        contentType: false, // Required for FormData
        success: function(response) {
            // Parse the JSON response
            var result = JSON.parse(response);
            console.log(response);
            if (result.success) {
                // Hide any existing error messages
                $('#errorMessage5').addClass('d-none');
                // Show success message
                $('#successMessage5').removeClass('d-none');

                setTimeout(function() {
                    $('#summaryEditItemModal').modal('hide');
                    // Reset the form and hide the success message
                    $('#summaryEditItemForm')[0].reset();
                    $('#successMessage5').addClass('d-none');
                    location.reload();
                }, 2000);
            } else {
                // Show validation errors
                $('#successMessage5').addClass('d-none');
                $('#errorMessage5').removeClass('d-none');
                let errorHtml = '';
                for (let field in result.errors) {
                    errorHtml += `<li>${result.errors[field]}</li>`;
                }
                $('#editErrorList5').html(errorHtml);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating item:', error);
            console.log("Status:", status);
            console.log("Response Text:", xhr.responseText);
        }
    });
});


// Handle form submission for updating the item
$('#summaryEditItemForm').on('submit', function(e) {
    e.preventDefault()
    $.ajax({
        url: 'update_summary_item.php', // URL of your PHP script for updating the item
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            // Parse the JSON response
            var result = JSON.parse(response);
            console.log(response);
            if (result.success) {
                // Hide any existing error messages
                $('#errorMessage5').addClass('d-none')
                // Show success message
                $('#successMessage5').removeClass('d-none');
                
                setTimeout(function() {
                    $('#summaryEditItemModal').modal('hide')
                    // Reset the form and hide the success message
                    $('#summaryEditItemForm')[0].reset();
                    $('#successMessage5').addClass('d-none');
                    location.reload();
                }, 2000); 
            } else {
                // Show validation errors
                $('#successMessage5').addClass('d-none')
                $('#errorMessage5').removeClass('d-none');
                let errorHtml = '';
                for (let field in response.errors) {
                    errorHtml += `<li>${response.errors[field]}</li>`;
                }
                $('#errorList5').html(errorHtml);
            }
        },
        error: function() {
            console.error('Error updating event:', error);
        }
    });
});

// JavaScript for the delete button click event
$(document).on("click", ".summary-delete-btn", function () {
    var itemId = $(this).data("id");  // Retrieve item_id from button's data attribute
    $("#summary_delete_item_id").val(itemId);        // Set item_id in the modal form's hidden input field
    console.log('Selected Item ID: ' + itemId);
});

// JavaScript for the delete button click event
$(document).on("click", ".delete-btn", function () {
    var itemId = $(this).data("id");  // Retrieve item_id from button's data attribute
    $("#delete_item_id").val(itemId);        // Set item_id in the modal form's hidden input field
    console.log('Selected Item ID: ' + itemId);
});

$('#confirmDeleteBtn').on('click', function() {
    var itemId = $('#delete_item_id').val(); // Get the item ID from the hidden input field
    
    // Send an AJAX request to delete the item
    $.ajax({
        url: 'delete_item.php', // PHP file to handle deletion
        type: 'POST',
        data: { item_id: itemId },
        dataType: 'json',
        success: function(response) {
            try { 
                if (response.success) {
                    // Show success message (optional)
                    console.log(response.message);
                    
                    // Hide any existing error messages
                    $('#errorMessage3').addClass('d-none');

                    // Show success message
                    $('#successMessage3').removeClass('d-none').text(response.message);

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#deleteItemModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#successMessage3').addClass('d-none');
                        location.reload(); 
                    }, 2000);
                } else {
                    // Show validation errors
                    $('#successMessage3').addClass('d-none');

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
            console.error('Error deleting item:', error);
            console.log(xhr.responseText);
        }
    });
});

$('#summaryConfirmDeleteBtn').on('click', function() {
    var itemId = $('#summary_delete_item_id').val(); // Get the item ID from the hidden input field
    
    // Send an AJAX request to delete the item
    $.ajax({
        url: 'delete_summary_item.php', // PHP file to handle deletion
        type: 'POST',
        data: { item_id: itemId },
        dataType: 'json',
        success: function(response) {
            try { 
                if (response.success) {
                    // Show success message (optional)
                    console.log(response.message);
                    
                    // Hide any existing error messages
                    $('#errorMessage6').addClass('d-none');

                    // Show success message
                    $('#successMessage6').removeClass('d-none').text(response.message);

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#summaryDeleteItemModal').modal('hide'); 

                        // Reset the form and hide the success message
                        $('#successMessage6').addClass('d-none');
                        location.reload(); 
                    }, 2000);
                } else {
                    // Show validation errors
                    $('#successMessage6').addClass('d-none');

                    $('#errorMessage6').removeClass('d-none');
                    let errorHtml = '';
                    for (let field in response.errors) {
                        errorHtml += `<li>${response.errors[field]}</li>`;
                    }
                    $('#errorList6').html(errorHtml);
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


$(document).ready(function () {
    // Check localStorage for the last active tab
    let lastActiveTab = localStorage.getItem("lastActiveTab");
    
    // If a saved tab exists, activate it
    if (lastActiveTab) {
        $(`#${lastActiveTab}-tab`).tab("show");
    }

    // Save the active tab to localStorage on click
    $("a[data-bs-toggle='tab']").on("shown.bs.tab", function (e) {
        let activeTabId = $(e.target).attr("id");
        localStorage.setItem("lastActiveTab", activeTabId);
    });
});
});

