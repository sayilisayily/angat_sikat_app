$(document).ready(function () {
  $("#archiveLegendTable").DataTable({
    paging: true,
    searching: true,
    info: true,
    lengthChange: true,
    pageLength: 10,
    ordering: true,
    order: [],
  });
});

// Handle Add Event Form Submission
$("#addYearForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "add_year.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (in case it's returned as a string)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#yearErrorMessage").addClass("d-none");

          // Show success message
          $("#yearsuccessMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#addYearModal").modal("hide");

            // Reset the form and hide the success message
            $("#addYearForm")[0].reset();
            $("#yearsuccessMessage").addClass("d-none");

            // Reload the page to reflect the new event
            location.reload();
          }, 2000); // Adjust the delay as needed (2 seconds here)
        } else {
          // Hide any existing success messages
          $("#yearsuccessMessage").addClass("d-none");

          // Show error messages
          $("#yearErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#yearErrorList").html(errorHtml);
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

// Handle Add Event Form Submission
$("#addSemesterForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "add_semester.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (in case it's returned as a string)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#semesterErrorMessage").addClass("d-none");

          // Show success message
          $("#semesterSuccessMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#addYearModal").modal("hide");

            // Reset the form and hide the success message
            $("#addSemesterForm")[0].reset();
            $("#semesterSuccessMessage").addClass("d-none");

            // Reload the page to reflect the new event
            location.reload();
          }, 2000); // Adjust the delay as needed (2 seconds here)
        } else {
          // Hide any existing success messages
          $("#semesterSuccessMessage").addClass("d-none");

          // Show error messages
          $("#semesterErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#semesterErrorList").html(errorHtml);
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

$(".edit-year-btn").on("click", function () {
  var yearId = $(this).data("id"); // Get semester_id from the button
  console.log("Selected Year ID:", yearId); // Log the event ID for debugging

  // Send an AJAX request to fetch the event details using the event ID
  $.ajax({
    url: "get_year_details.php", // PHP file to fetch event data
    type: "POST",
    data: { year_id: yearId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form with event data
        $("#editYearId").val(response.data.year_id); // Hidden field for event ID
        $("#editYearStartDate").val(response.data.start_date);
        $("#editYearEndDate").val(response.data.end_date);
        $("#editYearStatus").val(response.data.status);

        // Show the modal
        $("#editYearModal").modal("show");
      } else {
        console.log("Error fetching data: ", response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: ", error);
    },
  });
});

$(".edit-semester-btn").on("click", function () {
  var semesterId = $(this).data("id"); // Get semester_id from the button
  console.log("Selected Legend ID:", semesterId); // Log the event ID for debugging

  // Send an AJAX request to fetch the event details using the event ID
  $.ajax({
    url: "get_semester_details.php", // PHP file to fetch event data
    type: "POST",
    data: { semester_id: semesterId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form with event data
        $("#editSemesterId").val(response.data.semester_id); // Hidden field for event ID
        $("#editSemesterYear").val(response.data.year_id);
        $("#editType").val(response.data.type);
        $("#editStartDate").val(response.data.start_date);
        $("#editEndDate").val(response.data.end_date);
        $("#editStatus").val(response.data.status);

        // Show the modal
        $("#editSemesterModal").modal("show");
      } else {
        console.log("Error fetching data: ", response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: ", error);
    },
  });
});

// Handle Edit Event Form Submission
$("#editSemesterForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "update_semester.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (ensure it's valid JSON)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#semesterEditErrorMessage").addClass("d-none");

          // Show success message
          $("#semesterEditSuccessMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#editSemesterModal").modal("hide");

            // Reset the form and hide the success message
            $("#editSemesterForm")[0].reset();
            $("#semesterEditSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          // Show validation errors
          $("#semesterEditSuccessMessage").addClass("d-none");

          $("#semesterEditErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#semesterEditErrorList").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error updating event:", error);
      console.log(xhr.responseText);
    },
  });
});

// Handle Edit Event Form Submission
$("#editYearForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "update_year.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (ensure it's valid JSON)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#yearEditErrorMessage").addClass("d-none");

          // Show success message
          $("#yearEditSuccessMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#editYearModal").modal("hide");

            // Reset the form and hide the success message
            $("#editYearForm")[0].reset();
            $("#yearEditSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          // Show validation errors
          $("#yearEditSuccessMessage").addClass("d-none");

          $("#yearEditErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#yearEditErrorList").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error updating event:", error);
      console.log(xhr.responseText);
    },
  });
});

// Event delegation for dynamically loaded buttons (Archive)
$(document).on("click", ".archive-btn", function () {
  var eventId = $(this).data("id"); // Get legend_id from the button
  $("#archiveEventId").val(eventId); // Store the event ID in the hidden input field
  $("#archiveModal").modal("show"); // Show the archive confirmation modal
  console.log("Selected Event ID: " + eventId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$("#confirmArchiveBtn").on("click", function () {
  var eventId = $("#archiveEventId").val(); // Get the event ID from the hidden input field

  // Send an AJAX request to archive the event
  $.ajax({
    url: "archive_event.php", // PHP file to handle archiving
    type: "POST",
    data: { event_id: eventId },
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
          $("#successMessage3").addClass("d-none");

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
