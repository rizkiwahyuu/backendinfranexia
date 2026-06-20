<?php

declare(strict_types=1);

$sqlitePath = dirname(__DIR__).DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.sqlite';

if (! file_exists($sqlitePath)) {
    fwrite(STDERR, "SQLite file not found: {$sqlitePath}\n");
    exit(1);
}

$sqlite = new PDO('sqlite:'.$sqlitePath);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$result = [
    'disturbance_months' => $sqlite->query("SELECT substr(reported_at, 6, 2) AS month, COUNT(*) AS total FROM disturbances GROUP BY month ORDER BY month")->fetchAll(),
    'active_disturbance_months' => $sqlite->query("SELECT substr(reported_at, 6, 2) AS month, COUNT(*) AS total FROM disturbances WHERE status NOT IN ('resolved', 'closed') GROUP BY month ORDER BY month")->fetchAll(),
    'resolved_months' => $sqlite->query("SELECT substr(resolved_at, 6, 2) AS month, COUNT(*) AS total FROM disturbances WHERE resolved_at IS NOT NULL GROUP BY month ORDER BY month")->fetchAll(),
    'task_months' => $sqlite->query("SELECT substr(due_date, 6, 2) AS month, COUNT(*) AS total FROM pruning_tasks GROUP BY month ORDER BY month")->fetchAll(),
    'report_months' => $sqlite->query("SELECT substr(submitted_at, 6, 2) AS month, COUNT(*) AS total FROM field_reports GROUP BY month ORDER BY month")->fetchAll(),
    'approved_report_months' => $sqlite->query("SELECT substr(submitted_at, 6, 2) AS month, COUNT(*) AS total FROM field_reports WHERE status = 'approved' GROUP BY month ORDER BY month")->fetchAll(),
    'regions' => $sqlite->query("SELECT region_id, COUNT(*) AS total FROM disturbances GROUP BY region_id ORDER BY region_id")->fetchAll(),
    'types' => $sqlite->query("SELECT type, COUNT(*) AS total FROM disturbances GROUP BY type ORDER BY total DESC")->fetchAll(),
    'statuses' => $sqlite->query("SELECT status, COUNT(*) AS total FROM disturbances GROUP BY status ORDER BY total DESC")->fetchAll(),
    'latest_disturbance' => $sqlite->query("SELECT disturbance_code, reported_at, resolved_at, status, region_id, type, severity FROM disturbances ORDER BY id DESC LIMIT 1")->fetch(),
    'latest_task' => $sqlite->query("SELECT task_code, due_date, status, region_id, priority FROM pruning_tasks ORDER BY id DESC LIMIT 1")->fetch(),
    'latest_report' => $sqlite->query("SELECT report_code, submitted_at, approved_at, status, report_type FROM field_reports ORDER BY id DESC LIMIT 1")->fetch(),
];

echo json_encode($result, JSON_PRETTY_PRINT).PHP_EOL;
