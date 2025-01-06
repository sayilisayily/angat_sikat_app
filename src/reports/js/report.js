$(document).ready(function () {
    // Handle Budget Request Form Submission
    $("#budgetRequestForm").on("submit", function (event) {
      event.preventDefault(); // Prevent default form submission
  
      // Hide previous alerts
      $("#successMessage").addClass("d-none");
      $("#errorMessage").addClass("d-none");
      $("#errorList").empty(); // Clear any previous errors
  
      $.ajax({
        url: "generate_budget_request.php", // The PHP file to generate the PDF
        type: "POST",
        data: $(this).serialize(), // Serialize the form data
        xhrFields: {
          responseType: "blob", // Expect a binary response (PDF file)
        },
        success: function (response, status, xhr) {
          console.log(response); // Log the response for inspection
          const disposition = xhr.getResponseHeader("Content-Disposition");
          let filename = "budget_request.pdf"; // Default filename
  
          // Check if the response is a blob and log it
          if (response instanceof Blob) {
            console.log("Received PDF blob");
          } else {
            console.error("Unexpected response type: ", response);
          }
  
          // Extract filename from the Content-Disposition header, if available
          if (disposition && disposition.indexOf("attachment") !== -1) {
            const matches = /filename="([^"]*)"/.exec(disposition);
            if (matches && matches[1]) {
              filename = matches[1];
            }
          }
  
          // Create a download link for the file
          const link = document.createElement("a");
          const url = window.URL.createObjectURL(response);
          link.href = url;
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          window.URL.revokeObjectURL(url); // Clean up the object URL
          link.remove();
  
          // Show success message
          $("#successMessage").removeClass("d-none");
  
          // Reset the form and close the modal
          $("#budgetRequestForm")[0].reset();
          $("#budgetRequestModal").modal("hide");
        },
        error: function (xhr, status, error) {
          // Detailed error handling
          console.error("Error generating PDF:", error);
  
          // Handle client-side errors (4xx) and server-side errors (5xx)
          if (xhr.status >= 400 && xhr.status < 500) {
            $("#errorMessage").removeClass("d-none");
            $("#errorList").append(
              "<li>Client Error: " + xhr.statusText + "</li>"
            );
          } else if (xhr.status >= 500) {
            $("#errorMessage").removeClass("d-none");
            $("#errorList").append(
              "<li>Server Error: " + xhr.statusText + "</li>"
            );
          } else {
            // Network or unknown errors
            $("#errorMessage").removeClass("d-none");
            $("#errorList").append(
              "<li>Unexpected Error: Please check your network connection and try again.</li>"
            );
          }
  
          // Optionally display more information for debugging
          if (xhr.responseText) {
            $("#errorList").append(
              "<li>Details: " + xhr.responseText.substring(0, 200) + "...</li>"
            ); // Limit error detail length
          }
        },
        timeout: 10000, // Set a timeout for the request (10 seconds)
      }).fail(function (jqXHR, textStatus) {
        // Handle timeout or other failures
        if (textStatus === "timeout") {
          $("#errorMessage").removeClass("d-none");
          $("#errorList").append("<li>Request timed out. Please try again.</li>");
        }
      });
    });
  });
  
  document.getElementById("event_title").addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    const eventStartDate = selectedOption.getAttribute("data-start-date");
    const eventId = selectedOption.getAttribute("data-event-id");
  
    // Set the event_start_date field
    document.getElementById("event_start_date").value = eventStartDate || "";
    document.getElementById("event_id").value = eventId || "";
  });
  
  document
    .getElementById("proposal_title")
    .addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const eventStartDate = selectedOption.getAttribute("data-start-date");
      const eventId = selectedOption.getAttribute("data-event-id");
      const eventVenue = selectedOption.getAttribute("data-venue");
  
      // Set the event_start_date field
      document.getElementById("proposal_start_date").value = eventStartDate || "";
      document.getElementById("proposal_id").value = eventId || "";
      document.getElementById("proposal_venue").value = eventVenue || "";
    });
  
  document.getElementById("permit_title").addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    const eventAmount = selectedOption.getAttribute("data-amount");
    const eventId = selectedOption.getAttribute("data-event-id");
  
    // Set the event_start_date field
    document.getElementById("total_amount").value = eventAmount || "";
    document.getElementById("permit_id").value = eventId || "";
  });
  
  document
    .getElementById("liquidation_title")
    .addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const eventAmount = selectedOption.getAttribute("data-amount");
      const eventId = selectedOption.getAttribute("data-event-id");
  
      // Set the event_start_date field
      document.getElementById("liquidation_amount").value = eventAmount || "";
      document.getElementById("liquidation_id").value = eventId || "";
    });
  
  document
    .getElementById("accomplishment_title")
    .addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const eventVenue = selectedOption.getAttribute("data-venue");
      const eventStartDate = selectedOption.getAttribute("data-start-date");
      const eventId = selectedOption.getAttribute("data-event-id");
  
      // Set the event_start_date field
      document.getElementById("accomplishment_venue").value = eventVenue || "";
      document.getElementById("accomplishment_start_date").value =
        eventStartDate || "";
      document.getElementById("accomplishment_id").value = eventId || "";
    });