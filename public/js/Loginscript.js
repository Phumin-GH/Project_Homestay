function toggleToForgot() {
  document.querySelector(".login-form").style.display = "none";
  document.querySelector(".signup-form").style.display = "none";
  document.querySelector(".forgot-form").style.display = "block";
}
function toggleToLogin() {
  document.querySelector(".login-form").style.display = "block";
  document.querySelector(".signup-form").style.display = "none";
  document.querySelector(".forgot-form").style.display = "none";
}
