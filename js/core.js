// Core Application Framework
class TaskManagerApp {
    constructor() {
        this.baseUrl = 'api/tasks.php';
        
        // Initialize managers
        this.api = new ApiManager(this.baseUrl);
        this.taskManager = new TaskManager();
        this.ui = new UIManager();
        this.theme = new ThemeManager();
        this.filters = new FilterManager();
        this.modals = new ModalManager();
        this.flash = new FlashManager();
        this.utils = new Utils();
        
        this.init();
    }
    
    init() {
        this.modals.setupEventListeners();
        this.theme.setupTheme();
        this.filters.setupFilters();
        this.taskManager.setupTaskInteractions();
        console.log('Task Manager App Initialized');
    }

    // Proxy methods to maintain original API
    setupEventListeners() { return this.modals.setupEventListeners(); }
    setupTheme() { return this.theme.setupTheme(); }
    setupFilters() { return this.filters.setupFilters(); }
    setupTaskInteractions() { return this.taskManager.setupTaskInteractions(); }
    
    openTaskModal(taskId = null) { return this.modals.openTaskModal(taskId); }
    closeTaskModal() { return this.modals.closeTaskModal(); }
    
    showFlashMessage(message, type, duration) { return this.flash.showFlashMessage(message, type, duration); }
    getCurrentFilters() { return this.ui.getCurrentFilters(); }
    
    populateTaskForm(task) { return this.taskManager.populateTaskForm(task); }
    handleTaskSubmit(e) { return this.taskManager.handleTaskSubmit(e); }
    refreshTasks() { return this.taskManager.refreshTasks(); }
    checkEmptyState() { return this.taskManager.checkEmptyState(); }
    refreshStats() { return this.taskManager.refreshStats(); }
    updateStatistics(stats) { return this.taskManager.updateStatistics(stats); }
    escapeHtml(unsafe) { return this.taskManager.escapeHtml(unsafe); }
    
    showConfirmationModal(title, message, confirmText, cancelText) { 
        return this.utils.showConfirmationModal(title, message, confirmText, cancelText); 
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.taskApp = new TaskManagerApp();
});