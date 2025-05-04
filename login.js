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
  const forgotPasswordModal = document.getElementById('forgotPasswordModal');
  forgotPasswordModal.style.display = 'block';

  // Reset the modal state
  resetForgotPasswordModal();
}

function closeForgotPasswordModal() {
  const forgotPasswordModal = document.getElementById('forgotPasswordModal');
  forgotPasswordModal.style.display = 'none';
}

function resetForgotPasswordModal() {
  // Hide all steps except the email input step
  document.getElementById('email-step').style.display = 'block';
  document.getElementById('otp-notification').style.display = 'none';
  document.getElementById('otp-step').style.display = 'none';
  document.getElementById('change-password-step').style.display = 'none';

  // Clear any input fields
  document.getElementById('forgot-email').value = '';
  document.getElementById('otp-input').value = '';
  document.getElementById('new-password').value = '';
  document.getElementById('confirm-password').value = '';
}

function handleForgotPassword(event) {
  event.preventDefault();

  const email = document.getElementById('forgot-email').value;
  console.log("Email entered:", email); // Log the email

  // Generate a 6-digit OTP
  const otp = Math.floor(100000 + Math.random() * 900000); // Generates a random 6-digit number
  console.log(`Generated OTP: ${otp}`); // For debugging purposes

  // Send OTP to the backend for storage
  fetch('storeOtp.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, otp }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert('OTP has been sent to your email.');
        document.getElementById('otp-notification').style.display = 'block';
      } else {
        alert('Failed to send OTP. Please try again.');
      }
    })
    .catch((error) => {
      console.error('Error:', error);
      alert('An error occurred. Please try again.');
    });
}

let countdownInterval;

function goToOtpStep() {
  // Hide the OTP notification
  const otpNotification = document.getElementById('otp-notification');
  otpNotification.style.display = 'none';

  // Show the OTP input step
  const otpStep = document.getElementById('otp-step');
  otpStep.style.display = 'block';

  // Start the countdown timer
  startCountdown(90); // 90 seconds = 1 minute and 30 seconds
}

function startCountdown(seconds) {
  const countdownTimer = document.getElementById('countdown-timer');
  const resendOtpDiv = document.getElementById('resend-otp');
  const resendOtpLink = resendOtpDiv.querySelector('a');

  let remainingTime = seconds;

  // Show the countdown timer and disable the resend link
  countdownTimer.style.display = 'block';
  resendOtpDiv.style.display = 'none';
  resendOtpLink.style.pointerEvents = 'none'; // Disable clicking
  resendOtpLink.style.color = 'gray'; // Change color to indicate it's disabled

  countdownInterval = setInterval(() => {
    const minutes = Math.floor(remainingTime / 60);
    const seconds = remainingTime % 60;

    countdownTimer.textContent = `Time remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

    if (remainingTime <= 0) {
      clearInterval(countdownInterval);
      countdownTimer.style.display = 'none';
      resendOtpDiv.style.display = 'block';
      resendOtpLink.style.pointerEvents = 'auto'; // Enable clicking
      resendOtpLink.style.color = '#007bff'; // Restore link color
    }

    remainingTime--;
  }, 1000);
}

function resendOtp() {
  // Simulate resending OTP (replace with actual API call)
  console.log('Resending OTP...');

  // Restart the countdown timer
  startCountdown(90); // Restart with 90 seconds
}

function sendOtp(event) {
  event.preventDefault();
  const email = document.getElementById("forgot-email").value;

  fetch("send-otp.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Response from send-otp.php:", data); // Log the response
      if (data.success) {
        alert("An OTP has been sent to your email. Please check your inbox.");
        document.getElementById("otp-notification").style.display = "block";
      } else {
        alert(data.error || "Failed to send OTP. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred. Please try again.");
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

function handleOtpSubmit(event) {
  event.preventDefault();

  const otpInput = document.getElementById('otp-input').value;

  // Simulate OTP verification (replace with actual API call)
  console.log(`Verifying OTP: ${otpInput}`);

  // Simulate a delay for OTP verification (e.g., API response)
  setTimeout(() => {
    const isOtpValid = true; // Replace with actual OTP validation logic

    if (isOtpValid) {
      // Hide the OTP input step
      const otpStep = document.getElementById('otp-step');
      otpStep.style.display = 'none';

      // Show the Change Password step
      const changePasswordStep = document.getElementById('change-password-step');
      changePasswordStep.style.display = 'block';
    } else {
      alert('Invalid OTP. Please try again.');
    }
  }, 10); // Simulate a 1-second delay for verification
}

function handleChangePassword(event) {
  event.preventDefault();

  const newPassword = document.getElementById('new-password').value;
  const confirmPassword = document.getElementById('confirm-password').value;

  if (newPassword !== confirmPassword) {
    alert('Passwords do not match. Please try again.');
    return;
  }

  // Simulate password change (replace with actual API call)
  console.log('Password changed successfully:', newPassword);

  // Show success message
  alert('Your password has been changed successfully!');

  // Redirect to the login form (front page)
  const forgotPasswordModal = document.getElementById('forgotPasswordModal');
  forgotPasswordModal.style.display = 'none'; // Close the modal

  // Optionally clear all input fields
  document.getElementById('new-password').value = '';
  document.getElementById('confirm-password').value = '';
  document.getElementById('forgot-email').value = '';
  document.getElementById('otp-input').value = '';

  // Reset the modal state to the email input step
  resetForgotPasswordModal();
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



