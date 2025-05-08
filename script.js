document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const toggleButton = document.querySelector('.logo-container');

    // Check localStorage for the collapsed state and apply it
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    sidebar.setAttribute('data-collapsed', isCollapsed);

    toggleButton.addEventListener('click', () => {
        const isCollapsed = sidebar.getAttribute('data-collapsed') === 'true';
        sidebar.setAttribute('data-collapsed', !isCollapsed);

        // Save the collapsed state to localStorage
        localStorage.setItem('sidebarCollapsed', !isCollapsed);
    });

    const datePicker = document.getElementById("date-picker");

    // Function to format today's date as YYYY-MM-DD
    function getTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, "0");
        const day = String(today.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }

    // Check if a date is stored in sessionStorage
    const storedDate = sessionStorage.getItem("selectedDate");

    if (storedDate) {
        // Set the stored date as the value of the date picker
        datePicker.value = storedDate;
    } else {
        // Set today's date as the default value
        datePicker.value = getTodayDate();
    }

    // Save the selected date to sessionStorage when the user changes it
    window.saveDate = () => {
        sessionStorage.setItem("selectedDate", datePicker.value);
    };

    datePicker.addEventListener("change", () => {
        sessionStorage.setItem("selectedDate", datePicker.value);
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const datePicker = document.getElementById("date-picker");
    const shiftSelect = document.getElementById("shift");

    // Retrieve the stored date and shift from sessionStorage
    const storedDate = sessionStorage.getItem("selectedDate");
    const storedShift = sessionStorage.getItem("selectedShift");

    if (storedDate) {
        datePicker.value = storedDate;
    }

    if (storedShift) {
        shiftSelect.value = storedShift;
    }
});

function saveDate() {
    const datePicker = document.getElementById("date-picker");
    const shiftSelect = document.getElementById("shift");
    const selectedDate = datePicker.value;
    const selectedShift = shiftSelect.value;

    // Save the selected date in sessionStorage
    sessionStorage.setItem("selectedDate", selectedDate);
    sessionStorage.setItem("selectedShift", selectedShift);

    // Send the selected date to the server
    fetch("save_date.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ selectedDate }),
    });

    // Submit the form to update the table dynamically
    document.querySelector("form").submit();
}

function updateAreas() {
    const areasContainer = document.getElementById('areas-container');
    const selectedFloor = document.getElementById('location').value;

    // Define areas for each floor
    const areas = {
        "1st Floor": ["Hallway", "Canteen", "Function Room", "Comfort Room", "Ceiling"],
        "2nd Floor": [],
        "3rd Floor": ["Cafeteria", "Pantry", "Restroom"],
        "4th Floor": ["Library", "Study Room", "Computer Room"],
        "5th Floor": ["Hallway", "Canteen", "Function Room", "Comfort Room", "Ceiling"],
        "6th Floor": ["Hallway", "stairs", "Class Room", "Comfort Room", "Ceiling"],
        "7th Floor": [""],
        "8th Floor": ["Hallway", "stairs", "Gym", "Comfort Room", "Auditorium"],
    };

    // Clear existing areas
    areasContainer.innerHTML = '';

    // Store selected floor areas in a variable
    const selectedAreas = areas[selectedFloor] || [];

    // Add areas for the selected floor
    if (selectedAreas.length > 0) {
        selectedAreas.forEach((area, index) => {
            const roomDiv = document.createElement('div');
            roomDiv.classList.add('room');

            roomDiv.innerHTML = `
                <label>${area}</label>
                <div class="status-buttons">
                    <button type="button" class="status-btn clean" onclick="selectStatus(this, 'Clean')">Clean</button>
                    <button type="button" class="status-btn needs-cleaning" onclick="selectStatus(this, 'Needs cleaning')">Needs Cleaning</button>
                    <button type="button" class="status-btn needs-attention" onclick="selectStatus(this, 'Needs attention')">Needs Attention</button>
                </div>
                <input type="hidden" name="areas[${index}][name]" value="${area}">
                <input type="hidden" name="areas[${index}][status]" class="area-status" value="">

                <div class="comment-container" style="display: none;"></div>
            `;

            areasContainer.appendChild(roomDiv);
        });
    }
    
    // Return selected areas if needed elsewhere
    return selectedAreas;
}

function selectStatus(button, status) {
    const room = button.closest(".room");
    const statusButtons = room.querySelectorAll(".status-btn");
    const hiddenInput = room.querySelector(".area-status");
    const commentContainer = room.querySelector(".comment-container");

    // Reset all buttons and set the clicked button as active
    statusButtons.forEach((btn) => btn.classList.remove("active"));
    button.classList.add("active");

    // Update the hidden input value
    hiddenInput.value = status;

    // Show or hide the comment container based on the status
    if (status === "Clean") {
        commentContainer.style.display = "none";
        commentContainer.innerHTML = ""; // Clear all comment inputs
    } else {
        commentContainer.style.display = "block";
        if (commentContainer.children.length === 0) {
            addCommentInput(commentContainer, status);
        }
    }
}

function addCommentInput(container, status) {
    const existingInputs = container.querySelectorAll(".comment-input");

    // Limit the number of comment inputs to 3
    if (existingInputs.length >= 3) return;

    const newInput = document.createElement("input");
    newInput.type = "text";
    newInput.name = `areas[${container.closest(".room").querySelector(".area-status").name.match(/\d+/)[0]}][comments][]`;
    newInput.classList.add("comment-input");
    newInput.style.marginTop = "10px";

    // Set placeholder based on the status
    newInput.placeholder =
        status === "Needs cleaning"
            ? "e.g., Room 101 wet floor"
            : "e.g., Room 101 broken window";

    // Make the first input required, but not the second or third
    newInput.required = existingInputs.length === 0;

    container.appendChild(newInput);
}

function removeEmptyInputs(container) {
    const existingInputs = container.querySelectorAll('.comment-input');

    existingInputs.forEach(input => {
        if (input.value.trim() === "") {
            container.removeChild(input); // Remove the input if it is empty
        }
    });

    // Ensure the first input is always required
    const updatedInputs = container.querySelectorAll('.comment-input');
    if (updatedInputs.length > 0) {
        updatedInputs[0].required = true; // First input is required
    }
}

function updateShiftDisplay() {
    const shiftSelect = document.getElementById('shift');
    const selectedOption = shiftSelect.options[shiftSelect.selectedIndex].text;

    // Update the display of the selected shift
    const timeDisplay = document.querySelector('.time-select span');
    if (timeDisplay) {
        timeDisplay.textContent = selectedOption;
    }
}

// Initialize areas for the default selected floor
document.addEventListener('DOMContentLoaded', updateAreas);

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const commentInputs = document.querySelectorAll(".comment-input");

    form.addEventListener("submit", (event) => {
        // Filter out empty comment inputs
        commentInputs.forEach((input) => {
            if (input.value.trim() === "") {
                input.remove(); // Remove empty comment inputs from the DOM
            }
        });
    });
});
