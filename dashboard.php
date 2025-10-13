<?php
include 'includes/config.php';
include 'includes/TaskManager.php';

$taskManager = new TaskManager($pdo);
$stats = $taskManager->getStatistics();

// Get tasks for charts
$allTasks = $taskManager->getTasks();
$tasks = $allTasks['success'] ? $allTasks['tasks'] : [];

// Calculate analytics data
$priorityCounts = ['high' => 0, 'medium' => 0, 'low' => 0];
$statusCounts = ['pending' => 0, 'completed' => 0];
$weeklyData = [];

foreach ($tasks as $task) {
    $priorityCounts[$task['priority']]++;
    $statusCounts[$task['status']]++;

    // Group by week
    $week = date('Y-W', strtotime($task['created_at']));
    if (!isset($weeklyData[$week])) {
        $weeklyData[$week] = 0;
    }
    $weeklyData[$week]++;
}

$pageTitle = "Analytics Dashboard";
include 'includes/header.php';
?>

<div class="dashboard-container">
    <div class="analytics-header">
        <h1>Task Analytics</h1>
        <p>Insights into your task management performance</p>
    </div>

    <div class="analytics-grid">
        <!-- Priority Distribution -->
        <div class="analytics-card">
            <h3>Task Priority Distribution</h3>
            <div class="chart-container">
                <canvas id="priorityChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Status Overview -->
        <div class="analytics-card">
            <h3>Completion Status</h3>
            <div class="chart-container">
                <canvas id="statusChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Weekly Activity -->
        <div class="analytics-card full-width">
            <h3>Weekly Task Creation</h3>
            <div class="chart-container">
                <canvas id="weeklyChart" width="800" height="300"></canvas>
            </div>
        </div>

        <!-- Task Insights -->
        <div class="analytics-card">
            <h3>Performance Insights</h3>
            <div class="insights-list">
                <div class="insight-item">
                    <i class="fas fa-bolt insight-icon high-priority"></i>
                    <div class="insight-content">
                        <span class="insight-value"><?php echo $priorityCounts['high']; ?></span>
                        <span class="insight-label">High Priority Tasks</span>
                    </div>
                </div>
                <div class="insight-item">
                    <i class="fas fa-check-circle insight-icon completed"></i>
                    <div class="insight-content">
                        <span class="insight-value"><?php echo $statusCounts['completed']; ?></span>
                        <span class="insight-label">Tasks Completed</span>
                    </div>
                </div>
                <div class="insight-item">
                    <i class="fas fa-clock insight-icon pending"></i>
                    <div class="insight-content">
                        <span class="insight-value"><?php echo $statusCounts['pending']; ?></span>
                        <span class="insight-label">Tasks Pending</span>
                    </div>
                </div>
                <div class="insight-item">
                    <i class="fas fa-trophy insight-icon achievement"></i>
                    <div class="insight-content">
                        <span class="insight-value">
                            <?php
                            $total = $stats['total'] ?? 0;
                            $completed = $stats['completed'] ?? 0;
                            echo $total > 0 ? round(($completed / $total) * 100) : 0;
                            ?>%
                        </span>
                        <span class="insight-label">Completion Rate</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Priority Distribution chart
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'doughnut',
            data: {
                labels: ['High Priority', 'Medium Priority', 'Low Priority'],
                datasets: [{
                    data: [
                        <?php echo $priorityCounts['high']; ?>,
                        <?php echo $priorityCounts['medium']; ?>,
                        <?php echo $priorityCounts['low']; ?>,
                    ],
                    backgroundColor: [
                        '#ef4444',
                        '#f59e0b',
                        '#10b981'
                    ],
                    borderWidth: 2,
                    borderColor: 'var(--bg-primary)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Status Overview Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Pending'],
                datasets: [{
                    data: [
                        <?php echo $statusCounts['completed']; ?>,
                        <?php echo $statusCounts['pending']; ?>
                    ],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b'
                    ],
                    borderWidth: 2,
                    borderColor: 'var(--bg-primary)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Weekly Activity Chart
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyData = <?php echo json_encode(array_values($weeklyData)); ?>;
        const weeklyLabels = <?php echo json_encode(array_keys($weeklyData)); ?>;

        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Tasks Created',
                    data: weeklyData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>