document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("search-input");
  const resultsBox = document.getElementById("search-results");

  input.addEventListener("input", () => {
    const query = input.value.trim();

    if (query.length === 0) {
      resultsBox.innerHTML = "";
      return;
    }

    fetch(`search_drinks.php?query=${encodeURIComponent(query)}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.error) {
          resultsBox.innerHTML = `<p>Error: ${data.error}</p>`;
          return;
        }

        if (data.length === 0) {
          resultsBox.innerHTML = "<p>No matches found.</p>";
          return;
        }

        resultsBox.innerHTML = data
          .map(
            (item) =>
              `<div class="search-result-item">
                 <strong>${item.name}</strong> — ${item.place_name} — ${item.price} ETB
               </div>`
          )
          .join("");
      })
      .catch((err) => {
        resultsBox.innerHTML = `<p>Fetch error: ${err.message}</p>`;
      });
  });
});
