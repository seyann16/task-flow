<?php
include 'includes/config.php';
include 'includes/TaskManager.php';

$taskManager = new TaskManager($pdo);

// Get filters from query parameters
$filters = [
    'status' => $_GET['status'] ?? null,
    'priority' => $_GET['priority'] ?? null,
    'search' => $_GET['search'] ?? null,
    'sort_by' => $_GET['sort_by'] ?? 'created_at'
];

// Fetch tasks with filters
$tasksResult = $taskManager->getTasks($filters);
$tasks = $tasksResult['success'] ? $tasksResult['tasks'] : [];

// Get statistics
$stats = $taskManager->getStatistics();

$pageTitle = "Task Dashboard";
include 'includes/header.php';
?>
            <div class="dashboard-container">
                <!-- Statistics Overview -->
                <section class="stats-overview">
                    <div class="stat-card">
                        <div class="stat-icon total-tasks">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-number"><?php echo $stats['total'] ?? 0; ?></h3>
                            <p class="stat-label">Total Tasks</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon completed-tasks">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-number"><?php echo $stats['completed'] ?? 0; ?></h3>
                            <p class="stat-label">Completed</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon pending-tasks">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-number"><?php echo $stats['pending'] ?? 0; ?></h3>
                            <p class="stat-label">Pending</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon high-priority">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 class="stat-number"><?php echo $stats['high_priority'] ?? 0; ?></h3>
                            <p class="stat-label">High Priority</p>
                        </div>
                    </div>
                </section>

                <!-- Action Bar -->
                <section class="action-bar">
                    <div class="action-group">
                        <button class="btn btn-primary" id="addTaskBtn">
                            <i class="fas fa-plus"></i>
                            Add New Task
                        </button>
                    </div>

                    <div class="filter-group">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" placeholder="Search tasks..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                        </div>

                        <select class="filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo ($filters['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>

                        <select class="filter-select" id="priorityFilter">
                            <option value="">All Priority</option>
                            <option value="high" <?php echo ($filters['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                            <option value="medium" <?php echo ($filters['priority'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="low" <?php echo ($filters['priority'] ?? '') === 'low' ? 'selected' : '';?>>Low</option>
                        </select>

                        <select class="filter-select" id="sortFilter">
                            <option value="created_at" <?php echo ($filters['sort_by'] ?? '') === 'created_at' ? 'selected' : '' ?>>Newest First</option>
                            <option value="due_date" <?php echo ($filters['sort_by'] ?? '') === 'due_date' ? 'selected' : '' ?>>Due Date</option>
                            <option value="priority" <?php echo ($filters['sort_by'] ?? '') === 'priority' ? 'selected' : '' ?>>Priority</option>
                        </select>
                    </div>
                </section>

                <!-- Tasks Grid -->
                <section class="task-section">
                    <?php if (empty($tasks)): ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list empty-icon"></i>
                            <h3>No tasks found</h3>
                            <p>Get started by creating your firs task!</p>
                            <button class="btn btn-primary" id="emptyAddTaskBtn">
                                <i class="fas fa-plus"></i>
                                Add Your First Task
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="tasks-grid" id="tasksGrid">
                            <?php foreach ($tasks as $task): ?>
                                <div class="task-card" data-task-id="<?php echo $task['id']; ?>" data-priority="<?php echo $task['priority']; ?>">
                                    <div class="task-header">
                                        <div class="task-meta">
                                            <span class="task-priority priority-<?php echo $task['priority']; ?>">
                                                <?php echo ucfirst($task['priority']); ?>
                                            </span>
                                            <span class="task-date">
                                                <?php echo date('M j, Y', strtotime($task['created_at'])); ?>
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
                                        <h4 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <?php if (!empty($task['description'])): ?>
                                            <p class="task-description"><?php echo htmlspecialchars($task['description']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($task['due_date']): ?>
                                            <div class="task-due-date">
                                                <i class="fas fa-calendar"></i>
                                                Due: <?php echo date('M j, Y', strtotime($task['due_date'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="task-footer">
                                        <div class="task-status">
                                            <label class="checkbox-container">
                                                <input type="checkbox" class="status-toggle" <?php echo $task['status'] == 'completed' ? 'checked' : ''; ?> data-task-id="<?php echo $task['id']; ?>">
                                                <span class="checkmark"></span>
                                                <span class="status-label">
                                                    <?php echo $task['status'] == 'completed'? 'Completed' : 'Mark Complete'; ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Add Task Modal -->
            <div id="taskModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Add New Task</h3>
                        <button class="btn-icon close-modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="" id="taskForm" class="modal-form">
                        <input type="hidden" id="taskId" name="task_id">
                        <div class="form-group">
                            <label for="taskTitlte">Task Title *</label>
                            <input type="text" id="taskTitle" name="title" required placeholder="Enter task title..">
                        </div>

                        <div class="form-group">
                            <label for="taskDescription">Description</label>
                            <textarea name="description" id="taskDescription" rows="3" placeholder="Enter task description..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="taskPriority">Priority</label>
                                <select name="priority" id="taskPriority" class="form-select">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="taskDueDate">Due Date</label>
                                <input type="date" id="taskDueDate" name="due_date" class="form-input">
                            </div>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <span id="submitText">Create Task</span>
                                <div class="loading-spinner" id="submitSpinner" style="display: none;"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div> 
        </>

<?php include 'includes/footer.php'; ?>