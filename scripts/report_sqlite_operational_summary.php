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

$summary = [];

foreach (['users', 'assets', 'disturbances', 'pruning_tasks', 'field_reports', 'activity_logs'] as $table) {
    $summary['counts'][$table] = (int) $sqlite->query(sprintf('SELECT COUNT(*) FROM "%s"', $table))->fetchColumn();
}

$monthQueries = [
    'disturbances' => 'SELECT substr(reported_at, 6, 2) AS month, COUNT(*) AS total FROM disturbances GROUP BY month ORDER BY month',
    'pruning_tasks' => 'SELECT substr(due_date, 6, 2) AS month, COUNT(*) AS total FROM pruning_tasks GROUP BY month ORDER BY month',
    'field_reports' => 'SELECT substr(submitted_at, 6, 2) AS month, COUNT(*) AS total FROM field_reports GROUP BY month ORDER BY month',
];

foreach ($monthQueries as $table => $sql) {
    $summary['months'][$table] = $sqlite->query($sql)->fetchAll();
}

echo json_encode($summary, JSON_PRETTY_PRINT).PHP_EOL;
