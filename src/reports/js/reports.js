document.addEventListener("DOMContentLoaded", () => {
  // Prefill the event start date when an event is selected
  document
    .getElementById("event_title")
    .addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      document.getElementById("event_start_date").value =
        selectedOption.getAttribute("data-start-date");
      document.getElementById("event_id").value =
        selectedOption.getAttribute("data-event-id");
    });

  // Handle form submission
  document
    .getElementById("generateReportBtn")
    .addEventListener("click", function () {
      const form = document.getElementById("budgetRequestForm");
      const formData = new FormData(form);

      // Reset alerts
      document.getElementById("successMessage").classList.add("d-none");
      const errorMessage = document.getElementById("errorMessage");
      errorMessage.classList.add("d-none");
      errorMessage.querySelector("#errorList").innerHTML = "";

      // Send AJAX request
      fetch("generate_pdf.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Show success alert
            document
              .getElementById("successMessage")
              .classList.remove("d-none");
            document.getElementById("budgetRequestForm").reset();

            // Optionally download the generated PDF
            const link = document.createElement("a");
            link.href = data.file_url; // PDF file path returned from the server
            link.download = data.file_name; // Suggested file name
            link.click();
          } else {
            // Show error messages
            errorMessage.classList.remove("d-none");
            data.errors.forEach((error) => {
              const li = document.createElement("li");
              li.textContent = error;
              errorMessage.querySelector("#errorList").appendChild(li);
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          errorMessage.classList.remove("d-none");
          const li = document.createElement("li");
          li.textContent = "An unexpected error occurred. Please try again.";
          errorMessage.querySelector("#errorList").appendChild(li);
        });
    });

  // Handle form submission
  document
    .getElementById("generateReportBtn")
    .addEventListener("click", function () {
      const form = document.getElementById("budgetRequestForm");
      const formData = new FormData(form);

      // Reset alerts
      document.getElementById("successMessage").classList.add("d-none");
      const errorMessage = document.getElementById("errorMessage");
      errorMessage.classList.add("d-none");
      errorMessage.querySelector("#errorList").innerHTML = "";

      // Send AJAX request
      fetch("generate_pdf.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Show success alert
            document
              .getElementById("successMessage")
              .classList.remove("d-none");
            document.getElementById("budgetRequestForm").reset();

            // Optionally download the generated PDF
            const link = document.createElement("a");
            link.href = data.file_url; // PDF file path returned from the server
            link.download = data.file_name; // Suggested file name
            link.click();
          } else {
            // Show error messages
            errorMessage.classList.remove("d-none");
            data.errors.forEach((error) => {
              const li = document.createElement("li");
              li.textContent = error;
              errorMessage.querySelector("#errorList").appendChild(li);
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          errorMessage.classList.remove("d-none");
          const li = document.createElement("li");
          li.textContent = "An unexpected error occurred. Please try again.";
          errorMessage.querySelector("#errorList").appendChild(li);
        });
    });
});

document.getElementById("event_title").addEventListener("change", function () {
  const selectedOption = this.options[this.selectedIndex];
  const eventId = selectedOption.getAttribute("data-event-id");

  // Set the event_start_date field
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
