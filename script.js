// Save scroll position before reload
window.addEventListener("beforeunload", function () {
  localStorage.setItem("scrollPosition", window.scrollY);
});

// Restore scroll position on load
window.addEventListener("load", function () {
  let scrollPosition = localStorage.getItem("scrollPosition");
  if (scrollPosition) window.scrollTo(0, parseInt(scrollPosition));
});
