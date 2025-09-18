document.addEventListener("DOMContentLoaded", () => {
  const authContainer = document.getElementById("auth-container");
  const loginTab = document.getElementById("login-tab");
  const signupTab = document.getElementById("signup-tab");
  const loginForm = document.getElementById("login-form");
  const signupForm = document.getElementById("signup-form");
  const forgotPasswordLink = document.getElementById("forgot-password-link");
  //const forgotPasswordModal = document.getElementById("forgot-password-modal");
  // const closeForgotModalBtn = document.getElementById("close-forgot-modal");
  const closeAuthBtn = document.getElementById("close-auth");

  // Tab switching
  loginTab.addEventListener("click", () => {
    loginTab.classList.add("active");
    signupTab.classList.remove("active");
    loginForm.classList.add("active");
    signupForm.classList.remove("active");
  });

  signupTab.addEventListener("click", () => {
    signupTab.classList.add("active");
    loginTab.classList.remove("active");
    signupForm.classList.add("active");
    loginForm.classList.remove("active");
  });

  // Close auth container
  closeAuthBtn.addEventListener("click", () => {
    window.location.href = "../index.php";
  });

  // Form submission handling
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.classList.add("loading");
      submitBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Processing...';
    });
  });

  // Password confirmation validation
  const confirmPasswordInput = document.getElementById(
    "signup-confirm-password"
  );
  const passwordInput = document.getElementById("signup-password");

  if (confirmPasswordInput && passwordInput) {
    confirmPasswordInput.addEventListener("input", () => {
      if (passwordInput.value !== confirmPasswordInput.value) {
        confirmPasswordInput.setCustomValidity("Passwords do not match");
      } else {
        confirmPasswordInput.setCustomValidity("");
      }
    });
  }
});
document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get("tab");

  if (tab === "signup") {
    document.getElementById("signup-tab").classList.add("active");
    document.getElementById("signup-form").classList.add("active");
    document.getElementById("login-tab").classList.remove("active");
    document.getElementById("login-form").classList.remove("active");
  } else if (tab === "login") {
    document.getElementById("login-tab").classList.add("active");
    document.getElementById("login-form").classList.add("active");
    document.getElementById("signup-tab").classList.remove("active");
    document.getElementById("signup-form").classList.remove("active");
  }
});
