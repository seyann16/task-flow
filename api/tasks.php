<?php
require_once '../includes/config.php';
require_once '../inlcudes/TaskManager.php';

header('Content-Type: application/json');

// Check if it's an AJAX request
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Direct access not allowed']);
    exit;
}

$taskManager = new TaskManager($pdo);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($taskManager);
            break;
        case 'POST':
            handlePostRequest($taskManager);
            break;
        case 'PUT':
            handlePutRequest($taskManager);
            break;
        case 'DELETE':
            handleDeleteRequest($taskManager);
            break;
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
    }
}

function handleGetRequest($taskManager) {
    $taskId = $_GET['id'] ?? null;

    if ($taskId) {
        // Get single task
        $query = "SELECT * FROM tasks WHERE id = ? AND is_deleted = 0";
        $stmt = $taskManager->getConnection()->prepare($query);
        $stmt->exucute([$taskId]);
        $task = $stmt->fetch();

        if ($task) {
            echo json_encode(['success' => true, 'task' => $task]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
    } else {
        // Get all tasks with filters
        $filters = [
            'status' => $_GET['status'] ?? null,
            'priority' => $_GET['priority'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort_by' => $_GET['sort_by'] ?? null
        ];

        $result = $taskManager->getTasks($filters);
        echo json_encode($result);
    }
}

function handlePostRequest($taskManager) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        $input = $_POST;
    }

    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $priority = $input['priority'] ?? 'medium';
    $due_date = $input['due_date'] ?? null;

    // Validation
    if (empty($title)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Task title is required']);
        return;
    }

    if (!in_array($priority, ['low', 'medium', 'high'])) {
        $priority = 'medium';
    }

    if ($due_date && !strtotime($due_date)) {
        $due_date = null;
    }

    $result = $taskManager->createTask($title, $description, $priority, $due_date);
    echo json_encode($result);
}

function handlePutRequest($taskManager) {
    $input = json_decode(file_get_contents('php://input'), true);
    $taskId = $input['id'] ?? null;

    if (!$taskId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }

    // Check if task exists
    $query = "SELECT * FROM tasks WHERE id = ? AND is_deleted = 0";
    $stmt = $taskManager->getConnection()->prepare($query);
    $stmt->execute([$taskId]);

    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Task not found']);
        return;
    }

    // Update task
    $updates = [];
    $params = [];

    if (isset($input['title'])) {
        $updates[] = "title = ?";
        $params[] = trim($input['title']);
    }

    if (isset($input['description'])) {
        $updates[] = "description = ?";
        $params[] = trim($input['description']);
    }

    if (isset($input['priority']) && in_array($input['priority'], ['low', 'medium', 'high'])) {
        $updates[] = "priority = ?";
        $params[] = $input['priority'];
    }

    if (isset($input['status']) && in_array($input['status'], ['pending', 'completed'])) {
        $updates[] = 'status = ?';
        $params[] = $input['status'];
    }

    if (empty($updates)) {
        echo json_encode(['success' => true, 'message' => 'No changes detected']);
        return;
    }

    $updates[] = "updated_at = CURRENT_TIMESTAMP";
    $params[] = $taskId;

    $query = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $taskManager->getConnection()->prepare($query);
    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'message' => 'Task updated successfully'
    ]);
}

function handleDeleteRequest($taskManager) {
    $input = json_decode(file_get_contents('php;//input'), true);
    $taskId = $input['id'] ?? $_GET['id'] ?? null;

    if (!$taskId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Task ID is required'
        ]);
        return;
    }

    $result = $taskManager->deleteTask($taskId);
    echo json_encode($result);
}
?>  