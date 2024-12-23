$(document).ready(function () {
  $(document).ready(function () {
    // Toggle visibility for balance values and change eye icon
    $(".toggle-eye").click(function () {
      var balanceValue = $(this).siblings(".balance-value");
      var balancePlaceholder = $(this).siblings(".balance-placeholder");
      var icon = $(this);

      // Toggle the visibility
      balanceValue.toggle();
      balancePlaceholder.toggle();

      // Toggle the eye icon
      if (balanceValue.is(":visible")) {
        icon.removeClass("fa-eye").addClass("fa-eye-slash"); // Change to eye-slash when visible
      } else {
        icon.removeClass("fa-eye-slash").addClass("fa-eye"); // Change to eye when hidden
      }
    });
  });

  // Submit Beginning Balance Form
  $("#editBeginningBalanceForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: "update_beginning_balance.php", // PHP script to update the balance
      type: "POST",
      data: $(this).serialize(),
      success: function (response) {
        try {
          // Parse the JSON response (ensure it's valid JSON)
          response = JSON.parse(response);
          console.log(response);

          if (response.success) {
            // Hide any existing error messages
            $("#errorMessage1").addClass("d-none");

            // Show success message
            $("#successMessage1").removeClass("d-none");

            // Close the modal after a short delay
            setTimeout(function () {
              $("#editBeginningBalanceModal").modal("hide");
              location.reload();
            }, 2000);
          } else {
            // Hide any existing success messages
            $("#successMessage1").addClass("d-none");

            // Show validation errors
            $("#errorMessage1").removeClass("d-none");
            let errorHtml = "";
            for (let field in response.errors) {
              errorHtml += `<li>${response.errors[field]}</li>`;
            }
            $("#errorList1").html(errorHtml);
          }
        } catch (error) {
          console.error("Error parsing JSON response:", error);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error updating event:", error);
        console.log(response);
      },
    });
  });

  // Submit Cash on Hand form
  $("#editCashOnBankForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: "update_cash_on_bank.php",
      type: "POST",
      data: (form_data = $(this).serialize()),
      success: function (response) {
        try {
          // Parse the JSON response (ensure it's valid JSON)
          response = JSON.parse(response);
          console.log(response);

          if (response.success) {
            // Hide any existing error messages
            $("#errorMessage2").addClass("d-none");

            // Show success message
            $("#successMessage2").removeClass("d-none");

            // Close the modal after a short delay
            setTimeout(function () {
              $("#editCashOnHandModal").modal("hide");
              location.reload();
            }, 2000);
          } else {
            // Hide any existing success messages
            $("#successMessage2").addClass("d-none");

            // Show validation errors
            $("#errorMessage2").removeClass("d-none");
            let errorHtml = "";
            for (let field in response.errors) {
              errorHtml += `<li>${response.errors[field]}</li>`;
            }
            $("#errorList2").html(errorHtml);
          }
        } catch (error) {
          console.error("Error parsing JSON response:", error);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error updating event:", error);
        console.log(response);
      },
    });
  });
});

// Submit Cash on Hand form
$("#editCashOnHandForm").on("submit", function (e) {
  e.preventDefault();

  $.ajax({
    url: "update_cash_on_hand.php",
    type: "POST",
    data: (form_data = $(this).serialize()),
    success: function (response) {
      try {
        // Parse the JSON response (ensure it's valid JSON)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#errorMessage3").addClass("d-none");

          // Show success message
          $("#successMessage3").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#editCashOnHandModal").modal("hide");
            location.reload();
          }, 2000);
        } else {
          // Hide any existing success messages
          $("#successMessage3").addClass("d-none");

          // Show validation errors
          $("#errorMessage3").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#errorList3").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON response:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error updating event:", error);
      console.log(response);
    },
  });
});

$(document).on("click", ".edit-btn", function () {
  var allocationId = $(this).data("id"); // Retrieve allocation_id from button
  console.log("Selected Event ID:", allocationId);
  // Use AJAX to get the budget allocation data
  $.ajax({
    url: "get_budget_allocation.php", // PHP file to fetch budget data
    type: "POST",
    data: { allocation_id: allocationId }, // Send allocation_id to server
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Populate the form fields in the modal with data from the response
        $("#edit_allocation_id").val(response.allocation_id); // Hidden input
        $("#edit_allocated_budget").val(response.allocated_budget); // Read-only field
        $("#addBudget").val(""); // Clear the add budget input
        $("#subtractBudget").val(""); // Clear the subtract budget input

        // Show the modal
        $("#editBudgetModal").modal("show");
      } else {
        alert(response.message || "Failed to fetch data for editing.");
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("An error occurred while fetching budget allocation data.");
    },
  });
});

//Submit Budget Allocation Form
$("#addBudgetForm").on("submit", function (e) {
  e.preventDefault(); // Prevent the default form submission behavior

  $.ajax({
    url: "add_budget.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (ensure it's valid JSON)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#errorMessage4").addClass("d-none");

          // Show success message
          $("#successMessage4").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#addBudgetModal").modal("hide");
            location.reload();
          }, 2000);
        } else {
          // Hide any existing success messages
          $("#successMessage4").addClass("d-none");

          // Show validation errors
          $("#errorMessage4").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#errorList4").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON response:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error updating budget:", error);
      console.log(response);
    },
  });
});

//Submit Budget Allocation Form
$("#editBudgetForm").on("submit", function (e) {
  e.preventDefault(); // Prevent the default form submission behavior

  $.ajax({
    url: "update_budget.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      try {
        // Parse the JSON response (ensure it's valid JSON)
        response = JSON.parse(response);
        console.log(response);

        if (response.success) {
          // Hide any existing error messages
          $("#errorMessage").addClass("d-none");

          // Show success message
          $("#successMessage").removeClass("d-none");

          // Close the modal after a short delay
          setTimeout(function () {
            $("#editBudgetModal").modal("hide");
            location.reload();
          }, 2000);
        } else {
          // Hide any existing success messages
          $("#successMessage").addClass("d-none");

          // Show validation errors
          $("#errorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#errorList").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON response:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error updating budget:", error);
      console.log(response);
    },
  });
});
