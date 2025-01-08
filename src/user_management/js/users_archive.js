$(document).ready(function () {
  $("#archiveUsersTable").DataTable({
    paging: true,
    searching: true,
    info: true,
    lengthChange: true,
    pageLength: 10,
    ordering: true,
    order: [],
  });
});

$(document).on("click", ".recover-btn", function () {
  var eventId = $(this).data("id"); // Get the event_id from the button's data-id attribute
  $("#recover_user_id").val(eventId); // Set the value of the hidden input
});

$(document).on("click", ".delete-btn", function () {
  var eventId = $(this).data("id"); // Get the event_id from the button's data-id attribute
  $("#delete_event_id").val(eventId); // Set the value of the hidden input
});

$("#confirmRecoverBtn").on("click", function () {
  var eventId = $("#recover_user_id").val(); // Get the item ID from the hidden input field

  // Send an AJAX request to delete the item
  $.ajax({
    url: "recover_user.php", // PHP file to handle deletion
    type: "POST",
    data: { event_id: eventId },
    dataType: "json",
    success: function (response) {
      try {
        if (response.success) {
          // Show success message (optional)
          console.log(response.message);

          // Hide any existing error messages
          $("#recoverErrorMessage").addClass("d-none");

          // Show success message
          $("#recoverSuccessMessage")
            .removeClass("d-none")
            .text(response.message);

          // Close the modal after a short delay
          setTimeout(function () {
            $("#recoverModal").modal("hide");

            // Reset the form and hide the success message
            $("#recoverSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          // Show validation errors
          $("#recoverSuccessMessage").addClass("d-none");

          $("#recoverErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#recoverErrorList").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error deleting item:", error);
      console.log(xhr.responseText);
    },
  });
});

$("#confirmDeleteBtn").on("click", function () {
  var eventId = $("#delete_event_id").val(); // Get the item ID from the hidden input field

  // Send an AJAX request to delete the item
  $.ajax({
    url: "delete_user.php", // PHP file to handle deletion
    type: "POST",
    data: { event_id: eventId },
    dataType: "json",
    success: function (response) {
      try {
        if (response.success) {
          // Show success message (optional)
          console.log(response.message);

          // Hide any existing error messages
          $("#deleteErrorMessage").addClass("d-none");

          // Show success message
          $("#deleteSuccessMessage")
            .removeClass("d-none")
            .text(response.message);

          // Close the modal after a short delay
          setTimeout(function () {
            $("#deleteModal").modal("hide");

            // Reset the form and hide the success message
            $("#deleteSuccessMessage").addClass("d-none");
            location.reload();
          }, 2000);
        } else {
          // Show validation errors
          $("#deleteSuccessMessage").addClass("d-none");

          $("#deleteErrorMessage").removeClass("d-none");
          let errorHtml = "";
          for (let field in response.errors) {
            errorHtml += `<li>${response.errors[field]}</li>`;
          }
          $("#deleteErrorList").html(errorHtml);
        }
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error deleting item:", error);
      console.log(xhr.responseText);
    },
  });
});
