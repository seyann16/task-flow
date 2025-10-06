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
            if (isset($filter['search']) && !empty($filters['search'])) {
                $whereClause .= "AND (title LIKE ? OR description LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Sorting
        }
    }
}
?>