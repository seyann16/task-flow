<?php
class TaskManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new task
    public function createTask($title, $description = '', $priority = 'medium', $due_date = null) {
        try {
            $query = "INSERT INTO tasks (title, description, priority, due_date) VALUE (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$title, $description, $priority, $due_date]);

            return [
                'success' => true,
                'task_id' => $this->pdo->lastInsertId(),
                'message' => 'Task created successfully'
            ];
        } catch (PDOException $e) {
            error_log("Create task error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create task'
            ];
        }
    }

    // Get all tasks with filtering and sorting
    public function getTasks($filters = []) {
        try {
            $whereClause = "WHERE is_deleted = 0";
            $params = [];

            // Status filter
            if (isset($filters['status']) && in_array($filters['status'], ['pending', 'completed'])) {
                $whereClause .= " AND status = ?";
                $params[] = $filters['status'];
            }

            // Priority filter
            if (isset($filters['priority']) && in_array($filters['priority'], ['low', 'medium', 'high'])) {
                $whereClause .= " AND priority = ?";
                $params[] = $filters['priority'];
            }

            // Search term
            if (isset($filters['search']) && !empty($filters['search'])) {
                $whereClause .= "AND (title LIKE ? OR description LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Sorting
            $orderBy = "ORDER BY ";
            if (isset($filters['sort_by'])) {
                switch ($filters['sort_by']) {
                    case 'due_date':
                        $orderBy .= "due_date ASC, created_at DESC";
                        break;
                    case 'priority':
                        $orderBy .= "FIELD(priority, 'high', 'medium', 'low'), created_at DESC";
                        break;
                    default:
                        $orderBy .= "created_at DESC";
                }       
            } else {
                $orderBy .= "created_at DESC";
            }

            $query = "SELECT * FROM tasks $whereClause $orderBy";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            return [
                'success' => true,
                'tasks' => $stmt->fetchAll(),
                'total' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            error_log("Get tasks error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch tasks'
            ];
        }
    }

    // Update task status
    public function updateTaskStatus($task_id, $status) {
        try {
            $query = "UPDATE tasks SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND is_deleted = 0";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$status, $task_id]);

            return [
                'success' => true,
                'message' => 'Task status updated successfully'
            ];
        } catch (PDOException $e) {
            error_log("Update task status error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update task status'
            ];
        }
    }

    // Delete task (soft delete)
    public function deleteTask($task_id) {
        try {
            $query = "UPDATE tasks SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$task_id]);

            return [
                'success' => true,
                'message' => 'Task deleted successfully'
            ];
        } catch (PDOException $e) {
            error_log("Delete task error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete task'
            ];
        }
    }

    // Get task statistics
    public function getStatistics() {
        try {
            $query = "SELECT COUNT(*) as total,
                      SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                      SUM(CASE WHEN priority = 'high' AND status = 'pending' THEN 1 ELSE 0 END) as `high_priority`
                      FROM tasks WHERE is_deleted = 0";
            $stmt = $this->pdo->query($query);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get statistics error: " . $e->getMessage());
            return null;
        }
    }
}
?>