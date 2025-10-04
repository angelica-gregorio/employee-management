document.addEventListener("DOMContentLoaded", function () {
    const splash = document.getElementById("splash-screen");

    // Check if splash has been shown before
    if (!localStorage.getItem("splashShown")) {
        // Show splash for 2.5 seconds
        setTimeout(() => {
            splash.classList.add("hidden");
            localStorage.setItem("splashShown", "true"); // mark as shown
        }, 2500); // adjust time as needed
    } else {
        // Hide splash immediately
        splash.classList.add("hidden");
    }
});

  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("toggleSidebar");

  toggleBtn.addEventListener("click", () => {
    if (sidebar.style.marginLeft === "-250px") {
      sidebar.style.marginLeft = "0";
    } else {
      sidebar.style.marginLeft = "-250px";
    }
  });
