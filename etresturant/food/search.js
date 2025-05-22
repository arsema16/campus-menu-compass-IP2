document.addEventListener("DOMContentLoaded", () => {
  const searchIcon = document.getElementById("search-icon");
  const searchBar = document.getElementById("search-bar");
  const searchInput = document.getElementById("search-input");
  const searchResults = document.getElementById("search-results");

  // Toggle search bar visibility on icon click
  searchIcon.addEventListener("click", (e) => {
    e.preventDefault();
    searchBar.classList.toggle("active");
    searchInput.focus();
  });

  // Handle input events in the search input
  searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim();

    if (query.length === 0) {
      searchResults.innerHTML = "";
      return;
    }

    // Change the search URL to match your PHP script
    fetch(`search.php?query=${encodeURIComponent(query)}`)
      .then((res) => res.json())
      .then((data) => {
        searchResults.innerHTML = "";

        if (data.length === 0) {
          searchResults.innerHTML = "<p>No matches found.</p>";
          return;
        }

        // Show all matching results with name, place, and price
        data.forEach((item) => {
          const div = document.createElement("div");
          div.classList.add("search-item");
          div.innerHTML = `
            <strong>${item.name}</strong> — ${item.place_name} — ${item.price}
          `;
          searchResults.appendChild(div);
        });
      })
      .catch((err) => {
        searchResults.innerHTML = `<p>Fetch error: ${err.message}</p>`;
      });
  });
});
