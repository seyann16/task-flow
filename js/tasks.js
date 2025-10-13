// Task Operations and UI Updates
class TaskManager {
    constructor() {
        this.api = new ApiManager('api/tasks.php');
    }

    // Setup task-related event listeners
    setupTaskInteractions() {
        // Status toggle
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('status-toggle')) {
                this.api.toggleTaskStatus(e.target);
            }
        });

        // Edit task buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.edit-task')) {
                const taskCard = e.target.closest('.task-card');
                const taskId = taskCard?.dataset.taskId;
                if (taskId) taskApp.openTaskModal(taskId);
            }
        });

        // Delete task buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-task')) {
                const taskCard = e.target.closest('.task-card');
                const taskId = taskCard?.dataset.taskId;
                if (taskId) this.confirmDeleteTask(taskId, taskCard);
            }
        });
    }

    populateTaskForm(task) {
        document.getElementById('taskId').value = task.id;
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskDescription').value = task.description || '';
        document.getElementById('taskPriority').value = task.priority;
        document.getElementById('taskDueDate').value = task.due_date || '';
    }

    async handleTaskSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const taskId = formData.get('task_id');
        const taskData = {
            title: formData.get('title'),
            description: formData.get('description'),
            priority: formData.get('priority'),
            due_date: formData.get('due_date') || null
        };

        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = document.getElementById('submitSpinner');

        // Show loading state
        submitBtn.disabled = true;
        spinner.style.display = 'inline-block';

        try {
            let result;

            if (taskId) {
                // Update existing task
                taskData.id = taskId;
                result = await this.api.apiRequest('', {
                    method: 'PUT',
                    body: taskData
                });
            } else {
                // Create new task
                result = await this.api.apiRequest('', {
                    method: 'POST',
                    body: taskData
                });
            }

            if (result.success) {
                taskApp.showFlashMessage(result.message, 'success');
                taskApp.closeTaskModal();
                this.refreshTasks();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            taskApp.showFlashMessage('Error saving task: ' + error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            spinner.style.display = 'none';
        }
    }

    async confirmDeleteTask(taskId, taskCard) {
        const confirmed = await taskApp.showConfirmationModal(
            'Delete Task',
            'Are you sure you want to delete this task? This action cannot be undone.',
            'Delete',
            'Cancel'
        );

        if (confirmed) {
            await this.api.deleteTask(taskId, taskCard);
        }
    }

    async refreshTasks() {
        try {
            const filters = taskApp.getCurrentFilters();
            const result = await this.api.apiRequest(`?${new URLSearchParams(filters)}`);

            if (result.success) {
                this.updateTasksGrid(result.tasks);
                this.refreshStats();
            }
        } catch (error) {
            console.error('Error refreshing tasks:', error);
        }
    }

    updateTasksGrid(tasks) {
        const tasksGrid = document.getElementById('tasksGrid');
        
        if (!tasksGrid) return;

        if (tasks.length === 0) {
            tasksGrid.innerHTML = this.getEmptyStateHTML();
            return;
        }

        tasksGrid.innerHTML = tasks.map(task => this.createTaskCardHTML(task)).join('');
    }

    createTaskCardHTML(task) {
        const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }) : null;

        return `
            <div class="task-card" data-task-id="${task.id}" data-priority="${task.priority}">
                <div class="task-header">
                    <div class="task-meta">
                        <span class="task-priority priority-${task.priority}">
                            ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
                        </span>
                        <span class="task-date">
                            ${new Date(task.created_at).toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric'
                            })}
                        </span>
                    </div>
                    <div class="task-actions">
                        <button class="btn-icon edit-task" title="Edit Task">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon delete-task" title="Delete Task">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="task-body">
                    <h4 class="task-title">${this.escapeHtml(task.title)}</h4>
                    ${task.description ? `<p class="task-description">${this.escapeHtml(task.description)}</p>` : ''}
                    ${dueDate ? `
                        <div class="task-due-date">
                            <i class="fas fa-calendar"></i>
                            Due: ${dueDate}
                        </div>
                    ` : ''}
                </div>

                <div class="task-footer">
                    <div class="task-status">
                        <label class="checkbox-container">
                            <input type="checkbox" class="status-toggle" ${task.status === 'completed' ? 'checked' : ''} data-task-id="${task.id}">
                            <span class="checkmark"></span>
                            <span class="status-label">
                                ${task.status === 'completed' ? 'Completed' : 'Mark Complete'}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        `;
    }

    getEmptyStateHTML() {
        return `
            <div class="empty-state">
                <i class="fas fa-clipboard-list empty-icon"></i>
                <h3>No tasks found</h3>
                <p>Get started by creating your first task!</p>
                <button class="btn btn-primary" id="emptyAddTaskBtn">
                    <i class="fas fa-plus"></i>
                    Add Your First Task
                </button>
            </div>
        `;
    }

    checkEmptyState() {
        const tasksGrid = document.getElementById('tasksGrid');
        if (tasksGrid && tasksGrid.children.length === 0) {
            tasksGrid.innerHTML = this.getEmptyStateHTML();
        }
    }

    async refreshStats() {
        try {
            const result = await this.api.apiRequest('');
            
            if (result.success) {
                this.updateStatistics(result.stats || {});
            }
        } catch (error) {
            console.error('Error refreshing stats:', error);
        }
    }

    updateStatistics(stats) {
        const statElements = {
            'total': document.querySelector('.stat-card:nth-child(1) .stat-number'),
            'completed': document.querySelector('.stat-card:nth-child(2) .stat-number'),
            'pending': document.querySelector('.stat-card:nth-child(3) .stat-number'),
            'high_priority': document.querySelector('.stat-card:nth-child(4) .stat-number')
        };
        
        Object.entries(statElements).forEach(([key, element]) => {
            if (element) {
                // Add animation
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.textContent = stats[key] || '0';
                    element.style.transform = 'scale(1)';
                }, 150);
            }
        });
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
}