function togglePassword(id, el) {
  const input = document.getElementById(id);
  const img = el.querySelector('img'); // Get the image inside the button

  if (input.type === "password") {
    input.type = "text";  // Show password
    img.src = "source/eyesClosed.png";  // Image for "show password"
    img.alt = "Hide Password";
  } else {
    input.type = "password";  // Hide password
    img.src = "source/eyesOpened.png";  // Image for "hide password"
    img.alt = "Show Password";
  }
}

function validateForm() {
  const firstName = document.getElementById("first_name").value.trim();
  const lastName = document.getElementById("last_name").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;
  const errorDiv = document.getElementById("error");

  errorDiv.textContent = "";

  if (!firstName || !lastName || !email || !password || !confirmPassword) {
    errorDiv.textContent = "All fields are required.";
    return false;
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    errorDiv.textContent = "Please enter a valid email address.";
    return false;
  }

  if (password.length < 6) {
    errorDiv.textContent = "Password must be at least 6 characters long.";
    return false;
  }

  if (password !== confirmPassword) {
    errorDiv.textContent = "Passwords do not match.";
    return false;
  }

  return true;
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#registerForm");
    const errorDiv = document.querySelector("#error"); // Select the error div by its ID

    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // Prevent the default form submission

        const formData = new FormData(form);

        try {
            const response = await fetch("register.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();

            if (result.status === "error") {
                errorDiv.textContent = result.message; // Update the error div with the error message
                errorDiv.style.display = "block"; // Ensure the error div is visible
            } else if (result.status === "success") {
                window.location.href = result.redirect; // Redirect on success
            }
        } catch (error) {
            errorDiv.textContent = "An unexpected error occurred. Please try again.";
            errorDiv.style.display = "block"; // Ensure the error div is visible
        }
    });
});
