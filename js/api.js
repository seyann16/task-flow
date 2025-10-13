// API Helper Methods
class ApiManager {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
    }

    async apiRequest(endpoint, options = {}) {
        const url = endpoint.startsWith('http') ? endpoint : this.baseUrl + endpoint;

        const config = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...options
        };

        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            throw error;
        }
    }

    async loadTaskData(taskId) {
        try {
            const data = await this.apiRequest(`?id=${taskId}`, {
                method: 'GET'
            });

            if (data.success && data.task) {
                taskApp.populateTaskForm(data.task);
            } else {
                taskApp.showFlashMessage('Failed to load task data', 'error');
            }
        } catch (error) {
            taskApp.showFlashMessage('Error loading task: ' + error.message, 'error');
        }
    }

    async toggleTaskStatus(checkbox) {
        const taskId = checkbox.dataset.taskId;
        const newStatus = checkbox.checked ? 'completed' : 'pending';

        try {
            const result = await this.apiRequest('', {
                method: 'PUT',
                body: {
                    id: taskId,
                    status: newStatus
                }
            });

            if (result.success) {
                const taskCard = checkbox.closest('.task-card');
                const statusLabel = taskCard.querySelector('.status-label');

                if (statusLabel) {
                    statusLabel.textContent = newStatus === 'completed' ? 'Completed' : 'Mark Complete';

                    if (newStatus === 'completed') {
                        taskCard.style.opacity = '0.7';
                        statusLabel.style.color = 'var(--success-color)';
                    } else {
                        taskCard.style.opacity = '1';
                        statusLabel.style.color = '';
                    }
                }
                taskApp.refreshStats();
                taskApp.showFlashMessage(`Task marked as ${newStatus}`, 'success');
            } else {
                // Revert checkbox on error
                checkbox.checked = !checkbox.checked;
                throw new Error(result.message);
            }
        } catch (error) {
            taskApp.showFlashMessage('Error updating task: ' + error.message, 'error');
        }
    }

    async deleteTask(taskId, taskCard) {
        try {
            const result = await this.apiRequest('', {
                method: 'DELETE',
                body: { id: taskId }
            });

            if (result.success) {
                // Add fade-out animation
                taskCard.style.transform = 'scale(0.8)';
                taskCard.style.opacity = '0';

                setTimeout(() => {
                    taskCard.remove();
                    taskApp.checkEmptyState();
                    taskApp.refreshStats();
                }, 300);

                taskApp.showFlashMessage(result.message, 'success');
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            taskApp.showFlashMessage('Error deleting task: ' + error.message, 'error');
        }
    }
}