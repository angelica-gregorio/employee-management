document.addEventListener("DOMContentLoaded", function() {
    // Wait for the page to load, then fade out splash
    setTimeout(() => {
        const splash = document.getElementById("splash-screen");
        splash.classList.add("hidden");
    }, 1800); // 1.8 seconds delay
  })
