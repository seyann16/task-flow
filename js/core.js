// Core Application Framework
class TaskManagerApp {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupTheme();
        this.setupFilters();
        console.log('Task Manager App Initialized');
    }
    
    // Theme Management
    setupTheme() {
        const themeToggle = document.getElementById('themeToggle');
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        document.documentElement.setAttribute('data-theme', currentTheme);
        
        themeToggle?.addEventListener('click', () => {
            const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            this.updateThemeIcon(newTheme);
        });
        
        this.updateThemeIcon(currentTheme);
    }
    
    updateThemeIcon(theme) {
        const themeIcon = document.querySelector('#themeToggle i');
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }
    
    // Filter Management
    setupFilters() {
        const filters = ['searchInput', 'statusFilter', 'priorityFilter', 'sortFilter'];
        
        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            element?.addEventListener('change', () => this.applyFilters());
        });
        
        // Debounce search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => this.applyFilters(), 500);
            });
        }
    }
    
    applyFilters() {
        const params = new URLSearchParams();
        
        const search = document.getElementById('searchInput')?.value;
        const status = document.getElementById('statusFilter')?.value;
        const priority = document.getElementById('priorityFilter')?.value;
        const sortBy = document.getElementById('sortFilter')?.value;
        
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        if (priority) params.set('priority', priority);
        if (sortBy) params.set('sort_by', sortBy);
        
        window.location.href = `index.php?${params.toString()}`;
    }
    
    // Flash Message System
    showFlashMessage(message, type = 'success', duration = 5000) {
        const container = document.getElementById('flashMessages');
        if (!container) return;
        
        const messageEl = document.createElement('div');
        messageEl.className = `flash-message flash-${type}`;
        messageEl.innerHTML = `
            <div class="flash-content">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                <span>${message}</span>
            </div>
            <button class="flash-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(messageEl);
        
        if (duration > 0) {
            setTimeout(() => {
                if (messageEl.parentElement) {
                    messageEl.remove();
                }
            }, duration);
        }
    }
    
    // Modal Management
    setupEventListeners() {
        // Modal triggers
        document.getElementById('addTaskBtn')?.addEventListener('click', () => this.openTaskModal());
        document.getElementById('emptyAddTaskBtn')?.addEventListener('click', () => this.openTaskModal());
        
        // Modal close handlers
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', () => this.closeTaskModal());
        });
        
        // Close modal on backdrop click
        document.getElementById('taskModal')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                this.closeTaskModal();
            }
        });
        
        // Form submission
        document.getElementById('taskForm')?.addEventListener('submit', (e) => this.handleTaskSubmit(e));
    }
    
    openTaskModal(taskId = null) {
        const modal = document.getElementById('taskModal');
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');
        
        if (taskId) {
            modalTitle.textContent = 'Edit Task';
            submitText.textContent = 'Update Task';
            this.loadTaskData(taskId);
        } else {
            modalTitle.textContent = 'Add New Task';
            submitText.textContent = 'Create Task';
            document.getElementById('taskForm').reset();
            document.getElementById('taskId').value = '';
        }
        
        modal.classList.add('active');
    }
    
    closeTaskModal() {
        document.getElementById('taskModal').classList.remove('active');
    }
    
    loadTaskData(taskId) {
        // This will be implemented with AJAX
        console.log('Loading task data for:', taskId);
    }
    
    async handleTaskSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = document.getElementById('submitSpinner');
        
        // Show loading state
        submitBtn.disabled = true;
        spinner.style.display = 'inline-block';
        
        try {
            // Simulate API call - will be replaced with actual AJAX
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            this.showFlashMessage('Task saved successfully!', 'success');
            this.closeTaskModal();
            
            // Refresh page to show updated tasks
            setTimeout(() => window.location.reload(), 1000);
            
        } catch (error) {
            this.showFlashMessage('Error saving task. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            spinner.style.display = 'none';
        }
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.taskApp = new TaskManagerApp();
});