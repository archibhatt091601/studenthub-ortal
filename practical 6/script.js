let allEvents = [];
let currentPage = 1;
const recordsPerPage = 5;

fetch("events.json")
    .then(response => {
        if (!response.ok) {
            throw new Error("Could not load events.json");
        }
        return response.json();
    })
    .then(data => {
        allEvents = data;
        createCategories();
        displayEvents();
    })
    .catch(error => {
        document.getElementById("message").textContent = "Error: " + error.message;
    });

function createCategories() {
    const category = document.getElementById("category");
    const categories = [...new Set(allEvents.map(event => event.category))];

    categories.forEach(item => {
        const option = document.createElement("option");
        option.value = item;
        option.textContent = item;
        category.appendChild(option);
    });
}

function displayEvents() {
    let result = [...allEvents];

    const searchText = document.getElementById("search").value.toLowerCase();
    const selectedCategory = document.getElementById("category").value;
    const sortValue = document.getElementById("sort").value;

    result = result.filter(event =>
        event.title.toLowerCase().includes(searchText)
    );

    if (selectedCategory !== "all") {
        result = result.filter(event => event.category === selectedCategory);
    }

    if (sortValue === "title") {
        result.sort((a, b) => a.title.localeCompare(b.title));
    }

    if (sortValue === "date") {
        result.sort((a, b) => new Date(a.date) - new Date(b.date));
    }

    const start = (currentPage - 1) * recordsPerPage;
    const pageData = result.slice(start, start + recordsPerPage);

    const eventsDiv = document.getElementById("events");
    eventsDiv.innerHTML = "";

    pageData.forEach(event => {
        eventsDiv.innerHTML += `
            <div class="card">
                <h2>${event.title}</h2>
                <p><b>Category:</b> ${event.category}</p>
                <p><b>Date:</b> ${event.date}</p>
                <p><b>Location:</b> ${event.location}</p>
            </div>
        `;
    });

    document.getElementById("message").textContent =
        result.length + " event(s) found.";

    document.getElementById("page").textContent = "Page " + currentPage;

    document.getElementById("prev").disabled = currentPage === 1;
    document.getElementById("next").disabled =
        start + recordsPerPage >= result.length;
}

document.getElementById("search").addEventListener("input", () => {
    currentPage = 1;
    displayEvents();
});

document.getElementById("category").addEventListener("change", () => {
    currentPage = 1;
    displayEvents();
});

document.getElementById("sort").addEventListener("change", () => {
    currentPage = 1;
    displayEvents();
});

document.getElementById("prev").addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        displayEvents();
    }
});

document.getElementById("next").addEventListener("click", () => {
    currentPage++;
    displayEvents();
});
