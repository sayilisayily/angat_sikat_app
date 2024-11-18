let currentDate = new Date();

function renderCalendar() {
  const month = currentDate.getMonth();
  const year = currentDate.getFullYear();

  // Update displayed month and year in the dropdown buttons
  document.getElementById("selectedMonth").textContent =
    currentDate.toLocaleString("default", { month: "long" });
  document.getElementById("selectedYear").textContent = year;

  // Clear previous days
  const daysContainer = document.getElementById("days");
  daysContainer.innerHTML = "";

  const firstDay = new Date(year, month, 1).getDay();
  const lastDay = new Date(year, month + 1, 0).getDate();

  // Blank days before the first day of the month
  for (let i = 0; i < firstDay; i++) {
    const emptyDay = document.createElement("div");
    emptyDay.className = "day empty";
    daysContainer.appendChild(emptyDay);
  }

  // Days of the month
  for (let i = 1; i <= lastDay; i++) {
    const day = document.createElement("div");
    day.className = "day";
    day.textContent = i;
    day.addEventListener("click", () => selectDay(i));
    daysContainer.appendChild(day);
  }
}

// Month selection function
function selectMonth(month) {
  currentDate.setMonth(month);
  renderCalendar();
}

// Year selection function
function selectYear(year) {
  currentDate.setFullYear(year);
  renderCalendar();
}

// Initial render
renderCalendar();
