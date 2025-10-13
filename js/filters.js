// Filter Management
class FilterManager {    
    setupFilters() {
        // Initialize the filters
        const filters = [
            'searchInput',
            'statusFilter',
            'priorityFilter',
            'sortFilter'
        ];

        // Get the element for each filter ids
        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            // Get the filters once there is value detected from the element
            element?.addEventListener('change', () => this.applyFilters());
        });

        // Gets a reference to an HTML input element with ID "searchInput"
        const searchInput = document.getElementById('searchInput');
        // Checks if the element actually exists before proceeding (safety check)
        if (searchInput) {
            // Declares a variable to store the timeout ID (initially undefined)
            let timeout;
            // Listens for any input/typing in the search field
            searchInput.addEventListener('input', () => {
                // Cancels the previous timeout if the user types again
                clearTimeout(timeout);
                // Starts a new 500ms timer that will call applyFilters() when it completes
                timeout = setTimeout(() => this.applyFilters(), 500);
            });
        }
    }

    applyFilters() {
        // Initialize url search params object
        const params = new URLSearchParams();

        // Get the value to each inputs
        const search = document.getElementById('searchInput')?.value;
        const status = document.getElementById('statusFilter')?.value;
        const priority = document.getElementById('priorityFilter')?.value;
        const sortBy = document.getElementById('sortFilter')?.value;

        // Set the key, values in the params object
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        if (priority) params.set('priority', priority);
        if (sortBy) params.set('sort_by', sortBy);

        // Go to the url with search params
        window.location.href = `index.php?${params.toString()}`;
    }
}