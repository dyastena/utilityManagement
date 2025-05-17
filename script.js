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
});

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

    // Add areas for the selected floor
    if (areas[selectedFloor]) {
        areas[selectedFloor].forEach((area, index) => {
            const roomDiv = document.createElement('div');
            roomDiv.classList.add('room');

            roomDiv.innerHTML = `
                <label>${area}</label>
                <div class="status-buttons">
                    <button type="button" class="status-btn clean" onclick="selectStatus(this, 'Clean')">Clean</button>
                    <button type="button" class="status-btn needs-cleaning" onclick="selectStatus(this, 'Needs cleaning')">Needs Cleaning</button>
                    <button type="button" class="status-btn needs-attention" onclick="selectStatus(this, 'Needs attention')">Needs Attention</button>
                </div>
                <input type="hidden" name="area_${index}_status" value="">
                <div class="comment-container" style="display: none;"></div>
            `;

            areasContainer.appendChild(roomDiv);
        });
    }
}

// Function to handle button selection and toggle styles
function selectStatus(button, status) {
    const room = button.closest('.room'); // Get the parent room container
    const statusButtons = room.querySelectorAll('.status-btn'); // Get all buttons in the same room
    const hiddenInput = room.querySelector('input[type="hidden"]'); // Get the hidden input for status
    const commentContainer = room.querySelector('.comment-container'); // Get the container for comments

    // Reset all buttons to their default state
    statusButtons.forEach(btn => btn.classList.remove('active'));

    // Set the clicked button as active
    button.classList.add('active');

    // Update the hidden input value
    hiddenInput.value = status;

    // Show or hide the comment container based on the selected status
    if (status === "Clean") {
        commentContainer.style.display = "none"; // Hide the comment container
        commentContainer.innerHTML = ""; // Clear all comment inputs
    } else {
        commentContainer.style.display = "block"; // Show the comment container
        addCommentInput(commentContainer, status); // Add the first comment input with the appropriate placeholder
    }
}

function addCommentInput(container, status) {
    const existingInputs = container.querySelectorAll('.comment-input');

    // Limit the number of comment inputs to 4
    if (existingInputs.length >= 4) {
        return; // Stop adding new inputs if the limit is reached
    }

    const lastInput = existingInputs[existingInputs.length - 1];

    // Only add a new input if the last one has text or if no inputs exist
    if (!lastInput || lastInput.value.trim() !== "") {
        const newInput = document.createElement('input');
        newInput.type = "text";
        newInput.name = `comment_${existingInputs.length}`;
        newInput.classList.add('comment-input');
        newInput.required = true; // Make the input required
        newInput.style.marginTop = "10px";

        // Set placeholder based on the status
        if (status === "Needs cleaning") {
            newInput.placeholder = "e.g., Room 101 wet floor";
        } else if (status === "Needs attention") {
            newInput.placeholder = "e.g., Room 101 broken window";
        }

        // Add an event listener to check for text and dynamically add or remove inputs
        newInput.addEventListener('input', () => {
            if (newInput.value.trim() !== "") {
                addCommentInput(container, status); // Add a new input if the current one has text
            } else {
                removeEmptyInputs(container); // Remove empty inputs
            }
        });

        container.appendChild(newInput);
    }
}

function removeEmptyInputs(container) {
    const existingInputs = container.querySelectorAll('.comment-input');

    existingInputs.forEach(input => {
        if (input.value.trim() === "") {
            container.removeChild(input); // Remove the input if it is empty
        }
    });
}

// Initialize areas for the default selected floor
document.addEventListener('DOMContentLoaded', updateAreas);



