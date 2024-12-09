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

document.addEventListener('DOMContentLoaded', function () {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationCount = document.getElementById('notificationCount');
    const notificationList = document.getElementById('notificationList');
    const noNotifications = document.getElementById('noNotifications');
    
    // Fetch and display notifications when button is clicked
    notificationBtn.addEventListener('click', async () => {
        await loadNotifications();
        toggleDropdown();
    });
    
    // Function to load notifications from the server
    async function loadNotifications() {
        try {
            const response = await fetch('../fetch_notifications.php');
            const notifications = await response.json();
            console.log(notifications); // Debug the fetched notifications
            console.log('Unread Notifications:', notifications.filter(n => n.is_read === 0));
    
    
            // Update the notification dropdown
            notificationList.innerHTML = ''; // Clear existing notifications
            if (notifications.length > 0) {
                noNotifications.style.display = 'none';
                notifications.forEach(notification => {
                    const notificationItem = document.createElement('div');
                    notificationItem.classList.add('notification-item');
                    notificationItem.style.borderBottom = '1px solid #ccc';
                    notificationItem.style.padding = '10px';
                    notificationItem.style.cursor = 'pointer';
    
                    notificationItem.innerHTML = `
                        <p style="margin: 0;">${notification.message}</p>
                        <small style="color: gray;">${new Date(notification.created_at).toLocaleString()}</small>
                    `;
    
                    // Mark as read when clicked
                    notificationItem.addEventListener('click', () => {
                        markAsRead(notification.id);
                        notificationItem.style.backgroundColor = '#f0f0f0'; // Optional feedback
                    });
    
                    notificationList.appendChild(notificationItem);
                });
            } else {
                noNotifications.style.display = 'block';
            }
    
            // Update unread notification count
            updateNotificationBadge(notifications.filter(n => n.is_read === 0).length);
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }
    
    // Function to update the notification count badge
    function updateNotificationBadge(count) {
    try {
        console.log('Notification Count:', count); // Debug count
        if (count > 0) {
            notificationCount.textContent = count;
            notificationCount.style.display = 'block';
        } else {
            notificationCount.style.display = 'none';
        }
    } catch (error) {
        console.error('Error updating badge:', error);
    }
    }
    
    
    
    // Function to mark a notification as read
    async function markAsRead(notificationId) {
        try {
            await fetch('../notification_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: notificationId }),
            });
    
            // Reload notifications to refresh the unread count
            await loadNotifications();
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    // Function to toggle the dropdown
    function toggleDropdown() {
        console.log('Dropdown toggled'); // Debug toggle
        notificationDropdown.style.display = notificationDropdown.style.display === 'none' ? 'block' : 'none';
    }
    
    
    // Initial fetch to load unread notifications count
    loadNotifications();
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
                console.log(response);
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