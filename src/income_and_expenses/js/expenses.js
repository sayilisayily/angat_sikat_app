$(document).ready(function () {
  $("#expensesTable").DataTable({
    paging: true,
    searching: true,
    info: true,
    lengthChange: true,
    pageLength: 10,
    ordering: true,
    order: [],
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const titleDropdown = document.getElementById("title");

  // Add event listener for the title selector
  titleDropdown.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];

    if (selectedOption && selectedOption.value) {
      // Extract data from the selected option
      const totalAmount =
        selectedOption.getAttribute("data-total-amount") || "";
      const category = selectedOption.getAttribute("data-category") || "";
      const summary_id = selectedOption.getAttribute("data-id") || "";
      // Populate hidden fields
      document.getElementById("total_amount").value = totalAmount;
      document.getElementById("category").value = category;
      document.getElementById("summary_id").value = summary_id;

      console.log(
        "Selected Summary ID:",
        selectedOption.getAttribute("data-id")
      );
      console.log(`Selected Title: ${selectedOption.value}`);
      console.log(`Total Amount: ${totalAmount}`);
      console.log(`Category: ${category}`);
    } else {
      // Clear the fields if no title is selected
      document.getElementById("total_amount").value = "";
      document.getElementById("category").value = "";
    }
  });
});

// Handle Add Expense Form Submission
$("#addExpenseForm").on("submit", function (event) {
  event.preventDefault();

  // Create a new FormData object from the form
  var formData = new FormData(this);

  $.ajax({
    url: "add_expense.php",
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
            $("#addExpenseModal").modal("hide");

            // Reset the form and hide the success message
            $("#addExpenseForm")[0].reset();
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

$(".edit-btn").on("click", function () {
  var expenseId = $(this).data("id"); // Get expense_id from the button
  console.log("Selected Expense ID:", expenseId); // Log the event ID for debugging

  // Send an AJAX request to fetch the expense details
  $.ajax({
    url: "get_expense_details.php", // PHP file to fetch expense data
    type: "POST",
    data: { expense_id: expenseId },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form with expense data
        $("#editExpenseId").val(response.data.expense_id); // Populate hidden field
        $("#editTitle").val(response.data.title);
        $("#editCategory").val(response.data.category);
        $("#editAmount").val(response.data.amount);
        // Show the modal
        $("#editExpenseModal").modal("show");
      } else {
        console.error("Error fetching data: ", response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error: ", error);
    },
  });
});

// Handle Edit Event Form Submission
$("#editExpenseForm").on("submit", function (event) {
  event.preventDefault();

  $.ajax({
    url: "update_expense.php",
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
            $("#editExpenseModal").modal("hide");

            // Reset the form and hide the success message
            $("#editExpenseForm")[0].reset();
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
  var expenseId = $(this).data("id"); // Get expense_id from the button
  $("#archiveId").val(expenseId); // Store the event ID in the hidden input field
  $("#archiveModal").modal("show"); // Show the archive confirmation modal
  console.log("Selected Event ID: " + expenseId);
});

// Handle archive confirmation when "Archive" button in modal is clicked
$("#confirmArchiveBtn").on("click", function () {
  var expenseId = $("#archiveId").val(); // Get the event ID from the hidden input field

  // Send an AJAX request to archive the event
  $.ajax({
    url: "archive_expense.php", // PHP file to handle archiving
    type: "POST",
    data: { expense_id: expenseId },
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
