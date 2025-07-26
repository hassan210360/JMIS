document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const query = new URLSearchParams(new FormData(form)).toString();
        fetch("job_search.php?" + query)
            .then(response => response.text())
            .then(html => {
                document.querySelector("#job-results").innerHTML = html;
            })
            .catch(err => {
                console.error("Search failed", err);
            });
    });
});
