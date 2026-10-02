const pageSettings = {
  "events.html": {
    file: "events.json",
    cacheKey: "studenthub-events",
    filterField: "category",
    searchableFields: ["title", "venue", "description", "category"],
    cardType: "event"
  },
  "faq.html": {
    file: "faqs.json",
    cacheKey: "studenthub-faqs",
    filterField: "category",
    searchableFields: ["question", "answer", "category"],
    cardType: "faq"
  },
  "profile.html": {
    file: "students.json",
    cacheKey: "studenthub-students",
    filterField: "course",
    searchableFields: ["name", "enrollment", "course", "email"],
    cardType: "student"
  }
};

const pageName = window.location.pathname.split("/").pop();
const settings = pageSettings[pageName];

if (settings) {
  initializeDataBrowser(settings);
}

function initializeDataBrowser(config) {
  const searchInput = document.querySelector("[data-search]");
  const filterSelect = document.querySelector("[data-filter]");
  const sortSelect = document.querySelector("[data-sort]");
  const status = document.querySelector("[data-status]");
  const results = document.querySelector("[data-results]");
  const pagination = document.querySelector("[data-pagination]");
  const pageSize = 6;
  let records = [];
  let currentPage = 1;
  let sourceNotice = "";

  const loadRecords = async () => {
    sourceNotice = "";
    status.textContent = "Loading data…";
    results.replaceChildren();
    pagination.replaceChildren();

    try {
      const response = await fetch(config.file);
      if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`);
      }

      const data = await response.json();
      validateRecords(data, config);
      records = data;
      populateFilterOptions(filterSelect, records, config.filterField);
      try {
        localStorage.setItem(config.cacheKey, JSON.stringify(data));
      } catch (cacheError) {
        console.error("Data loaded, but the browser cache could not be updated.", cacheError);
      }
      render();
    } catch (error) {
      console.error(`Unable to load ${config.file}.`, error);
      const cachedRecords = readCache(config);
      if (cachedRecords) {
        records = cachedRecords;
        populateFilterOptions(filterSelect, records, config.filterField);
        sourceNotice = "Offline mode: showing the last saved copy from this browser. ";
        render();
      } else {
        status.textContent = `Could not load data: ${error.message}. Run this project from a local web server, then retry.`;
        const retryButton = document.createElement("button");
        retryButton.type = "button";
        retryButton.className = "retry-button";
        retryButton.textContent = "Retry";
        retryButton.addEventListener("click", loadRecords);
        status.append(" ", retryButton);
      }
    }
  };

  const render = () => {
    const query = searchInput.value.trim().toLocaleLowerCase();
    const selectedFilter = filterSelect.value;
    const sortValue = sortSelect.value;
    const [sortField, sortDirection] = sortValue.split("-");

    const filteredRecords = records
      .filter((record) => !selectedFilter || record[config.filterField] === selectedFilter)
      .filter((record) => config.searchableFields.some((field) =>
        String(record[field]).toLocaleLowerCase().includes(query)
      ))
      .sort((first, second) => {
        const firstValue = first[sortField];
        const secondValue = second[sortField];
        const comparison = typeof firstValue === "number"
          ? firstValue - secondValue
          : String(firstValue).localeCompare(String(secondValue), undefined, { numeric: true });
        return sortDirection === "desc" ? -comparison : comparison;
      });

    const pageCount = Math.max(1, Math.ceil(filteredRecords.length / pageSize));
    currentPage = Math.min(currentPage, pageCount);
    const start = (currentPage - 1) * pageSize;
    const visibleRecords = filteredRecords.slice(start, start + pageSize);

    results.replaceChildren(...visibleRecords.map((record) => createCard(record, config.cardType)));
    renderPagination(pagination, pageCount, currentPage, (nextPage) => {
      currentPage = nextPage;
      render();
    });

    if (!filteredRecords.length) {
      status.textContent = `${sourceNotice}No matching records. Try a different search or filter.`;
    } else {
      const firstResult = start + 1;
      const lastResult = Math.min(start + pageSize, filteredRecords.length);
      status.textContent = `${sourceNotice}Showing ${firstResult}–${lastResult} of ${filteredRecords.length} matching records (${records.length} total).`;
    }
  };

  searchInput.addEventListener("input", () => {
    currentPage = 1;
    render();
  });
  filterSelect.addEventListener("change", () => {
    currentPage = 1;
    render();
  });
  sortSelect.addEventListener("change", () => {
    currentPage = 1;
    render();
  });

  loadRecords();
}

function validateRecords(data, config) {
  if (!Array.isArray(data) || data.length === 0) {
    throw new Error("The JSON response must be a non-empty array.");
  }

  const requiredFields = ["id", config.filterField, ...config.searchableFields];
  if (data.some((record) => !record || requiredFields.some((field) => !(field in record)))) {
    throw new Error("The JSON response contains a record with missing required fields.");
  }
}

function populateFilterOptions(select, records, field) {
  const firstOption = select.options[0].cloneNode(true);
  select.replaceChildren(firstOption);
  const values = [...new Set(records.map((record) => record[field]))].sort((a, b) =>
    String(a).localeCompare(String(b))
  );

  values.forEach((value) => {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = value;
    select.append(option);
  });
}

function readCache(config) {
  try {
    const cachedData = localStorage.getItem(config.cacheKey);
    if (!cachedData) {
      return null;
    }

    const data = JSON.parse(cachedData);
    validateRecords(data, config);
    return data;
  } catch (cacheError) {
    console.error("Unable to read a valid cached copy of the data.", cacheError);
    return null;
  }
}

function renderPagination(container, pageCount, currentPage, onPageChange) {
  container.replaceChildren();
  if (pageCount <= 1) {
    return;
  }

  const addButton = (label, page, disabled, current = false) => {
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = label;
    button.disabled = disabled;
    if (current) {
      button.setAttribute("aria-current", "page");
    }
    button.addEventListener("click", () => onPageChange(page));
    container.append(button);
  };

  addButton("Previous", currentPage - 1, currentPage === 1);
  for (let page = 1; page <= pageCount; page += 1) {
    addButton(String(page), page, false, page === currentPage);
  }
  addButton("Next", currentPage + 1, currentPage === pageCount);
}

function createCard(record, type) {
  const card = document.createElement("article");
  card.className = "data-card";

  const title = document.createElement("h3");
  const content = document.createElement("div");

  if (type === "event") {
    title.textContent = record.title;
    appendDetail(content, "Date", formatDate(record.date));
    appendDetail(content, "Category", record.category);
    appendDetail(content, "Venue", record.venue);
    appendDetail(content, "About", record.description);
  } else if (type === "faq") {
    title.textContent = record.question;
    appendDetail(content, "Topic", record.category);
    appendDetail(content, "Answer", record.answer);
  } else {
    title.textContent = record.name;
    appendDetail(content, "Enrollment", record.enrollment);
    appendDetail(content, "Course", record.course);
    appendDetail(content, "Semester", record.semester);
    appendDetail(content, "Email", record.email);
  }

  card.append(title, content);
  return card;
}

function appendDetail(container, label, value) {
  const paragraph = document.createElement("p");
  const strong = document.createElement("strong");
  strong.textContent = `${label}: `;
  paragraph.append(strong, document.createTextNode(String(value)));
  container.append(paragraph);
}

function formatDate(dateString) {
  const date = new Date(`${dateString}T12:00:00`);
  return new Intl.DateTimeFormat(undefined, {
    year: "numeric",
    month: "long",
    day: "numeric"
  }).format(date);
}
