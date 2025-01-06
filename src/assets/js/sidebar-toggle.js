document.querySelectorAll(".sidebar-item > .sidebar-link").forEach((item) => {
  item.addEventListener("click", function (e) {
    const submenu = this.nextElementSibling; // Get the submenu if it exists
    const sidebar = document.getElementById("sidebar"); // Reference to sidebar

    // Toggle submenu visibility only if there is a submenu
    if (submenu) {
      e.preventDefault(); // Prevent default only if there's a submenu
      this.parentElement.classList.toggle("show-submenu"); // Toggle submenu visibility
    }
  });
});

// Sidebar toggle functionality
document.getElementById("toggleSidebar").addEventListener("click", function () {
  const sidebar = document.getElementById("sidebar");
  const mainWrapper = document.getElementById("main-wrapper");
  const appHeader = document.querySelector(".app-header");

  sidebar.classList.toggle("collapsed");
  mainWrapper.classList.toggle("expanded");

  // Adjust navbar width based on sidebar state
  if (sidebar.classList.contains("collapsed")) {
    appHeader.style.width = "calc(100% - 70px)";
    appHeader.style.left = "70px";
  } else {
    appHeader.style.width = "calc(100% - 250px)";
    appHeader.style.left = "250px";
  }
});
