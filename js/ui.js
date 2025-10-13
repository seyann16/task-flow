// UI Management
class UIManager {
    getCurrentFilters() {
        return {
            search: document.getElementById('searchInput')?.value || '',
            status: document.getElementById('statusFilter')?.value || '',
            priority: document.getElementById('priorityFilter')?.value || '',
            sort_by: document.getElementById('sortFilter')?.value || 'created_at'
        };
    }
}