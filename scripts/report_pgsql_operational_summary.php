<?php

declare(strict_types=1);

$pgConfig = [
    'host' => getenv('TARGET_DB_HOST') ?: '127.0.0.1',
    'port' => getenv('TARGET_DB_PORT') ?: '5432',
    'database' => getenv('TARGET_DB_DATABASE') ?: 'laravel',
    'username' => getenv('TARGET_DB_USERNAME') ?: 'postgres',
    'password' => getenv('TARGET_DB_PASSWORD') ?: '123',
    'sslmode' => getenv('TARGET_DB_SSLMODE') ?: 'prefer',
];

$pgsql = new PDO(
    sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
        $pgConfig['host'],
        $pgConfig['port'],
        $pgConfig['database'],
        $pgConfig['sslmode']
    ),
    $pgConfig['username'],
    $pgConfig['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$summary = [];

foreach (['users', 'assets', 'disturbances', 'pruning_tasks', 'field_reports', 'activity_logs'] as $table) {
    $summary['counts'][$table] = (int) $pgsql->query(sprintf('SELECT COUNT(*) FROM "%s"', $table))->fetchColumn();
}

$monthQueries = [
    'disturbances' => "SELECT to_char(reported_at, 'MM') AS month, COUNT(*) AS total FROM disturbances GROUP BY month ORDER BY month",
    'pruning_tasks' => "SELECT to_char(due_date, 'MM') AS month, COUNT(*) AS total FROM pruning_tasks GROUP BY month ORDER BY month",
    'field_reports' => "SELECT to_char(submitted_at, 'MM') AS month, COUNT(*) AS total FROM field_reports GROUP BY month ORDER BY month",
];

foreach ($monthQueries as $table => $sql) {
    $summary['months'][$table] = $pgsql->query($sql)->fetchAll();
}

echo json_encode($summary, JSON_PRETTY_PRINT).PHP_EOL;
