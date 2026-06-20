<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$basePath = dirname(__DIR__);
$sqlitePath = $basePath.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.sqlite';
$pgConfig = [
    'host' => '127.0.0.1',
    'port' => '5432',
    'database' => 'laravel',
    'username' => 'postgres',
    'password' => '123',
];

if (! file_exists($sqlitePath)) {
    fwrite(STDERR, "SQLite file not found: {$sqlitePath}\n");
    exit(1);
}

$sqlite = new PDO('sqlite:'.$sqlitePath);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pgsql = new PDO(
    sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $pgConfig['host'],
        $pgConfig['port'],
        $pgConfig['database']
    ),
    $pgConfig['username'],
    $pgConfig['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$tables = $sqlite->query("select name from sqlite_master where type = 'table' and name not like 'sqlite_%' order by name")->fetchAll(PDO::FETCH_COLUMN);
$tableRows = [];

foreach ($tables as $table) {
    $tableRows[$table] = $sqlite->query("select * from \"{$table}\"")->fetchAll();
}

// Copy parent tables first so FK references remain valid.
$orderedTables = [
    'migrations',
    'users',
    'assets',
    'disturbances',
    'pruning_tasks',
    'field_reports',
    'activity_logs',
    'cache',
    'cache_locks',
    'failed_jobs',
    'job_batches',
    'jobs',
    'password_reset_tokens',
    'sessions',
];

$existingTables = array_values(array_filter($orderedTables, fn (string $table) => array_key_exists($table, $tableRows)));

$pgsql->exec('BEGIN');

try {
    $pgsql->exec('SET session_replication_role = replica');

    foreach ($existingTables as $table) {
        $pgsql->exec('TRUNCATE TABLE "'.$table.'" RESTART IDENTITY CASCADE');
    }

foreach ($existingTables as $table) {
    $rows = $tableRows[$table];
    if ($rows === []) {
        echo "Skipped empty table: {$table}\n";
        continue;
        }

        $columns = array_keys($rows[0]);
        $placeholders = array_map(fn (string $column) => ':'.$column, $columns);
        $quotedColumns = array_map(fn (string $column) => '"'.$column.'"', $columns);
        $sql = sprintf(
            'INSERT INTO "%s" (%s) VALUES (%s)',
            $table,
            implode(', ', $quotedColumns),
            implode(', ', $placeholders)
        );
        $stmt = $pgsql->prepare($sql);
        $inserted = 0;

        foreach ($rows as $row) {
            foreach ($row as $column => $value) {
                if (is_bool($value)) {
                    $row[$column] = $value ? 1 : 0;
                } elseif (is_array($value)) {
                    $row[$column] = json_encode($value);
                }
            }

            $stmt->execute($row);
            $inserted++;
        }

        echo 'Copied '.$inserted.' rows into '.$table.PHP_EOL;
    }

    $pgsql->exec('SET session_replication_role = DEFAULT');
    $pgsql->exec('COMMIT');
} catch (Throwable $e) {
    $pgsql->exec('ROLLBACK');
    $pgsql->exec('SET session_replication_role = DEFAULT');
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}

echo "SQLite -> PostgreSQL sync complete.\n";
