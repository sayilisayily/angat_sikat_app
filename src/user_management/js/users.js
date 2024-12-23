$(document).ready(function () {
  $("#usersTable").DataTable({
    paging: true,
    searching: true,
    info: true,
    lengthChange: true,
    pageLength: 10,
    ordering: true,
    order: [],
  });
});

// Handle Add User Form Submission
$("#addUserForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "add_user.php",
    type: "POST",
    data: $(this).serialize(), // Required for FormData
    success: function (response) {
      try {
        // Parse the JSON response (in case it's returned as a string)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#errorMessage").addClass("d-none");

          // Show success message
          $("#successMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#addUserModal").modal("hide");

            // Reset the form and hide the success message
            $("#addUserForm")[0].reset();
            $("#successMessage").addClass("d-none");

            // Reload the page to reflect the new user
            location.reload();
          }, 2000); // Adjust the delay as needed (2 seconds here)
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
      console.error("Error adding user:", error);
    },
  });
});

// Handle Edit User Modal
$(".edit-btn").on("click", function () {
  var userId = $(this).data("id"); // Get user_id from the button
  console.log("Selected User ID:", userId); // Log the user ID for debugging

  // Send an AJAX request to fetch the user details using the user ID
  $.ajax({
    url: "get_user_details.php", // PHP file to fetch user data
    type: "POST",
    data: { user_id: userId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form with user data
        $("#editUserId").val(response.data.user_id); // Hidden field for user ID
        $("#edit_username").val(response.data.username);
        $("#edit_firstname").val(response.data.first_name); // Match DB field names
        $("#edit_lastname").val(response.data.last_name);
        $("#edit_email").val(response.data.email);
        $("#edit_organization").val(response.data.organization_id); // Use organization_id

        // Clear previous error messages
        $("#editErrorMessage").addClass("d-none");
        $("#editErrorList").empty();

        // Show the modal
        $("#editUserModal").modal("show");
      } else {
        console.error("Error fetching data:", response.message);
        alert("Error: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("Failed to fetch user details. Please try again.");
    },
  });
});

// Handle Edit User Form Submission
$("#editUserForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "update_user.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (ensure it's valid JSON)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#editErrorMessage").addClass("d-none");

          // Show success message
          $("#editSuccessMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#editUserModal").modal("hide");

            // Reset the form and hide the success message
            $("#editUserForm")[0].reset();
            $("#editSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          // Show validation errors
          $("#editSuccessMessage").addClass("d-none");

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
      console.error("Error updating user:", error);
      console.log(xhr.responseText);
    },
  });
});

// Event delegation for dynamically loaded buttons (Archive)
$(document).on("click", ".archive-btn", function () {
  var userId = $(this).data("id");
  $("#archiveId").val(userId);
  $("#archiveModal").modal("show");
  console.log("Selected User ID: " + userId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$("#confirmArchiveBtn").on("click", function () {
  var userId = $("#archiveId").val(); // Get the organization ID from the hidden input field

  // Send an AJAX request to archive the organization
  $.ajax({
    url: "archive_user.php", // PHP file to handle archiving
    type: "POST",
    data: { user_id: userId },
    dataType: "json",
    success: function (response) {
      try {
        if (response.success) {
          // Show success message (optional)
          console.log(response.message);
          // Hide any existing error messages
          $("#archiveErrorMessage").addClass("d-none");

          // Show success message
          $("#archiveSuccessMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#archiveModal").modal("hide");
            $("#archiveSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          // Show validation errors
          $("#archiveSuccessMessage").addClass("d-none");

          $("#archiveErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#archiveErrorList").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error archiving organization:", error);
      console.log(xhr.responseText);
    },
  });
});
