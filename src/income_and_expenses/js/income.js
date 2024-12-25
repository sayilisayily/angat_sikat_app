$(document).ready(function () {
  $("#incomeTable").DataTable({
    paging: true,
    searching: true,
    info: true,
    lengthChange: true,
    pageLength: 10,
    ordering: true,
    order: [],
  });
});

// Add an event listener to the title selector dropdown
document
  .getElementById("titleSelector")
  .addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];

    if (selectedOption && selectedOption.value) {
      // Extract data from the selected option
      const summaryId = selectedOption.getAttribute("value") || "";
      const title = selectedOption.getAttribute("data-title") || "";
      const revenue = selectedOption.getAttribute("data-revenue") || "";

      // Populate the modal fields
      document.getElementById("summary_id").value = summaryId;
      document.getElementById("titleInput").value = title;
      document.getElementById("revenue").value = revenue;
    } else {
      // Clear the fields if no title is selected
      document.getElementById("summary_id").value = "";
      document.getElementById("titleInput").value = "";
      document.getElementById("revenue").value = "";
    }
  });

// Handle Add Expense Form Submission
$("#addForm").on("submit", function (event) {
  event.preventDefault();

  // Create a new FormData object from the form
  var formData = new FormData(this);

  $.ajax({
    url: "add_income.php",
    type: "POST",
    data: formData, // Send the FormData object
    processData: false, // Prevent jQuery from processing the data
    contentType: false, // Prevent jQuery from setting the content-type header (FormData handles it)
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
            $("#addModal").modal("hide");

            // Reset the form and hide the success message
            $("#addForm")[0].reset();
            $("#successMessage").addClass("d-none");

            // Reload the page to reflect the new expense
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
      console.error("Error adding expense:", error);
    },
  });
});

$(document).on("click", ".edit-btn", function () {
  var incomeId = $(this).data("id"); // Get income_id from the button
  console.log("Selected Income ID:", incomeId); // Log the event ID for debugging

  // Send an AJAX request to fetch the event details using the expense ID
  $.ajax({
    url: "get_income_details.php", // PHP file to fetch expense data
    type: "POST",
    data: { income_id: incomeId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form with expense data
        $("#editIncomeId").val(incomeId); // Hidden field for event ID
        $("#editTitle").val(response.data.title);
        $("#editRevenue").val(response.data.amount);
        // Show the modal
        $("#editModal").modal("show");
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
$("#editForm").on("submit", function (event) {
  event.preventDefault();
  let formData = new FormData(this);

  $.ajax({
    url: "update_income.php",
    type: "POST",
    data: formData,
    contentType: false, // Important for file upload
    processData: false, // Important for file upload
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
            $("#editModal").modal("hide");

            // Reset the form and hide the success message
            $("#editForm")[0].reset();
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
      console.error("Error updating event:", error);
      console.log(xhr.responseText);
    },
  });
});
// Event delegation for dynamically loaded buttons (Archive)
$(document).on("click", ".archive-btn", function () {
  var incomeId = $(this).data("id"); // Get income_id from the button
  $("#archiveId").val(incomeId); // Store the event ID in the hidden input field
  $("#archiveModal").modal("show"); // Show the archive confirmation modal
  console.log("Selected Event ID: " + incomeId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$("#confirmArchiveBtn").on("click", function () {
  var incomeId = $("#archiveId").val(); // Get the event ID from the hidden input field

  // Send an AJAX request to archive the event
  $.ajax({
    url: "archive_income.php", // PHP file to handle archiving
    type: "POST",
    data: { income_id: incomeId },
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
      console.error("Error archiving event:", error);
      console.log(xhr.responseText);
    },
  });
});
