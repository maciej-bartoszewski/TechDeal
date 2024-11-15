document.addEventListener("DOMContentLoaded", function () {
    const toggleFiltersBtn = document.querySelector(".toggle_filters_btn");
    const toggleSortBtn = document.querySelector(".toggle_sort_btn");
    const filterSection = document.querySelector(".filter_section");
    const sortSection = document.querySelector(".sort_section");

    if (toggleFiltersBtn) {
        toggleFiltersBtn.addEventListener("click", function () {
            if (filterSection.style.display === "none" || filterSection.style.display === "") {
                filterSection.style.display = "flex";
                sortSection.style.display = "none";
            } else {
                filterSection.style.display = "none";
            }
        });
    }

    if (toggleSortBtn) {
        toggleSortBtn.addEventListener("click", function () {
            if (sortSection.style.display === "none" || sortSection.style.display === "") {
                sortSection.style.display = "flex";
                filterSection.style.display = "none";
            } else {
                sortSection.style.display = "none";
            }
        });
    }
});