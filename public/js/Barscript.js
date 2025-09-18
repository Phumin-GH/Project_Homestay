// Dropdown functionality - Click only, no hover
document.addEventListener("DOMContentLoaded", function () {
  const dropdownToggle = document.getElementById("dropdown-toggle");
  const dropdownMenu = document.getElementById("dropdown-menu");

  console.log("Dropdown elements:", {
    dropdownToggle,
    dropdownMenu,
  });

  if (dropdownToggle && dropdownMenu) {
    let isOpen = false;

    // Toggle dropdown on click
    dropdownToggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      console.log("Dropdown toggle clicked");

      if (isOpen) {
        closeDropdown();
      } else {
        openDropdown();
      }
    });

    // Open dropdown function
    function openDropdown() {
      dropdownMenu.classList.add("show");
      dropdownToggle.classList.add("active");
      isOpen = true;
    }

    // Close dropdown function
    function closeDropdown() {
      dropdownMenu.classList.remove("show");
      dropdownToggle.classList.remove("active");
      isOpen = false;
    }

    // Close dropdown when clicking outside
    document.addEventListener("click", function (e) {
      if (
        !dropdownToggle.contains(e.target) &&
        !dropdownMenu.contains(e.target)
      ) {
        closeDropdown();
      }
    });

    // Keyboard navigation
    dropdownToggle.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        if (isOpen) {
          closeDropdown();
        } else {
          openDropdown();
        }
      }
    });

    // Close dropdown on mobile when clicking a link
    const dropdownLinks = dropdownMenu.querySelectorAll("a");
    dropdownLinks.forEach((link) => {
      link.addEventListener("click", function () {
        closeDropdown();
      });
    });

    // Prevent dropdown from closing when clicking inside it
    dropdownMenu.addEventListener("click", function (e) {
      e.stopPropagation();
    });

    // Close dropdown on escape key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && isOpen) {
        closeDropdown();
      }
    });
  }
});
