document.addEventListener("DOMContentLoaded", () => {
  const loader = document.getElementById("loader");
  const modal = document.getElementById("successModal");
  const closeModal = document.getElementById("closeModal");

  // Hide loader after short delay
  setTimeout(() => {
    loader.style.display = "none";
  }, 1000);

  // Show success modal if login successful
  if (loginStatus === "success") {
    modal.style.display = "block";

    // Redirect after 2 seconds
    setTimeout(() => {
      window.location.href = "dashboard.php";
    }, 2000);
  }

  // Close modal manually
  closeModal.onclick = () => {
    modal.style.display = "none";
  };

  // Close modal on outside click
  window.onclick = (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };

  document.addEventListener("DOMContentLoaded", () => {
  const loader = document.getElementById("loader");

  // Hide loader after short delay
  setTimeout(() => {
    loader.style.display = "none";
  }, 1000);

  // Redirect if login successful
  if (loginStatus === "success") {
    window.location.href = "dashboard.php";
  }
});

});
