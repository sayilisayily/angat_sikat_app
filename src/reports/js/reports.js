$("#budgetRequestForm").submit(function (e) {
  e.preventDefault(); // Prevent the form from submitting normally

  var formData = $(this).serialize(); // Serialize the form data

  $.ajax({
    url: "generate_pdf.php", // The server-side script to generate the PDF
    type: "POST",
    data: formData,
    success: function (response) {
      // On success, handle the response (e.g., open PDF file or show success message)
      $("#successMessage").removeClass("d-none"); // Show success message
      window.location.href = response; // Assuming the server sends a URL to the PDF file
    },
    error: function (xhr, status, error) {
      // Handle errors
      var errors = JSON.parse(xhr.responseText);
      $("#errorList").empty();
      errors.forEach(function (error) {
        $("#errorList").append("<li>" + error + "</li>");
      });
      $("#errorMessage").removeClass("d-none"); // Show error message
    },
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
