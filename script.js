document.addEventListener("DOMContentLoaded", function () {
  // Multi-delete: Select all checkboxes
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.checked = selectAll.checked;
      });
    });
  }
  // Uncheck selectAll if any row is unchecked
  document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-checkbox')) {
      if (!e.target.checked && selectAll) selectAll.checked = false;
      if (selectAll && document.querySelectorAll('.row-checkbox:checked').length === document.querySelectorAll('.row-checkbox').length) {
        selectAll.checked = true;
      }
    }
  });

  // Splash Screen Logic (with failsafe)
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
    // Failsafe: always hide splash after 2 seconds
    setTimeout(() => {
      splash.classList.add("hidden");
    }, 2000);
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

  // Modal Switchers
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
  const addInsteadLink = document.getElementById('addInsteadLink');
  if (addInsteadLink) {
    addInsteadLink.addEventListener('click', function (e) {
      e.preventDefault();
      const updateModal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
      if (updateModal) updateModal.hide();
      const addModal = new bootstrap.Modal(document.getElementById('addModal'));
      addModal.show();
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
