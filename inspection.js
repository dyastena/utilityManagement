document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const toggleButton = document.querySelector('.logo-container');

    toggleButton.addEventListener('click', () => {
        const isCollapsed = sidebar.getAttribute('data-collapsed') === 'true';
        sidebar.setAttribute('data-collapsed', !isCollapsed);
    });
});


function updateAreas() {
    const areasContainer = document.getElementById('areas-container');
    const selectedFloor = document.getElementById('location').value;

    // Define areas for each floor
    const areas = {
        "1st Floor": ["Lobby", "Reception", "Restroom"],
        "2nd Floor": ["Conference Room", "Office 201", "Office 202"],
        "3rd Floor": ["Cafeteria", "Pantry", "Restroom"]
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
                    <div class="status-options">
                        <label><input type="radio" name="area_${index}_status" value="Clean" required> <span class="status-dot clean"></span> Clean</label>
                        <label><input type="radio" name="area_${index}_status" value="Needs cleaning" required> <span class="status-dot needs-cleaning"></span> Needs cleaning</label>
                        <label><input type="radio" name="area_${index}_status" value="Not applicable" required> <span class="status-dot not-applicable"></span> Not applicable</label>
                    </div>
                    <input type="text" name="area_${index}_comment" placeholder="Comment">
                `;

                areasContainer.appendChild(roomDiv);
            });
        }
    }

    // Initialize areas for the default selected floor
    document.addEventListener('DOMContentLoaded', updateAreas);