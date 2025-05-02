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

function openForgotPasswordModal() {
  document.getElementById("forgotPasswordModal").style.display = "block";
}

function closeForgotPasswordModal() {
  document.getElementById("forgotPasswordModal").style.display = "none";
  document.getElementById("email-step").style.display = "block";
  document.getElementById("otp-step").style.display = "none";
  document.getElementById("forgot-password-message").textContent = "";
}

function handleForgotPassword(event) {
  event.preventDefault();

  // Simulate email submission and show OTP step
  const email = document.getElementById("forgot-email").value;

  // Simulate a delay for email submission (replace this with actual server call)
  setTimeout(() => {
    document.getElementById("email-step").style.display = "none";
    document.getElementById("otp-step").style.display = "block";
    document.getElementById("forgot-password-message").textContent =
      "OTP has been sent to your email.";
    document.getElementById("forgot-password-message").style.color = "green";
  }, 500); // Simulated delay
}

function sendOtp(event) {
  event.preventDefault();
  const email = document.getElementById("forgot-email").value;

  // Send email to the server for OTP generation
  fetch("send-otp.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("email-step").style.display = "none";
        document.getElementById("otp-step").style.display = "block";
        document.getElementById("otp-message").textContent =
          "OTP has been sent to your email.";
      } else {
        document.getElementById("otp-message").textContent =
          "Email not found in the database.";
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function verifyOtp(event) {
  event.preventDefault();
  const otp = document.getElementById("otp-input").value;

  // Send OTP to the server for verification
  fetch("verify-otp.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ otp }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("otp-message").textContent =
          "OTP verified successfully.";
        // Redirect or perform further actions
      } else {
        document.getElementById("otp-message").textContent =
          "Invalid OTP. Please try again.";
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#loginForm"); // Select the login form
    const errorDiv = document.querySelector("#error"); // Select the error div

    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(form);

        try {
            const response = await fetch("login.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();

            if (result.status === "error") {
                errorDiv.textContent = result.message; // Display the error message
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



