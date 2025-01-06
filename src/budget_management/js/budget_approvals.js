$(document).ready(function () {
    $("#budgetApprovalsTable").DataTable({
      paging: true,
      searching: true,
      info: true,
      lengthChange: true,
      pageLength: 10,
      ordering: true,
      order: [],
    });
  });
  
  $(document).ready(function () {
    const notificationBtn = $("#notificationBtn");
    const notificationBadge = $("#notificationBadge");
    const notificationDropdown = $("#notificationDropdown");
    const notificationList = $("#notificationList");
  
    // Fetch notifications when the page loads
    function fetchNotifications() {
      $.ajax({
        url: "../get_notifications.php", // Adjust path as needed
        method: "GET",
        dataType: "json",
        success: function (data) {
          // Clear the notification list
          notificationList.empty();
  
          if (data.length > 0) {
            let hasUnread = false;
  
            data.forEach((notification) => {
              const isReadClass = notification.is_read == 1 ? "read" : "unread";
              if (notification.is_read == 0) hasUnread = true;
  
              notificationList.append(`
                              <div class="notification-item ${isReadClass} p-2 border-bottom" data-id="${
                notification.id
              }" style="cursor: pointer;">
                                  <p style="margin: 0;">${
                                    notification.message
                                  }</p>
                                  <small class="text-muted">${new Date(
                                    notification.created_at
                                  ).toLocaleString()}</small>
                              </div>
                          `);
            });
  
            // Show or hide the badge based on unread notifications
            if (hasUnread) {
              notificationBadge.removeClass("d-none");
            } else {
              notificationBadge.addClass("d-none");
            }
          } else {
            notificationList.html(`
                          <p id="noNotifications" class="text-center text-muted mt-2">
                              No new notifications
                          </p>
                      `);
            notificationBadge.addClass("d-none");
          }
        },
        error: function (err) {
          console.error("Error fetching notifications:", err);
          notificationList.html(`
                      <p id="noNotifications" class="text-center text-danger mt-2">
                          Failed to load notifications
                      </p>
                  `);
        },
      });
    }
  
    // Toggle dropdown visibility on button click
    notificationBtn.click(function () {
      notificationDropdown.toggleClass("d-none");
      if (!notificationDropdown.hasClass("d-none")) {
        fetchNotifications(); // Fetch notifications when opening the dropdown
      }
    });
  
    // Mark notification as read on click (optional functionality)
    $(document).on("click", ".notification-item", function () {
      const notificationId = $(this).data("id");
  
      // Make an AJAX call to mark the notification as read
      $.ajax({
        url: "../notification_read.php", // Create this PHP file to handle marking as read
        method: "POST",
        data: { id: notificationId },
        success: function () {
          fetchNotifications(); // Refresh notifications
        },
        error: function (err) {
          console.error("Error marking notification as read:", err);
        },
      });
    });
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
  $("#addBudgetApprovalForm").on("submit", function (e) {
    e.preventDefault();
  
    // Create FormData object to include file uploads
    let formData = new FormData(this);
  
    $.ajax({
      url: "add_budget_approval.php", // Add form submission PHP file
      type: "POST",
      data: formData, // Use formData object
      contentType: false, // Important for file upload
      processData: false, // Important for file upload
      success: function (response) {
        try {
          response = JSON.parse(response);
          console.log(response);
          if (response.success) {
            // Hide any existing error messages
            $("#errorMessage").addClass("d-none");
  
            // Show success message
            $("#successMessage").removeClass("d-none");
  
            setTimeout(function () {
              $("#budgetApprovalModal").modal("hide"); // Hide modal after success
  
              // Reset the form and hide the success message
              $("#addBudgetApprovalForm")[0].reset();
              $("#successMessage").addClass("d-none");
  
              location.reload();
            }, 2000); // Reload after 2 seconds
          } else {
            // Hide any existing success messages
            $("#successMessage").addClass("d-none");
  
            // Show error messages
            $("#errorMessage").removeClass("d-none");
            let errorHtml = "";
            for (let field in response.errors) {
              errorHtml += `<li>${response.errors[field]}</li>`;
            }
            $("#errorList").html(errorHtml);
          }
        } catch (error) {
          console.error("Error parsing JSON:", error);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error adding event:", error);
      },
    });
  });
  
  $(document).on("click", ".edit-btn", function () {
    var approvalId = $(this).data("id");
  
    // Use AJAX to get the budget approval data
    $.ajax({
      url: "get_budget_approval.php", // Modify to match your actual PHP file path
      type: "POST",
      data: { approval_id: approvalId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Populate the form fields in the modal
          $("#editApprovalId").val(approvalId);
          $("#editTitle").val(response.title);
          $("#currentAttachment").html(
            "<strong>Current Attachment:</strong> " + response.attachment
          );
  
          // Show the modal
          $("#editBudgetApprovalModal").modal("show");
        } else {
          alert("Failed to fetch data for editing.");
        }
      },
      error: function () {
        alert("Error occurred while fetching budget approval data.");
      },
    });
  });
  
  $("#editBudgetApprovalForm").on("submit", function (e) {
    e.preventDefault();
  
    // Create FormData object to include file uploads
    let formData = new FormData(this);
  
    $.ajax({
      url: "update_budget_approval.php", // Add form submission PHP file
      type: "POST",
      data: formData, // Use formData object
      contentType: false, // Important for file upload
      processData: false, // Important for file upload
      success: function (response) {
        try {
          response = JSON.parse(response);
          console.log(response);
          if (response.success) {
            // Hide any existing error messages
            $("#editErrorMessage").addClass("d-none");
  
            // Show success message
            $("#editSuccessMessage").removeClass("d-none");
  
            setTimeout(function () {
              $("#editBudgetApprovalModal").modal("hide"); // Hide modal after success
  
              // Reset the form and hide the success message
              $("#editBudgetApprovalForm")[0].reset();
              $("#editSuccessMessage").addClass("d-none");
  
              location.reload();
            }, 2000); // Reload after 2 seconds
          } else {
            // Hide any existing success messages
            $("#editSuccessMessage").addClass("d-none");
  
            // Show error messages
            $("#editErrorMessage").removeClass("d-none");
            let errorHtml = "";
            for (let field in response.errors) {
              errorHtml += `<li>${response.errors[field]}</li>`;
            }
            $("#editErrorList").html(errorHtml);
          }
        } catch (error) {
          console.error("Error parsing JSON:", error);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error adding event:", error);
      },
    });
  });
  
  // Event delegation for dynamically loaded archive buttons (for budget approval)
  $(document).on("click", ".archive-btn", function () {
    var budgetApprovalId = $(this).data("id"); // Get the budget approval ID from the button
    $("#archiveBudgetApprovalId").val(budgetApprovalId); // Store the ID in the hidden input field
    $("#archiveModal").modal("show"); // Show the archive confirmation modal
    console.log("Selected Event ID: " + budgetApprovalId);
  });
  
  // Handle archive confirmation when "Archive" button in modal is clicked
  $("#confirmArchiveBtn").on("click", function () {
    var budgetApprovalId = $("#archiveBudgetApprovalId").val(); // Get the event ID from the hidden input field
  
    // Send an AJAX request to archive the event
    $.ajax({
      url: "archive_budget_approval.php", // PHP file to handle archiving
      type: "POST",
      data: { approval_id: budgetApprovalId },
      dataType: "json",
      success: function (response) {
        try {
          if (response.success) {
            // Show success message (optional)
            console.log(response.message);
            // Hide any existing error messages
            $("#errorMessage3").addClass("d-none");
  
            // Show success message
            $("#successMessage3").removeClass("d-none");
  
            // Close the modal after a short delay
            setTimeout(function () {
              $("#archiveModal").modal("hide");
              $("#successMessage3").addClass("d-none");
              location.reload();
            }, 2000);
          } else {
            // Show validation errors
            $("#editsuccessMessage3").addClass("d-none");
  
            $("#errorMessage3").removeClass("d-none");
            let errorHtml = "";
            for (let field in response.errors) {
              errorHtml += `<li>${response.errors[field]}</li>`;
            }
            $("#errorList3").html(errorHtml);
          }
        } catch (error) {
          console.error("Error parsing JSON:", error);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error archiving event:", error);
        console.log(xhr.responseText);
      },
    });
  });