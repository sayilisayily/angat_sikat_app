$(document).ready(function () {
  $("#organizationsTable").DataTable({
    paging: true,
    searching: true,
    info: true,
    lengthChange: true,
    pageLength: 10,
    ordering: true,
    order: [],
  });
});

// Handle Add Organization Form Submission
$("#addOrganizationForm").on("submit", function (event) {
  event.preventDefault();
  var formData = new FormData(this);

  $.ajax({
    url: "add_organization.php",
    type: "POST",
    data: formData,
    processData: false, // Prevent jQuery from processing the data
    contentType: false,
    success: function (response) {
      try {
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          $("#errorMessage").addClass("d-none");
          $("#successMessage").removeClass("d-none");

          setTimeout(function () {
            $("#addOrganizationModal").modal("hide");
            $("#addOrganizationForm")[0].reset();
            $("#successMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          $("#successMessage").addClass("d-none");
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
      console.error("Error adding organization:", error);
    },
  });
});

// Edit Organization Button Click
$(".edit-btn").on("click", function () {
  var organizationId = $(this).data("id");
  console.log("Selected Organization ID:", organizationId);

  $.ajax({
    url: "get_organization_details.php",
    type: "POST",
    data: { organization_id: organizationId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form fields with the response data
        $("#editOrganizationId").val(response.data.organization_id);
        $("#editOrganizationName").val(response.data.organization_name);
        $("#editOrganizationMembers").val(response.data.organization_members);
        $("#editOrganizationStatus").val(response.data.organization_status);
        $("#editOrganizationColor").val(response.data.organization_color);
        $("#existingLogo").val(response.data.organization_logo);

        // Show the modal
        $("#editOrganizationModal").modal("show");
      } else {
        alert("Error fetching organization details: " + response.message);
        console.error(response.message);
      }
    },
    error: function (xhr, status, error) {
      alert("An error occurred while fetching organization details.");
      console.error("AJAX Error:", status, error);
    },
  });
});

// Handle Edit Organization Form Submission
$("#editOrganizationForm").on("submit", function (event) {
  event.preventDefault();
  var formData = new FormData(this);

  $.ajax({
    url: "update_organization.php",
    type: "POST",
    data: formData,
    processData: false, // Prevent jQuery from processing the data
    contentType: false,
    success: function (response) {
      try {
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          $("#editErrorMessage").addClass("d-none");
          $("#editSuccessMessage").removeClass("d-none");

          setTimeout(function () {
            $("#editOrganizationModal").modal("hide");
            $("#editOrganizationForm")[0].reset();
            $("#editSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
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
      console.error("Error updating organization:", error);
      console.log(xhr.responseText);
    },
  });
});

// Event delegation for dynamically loaded buttons (Archive)
$(document).on("click", ".archive-btn", function () {
  var organizationId = $(this).data("id");
  $("#archiveId").val(organizationId);
  $("#archiveModal").modal("show");
  console.log("Selected Organization ID: " + organizationId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$("#confirmArchiveBtn").on("click", function () {
  var organizationId = $("#archiveId").val(); // Get the organization ID from the hidden input field

  // Send an AJAX request to archive the organization
  $.ajax({
    url: "archive_organization.php", // PHP file to handle archiving
    type: "POST",
    data: { organization_id: organizationId },
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
