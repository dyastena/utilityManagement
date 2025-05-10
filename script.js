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

document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const body = document.body;

    // Check saved preference and apply dark mode
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled') {
        body.classList.add('dark-mode');
        if (darkModeToggle) darkModeToggle.checked = true; // Sync toggle state
    }

    // Toggle dark mode on checkbox change
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', () => {
            if (darkModeToggle.checked) {
                body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
        });
    }
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

// Function to handle button selection and toggle styles
function selectStatus(button, status) {
    const room = button.closest('.room'); // Get the parent room container
    const statusButtons = room.querySelectorAll('.status-btn'); // Get all buttons in the same room
    const hiddenInput = room.querySelector('.area-status'); // now based on class
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

    // Limit the number of comment inputs to 1
    if (existingInputs.length >= 1) {
        return; // Stop adding new inputs if the limit is reached
    }

    const lastInput = existingInputs[existingInputs.length - 1];

    // Only add a new input if the last one has text or if no inputs exist
    if (!lastInput || lastInput.value.trim() !== "") {
        const newInput = document.createElement('input');
        newInput.type = "text";
        newInput.name = `comment_${existingInputs.length}`;
        newInput.name = `areas[${container.closest('.room').querySelector('.area-status').name.match(/\d+/)[0]}][comments][]`;
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

// Function to apply the font size
function applyFontSize(fontSize) {
  const root = document.documentElement;

  if (fontSize === 'small') {
    root.style.fontSize = '12px';
  } else if (fontSize === 'medium') {
    root.style.fontSize = '16px';
  } else if (fontSize === 'large') {
    root.style.fontSize = '20px';
  }

  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    sidebar.style.fontSize = '14px';
  }
}

// On page load, check localStorage for saved font size
document.addEventListener('DOMContentLoaded', function () {
  const savedFontSize = localStorage.getItem('fontSize') || 'medium'; // Default to 'medium'
  applyFontSize(savedFontSize);

  // If the font-size selector exists (e.g., on settings.php), set its value
  const fontSizeSelector = document.getElementById('font-size-selector');
  if (fontSizeSelector) {
    fontSizeSelector.value = savedFontSize;

    // Save font size to localStorage and apply it when the user changes it
    fontSizeSelector.addEventListener('change', function () {
      const fontSize = this.value;
      localStorage.setItem('fontSize', fontSize); // Save to localStorage
      applyFontSize(fontSize); // Apply the font size
    });
  }
});

// Initialize areas for the default selected floor
document.addEventListener('DOMContentLoaded', updateAreas);

document.addEventListener('DOMContentLoaded', function () {
  const savedFontSize = localStorage.getItem('fontSize') || '1rem'; // Default to 1rem
  document.documentElement.style.fontSize = savedFontSize;

  const fontSizeSlider = document.getElementById('font-size-slider');
  const fontSizeValue = document.getElementById('font-size-value');

  if (fontSizeSlider) {
    fontSizeSlider.value = parseFloat(savedFontSize) * 16; // Convert rem to px for slider
    fontSizeValue.textContent = savedFontSize;

    fontSizeSlider.addEventListener('input', function () {
      const fontSize = `${this.value / 16}rem`; // Convert px to rem
      fontSizeValue.textContent = fontSize;
      document.documentElement.style.fontSize = fontSize;
      localStorage.setItem('fontSize', fontSize);
    });
  }
});
