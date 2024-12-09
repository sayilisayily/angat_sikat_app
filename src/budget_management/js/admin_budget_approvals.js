$(document).ready(function () {
    $('#approvalsTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10,
        "ordering": true,
        "order": [],
    });
});

// JavaScript to handle the modal data population
document.addEventListener('DOMContentLoaded', function () {
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmIdInput = document.getElementById('confirmId');
    const confirmActionInput = document.getElementById('confirmAction');
    const actionText = document.getElementById('actionText');

    confirmationModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const action = button.getAttribute('data-action'); // Extract action from data attributes
        const id = button.getAttribute('data-id'); // Extract ID from data attributes

        // Set the form values
        confirmIdInput.value = id;
        confirmActionInput.value = action;

        // Update modal text
        actionText.textContent = action === 'approve' ? 'approve' : 'disapprove';
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



// Handle confirmation when "Confirm" button in modal is clicked
$('#confirmationForm').on('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    var formData = $(this).serialize(); // Serialize the form data

    // Send an AJAX request to process the approval/disapproval
    $.ajax({
        url: 'update_approval_status.php', // PHP file to handle the action
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            try {
                if (response.success) {
                    // Show success message
                    $('#successMessage').removeClass('d-none').text(response.message);

                    // Hide the error message if previously shown
                    $('#errorMessage').addClass('d-none');

                    // Close the modal and reload the table after a delay
                    setTimeout(function () {
                        $('#confirmationModal').modal('hide');
                        $('#successMessage').addClass('d-none');
                        location.reload(); // Reload the page to reflect changes
                    }, 2000);
                } else {
                    // Show validation or other errors
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
            console.error('Error processing approval/disapproval:', error);
            console.log(xhr.responseText);
        }
    });
});