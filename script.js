document.addEventListener("DOMContentLoaded", function () {
  // Splash Screen Logic
  const splash = document.getElementById("splash-screen");
  if (splash) {
    if (!localStorage.getItem("splashShown")) {
      setTimeout(() => {
        splash.classList.add("hidden");
        localStorage.setItem("splashShown", "true");
      }, 1000); // Show splash for 1 second on first visit
    } else {
      splash.classList.add("hidden");
    }
  }

  // Dark Mode Toggle (icon only)
  const darkModeBtn = document.getElementById("toggleDarkMode");
  const darkModeIconSwitch = document.getElementById("darkModeIconSwitch");
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const savedMode = localStorage.getItem('darkMode');
  function setSwitchState(isDark, animate = false) {
    if (!darkModeIconSwitch) return;
    if (isDark) {
      darkModeIconSwitch.textContent = 'dark_mode';
      if (animate) {
        darkModeIconSwitch.classList.remove('sunrise', 'sundown');
        void darkModeIconSwitch.offsetWidth;
        darkModeIconSwitch.classList.add('sundown');
      } else {
        darkModeIconSwitch.classList.remove('sunrise', 'sundown');
      }
    } else {
      darkModeIconSwitch.textContent = 'light_mode';
      if (animate) {
        darkModeIconSwitch.classList.remove('sunrise', 'sundown');
        void darkModeIconSwitch.offsetWidth;
        darkModeIconSwitch.classList.add('sunrise');
      } else {
        darkModeIconSwitch.classList.remove('sunrise', 'sundown');
      }
    }
  }
  if (savedMode === 'dark' || (!savedMode && prefersDark)) {
    document.body.classList.add('dark-mode');
    setSwitchState(true);
  } else {
    document.body.classList.remove('dark-mode');
    setSwitchState(false);
  }
  if (darkModeBtn) {
    darkModeBtn.addEventListener('click', function () {
      const goingDark = !document.body.classList.contains('dark-mode');
      document.body.classList.toggle('dark-mode');
      localStorage.setItem('darkMode', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
      setSwitchState(document.body.classList.contains('dark-mode'), goingDark);
    });
  }

  // Update Instead Link (Add -> Update Modal)
  const updateInsteadLink = document.getElementById('updateInsteadLink');
  if (updateInsteadLink) {
    updateInsteadLink.addEventListener('click', function (e) {
      e.preventDefault();
      const addModal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
      if (addModal) addModal.hide();
      const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
      updateModal.show();
    });
  }

  // Toast auto-hide
  const toastEl = document.querySelector('.toast');
  if (toastEl) {
    setTimeout(() => {
      const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
      toast.hide();
    }, 2500);
  }
});
