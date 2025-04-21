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



