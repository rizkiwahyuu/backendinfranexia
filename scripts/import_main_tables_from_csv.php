<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$basePath = 'C:\\Users\\rizki\\Downloads\\';
$files = [
    'assets' => $basePath.'assets(1).csv',
    'disturbances' => $basePath.'disturbances (1).csv',
    'pruning_tasks' => $basePath.'pruning_tasks (1).csv',
];

foreach ($files as $table => $path) {
    if (! is_file($path)) {
        fwrite(STDERR, "File CSV untuk tabel {$table} tidak ditemukan: {$path}".PHP_EOL);
        exit(1);
    }
}

$tableColumns = [
    'assets' => [
        'id', 'asset_code', 'asset_name', 'asset_type', 'region_id', 'latitude', 'longitude',
        'address', 'status', 'installation_date', 'notes', 'created_by', 'created_at', 'updated_at',
    ],
    'disturbances' => [
        'id', 'disturbance_code', 'asset_id', 'region_id', 'type', 'severity', 'status', 'latitude',
        'longitude', 'description', 'reported_at', 'resolved_at', 'created_by', 'assigned_to',
        'created_at', 'updated_at',
    ],
    'pruning_tasks' => [
        'id', 'task_code', 'asset_id', 'region_id', 'assigned_to', 'title', 'description', 'priority',
        'status', 'latitude', 'longitude', 'due_date', 'created_by', 'created_at', 'updated_at',
    ],
];

$nullableColumns = [
    'assets' => ['installation_date', 'notes', 'created_by', 'created_at', 'updated_at'],
    'disturbances' => ['asset_id', 'latitude', 'longitude', 'resolved_at', 'created_by', 'assigned_to', 'created_at', 'updated_at'],
    'pruning_tasks' => ['asset_id', 'assigned_to', 'latitude', 'longitude', 'due_date', 'created_by', 'created_at', 'updated_at'],
];

$integerColumns = [
    'assets' => ['id', 'region_id', 'created_by'],
    'disturbances' => ['id', 'asset_id', 'region_id', 'severity', 'created_by', 'assigned_to'],
    'pruning_tasks' => ['id', 'asset_id', 'region_id', 'assigned_to', 'created_by'],
];

$floatColumns = [
    'assets' => ['latitude', 'longitude'],
    'disturbances' => ['latitude', 'longitude'],
    'pruning_tasks' => ['latitude', 'longitude'],
];

function readCsvRows(string $path, array $expectedColumns, array $nullable, array $integers, array $floats): array
{
    $handle = fopen($path, 'rb');

    if ($handle === false) {
        throw new RuntimeException("Gagal membuka file {$path}");
    }

    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        throw new RuntimeException("Header CSV kosong untuk {$path}");
    }

    if ($header !== $expectedColumns) {
        fclose($handle);
        throw new RuntimeException("Header CSV tidak cocok untuk {$path}");
    }

    $rows = [];
    while (($data = fgetcsv($handle)) !== false) {
        if ($data === [null] || $data === false) {
            continue;
        }

        $row = array_combine($expectedColumns, $data);
        if ($row === false) {
            fclose($handle);
            throw new RuntimeException("Gagal membaca baris CSV pada {$path}");
        }

        foreach ($row as $column => $value) {
            if (in_array($column, $nullable, true) && $value === '') {
                $row[$column] = null;
                continue;
            }

            if (in_array($column, $integers, true) && $value !== '') {
                $row[$column] = (int) $value;
                continue;
            }

            if (in_array($column, $floats, true) && $value !== '') {
                $row[$column] = (float) $value;
            }
        }

        $rows[] = $row;
    }

    fclose($handle);

    return $rows;
}

$payload = [];
foreach ($files as $table => $path) {
    $payload[$table] = readCsvRows(
        $path,
        $tableColumns[$table],
        $nullableColumns[$table],
        $integerColumns[$table],
        $floatColumns[$table],
    );
}

DB::connection()->disableQueryLog();

DB::transaction(function () use ($payload): void {
    DB::statement('PRAGMA foreign_keys = OFF');

    DB::table('field_reports')->delete();
    DB::table('activity_logs')->delete();
    DB::table('disturbances')->delete();
    DB::table('pruning_tasks')->delete();
    DB::table('assets')->delete();

    foreach (['field_reports', 'activity_logs', 'disturbances', 'pruning_tasks', 'assets'] as $table) {
        DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$table}'");
    }

    foreach ($payload['assets'] as $row) {
        DB::table('assets')->insert($row);
    }

    foreach ($payload['disturbances'] as $row) {
        DB::table('disturbances')->insert($row);
    }

    foreach ($payload['pruning_tasks'] as $row) {
        DB::table('pruning_tasks')->insert($row);
    }

    DB::statement('PRAGMA foreign_keys = ON');
});

echo 'Import selesai.'.PHP_EOL;
echo 'assets: '.count($payload['assets']).PHP_EOL;
echo 'disturbances: '.count($payload['disturbances']).PHP_EOL;
echo 'pruning_tasks: '.count($payload['pruning_tasks']).PHP_EOL;
echo 'field_reports: 0'.PHP_EOL;
echo 'activity_logs: 0'.PHP_EOL;
