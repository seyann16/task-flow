// Modal Management
class ModalManager {
    // listener function for all events
    setupEventListeners() {
        // Opens the tasks modal if the add task button is clicked
        document.getElementById('addTaskBtn')?.addEventListener('click', () => this.openTaskModal());
        document.getElementById('emptyAddTaskButton')?.addEventListener('click', () => this.openTaskModal());

        // Select all close modal classes
        document.querySelectorAll('.close-modal').forEach(btn => {
            // Add event listener for each buttons
            // If the button is clicked the task modal will be closed
            btn.addEventListener('click', () => this.closeTaskModal());
        });

        // Gets the modal element by ID "taskModal"
        // Listens for click events anywhere within the modal (including its children)
        document.getElementById('taskModal')?.addEventListener('click', (e) => {
            // Clicking in gray area → e.target === e.currentTarget → closeTaskModal()
            // Clicking in white area → e.target !== e.currentTarget → nothing happens
            if (e.target === e.currentTarget) {
                this.closeTaskModal();
            }
        });

        // Form submission
        document.getElementById('taskForm')?.addEventListener('submit', (e) => taskApp.taskManager.handleTaskSubmit(e));
    }

    openTaskModal(taskId = null) {
        // Get the task modal container
        const modal = document.getElementById('taskModal');
        // Get the task modal title
        const modalTitle = document.getElementById('modalTitle');
        // Get the text in the submit button
        const submitText = document.getElementById('submitText');

        // Checks if task id exists or has value
        if (taskId) {
            // Change text contents
            modalTitle.textContent = 'Edit Task';
            submitText.textContent = 'Update Task';
            // Load the task data using the task id 
            taskApp.api.loadTaskData(taskId);
        } else {
            // Revert to a new task text contents
            modalTitle.textContent = 'Add New Task';
            submitText.textContent = 'Create Task';
            // Reset the task form
            document.getElementById('taskForm').reset();
            // Set the task id into empty
            document.getElementById('taskId').value = '';
        }

        // Add an active class name for styling
        modal.classList.add('active');
    }

    closeTaskModal() {
        // Remove the class name 'active' from the task modal element
        document.getElementById('taskModal').classList.remove('active');
    }
}