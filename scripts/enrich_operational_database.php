<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$regionMeta = [
    0 => ['code' => 'SU', 'city' => 'Surabaya'],
    1 => ['code' => 'SU', 'city' => 'Surabaya'],
    2 => ['code' => 'SU', 'city' => 'Surabaya'],
    3 => ['code' => 'SD', 'city' => 'Sidoarjo'],
    4 => ['code' => 'SD', 'city' => 'Sidoarjo'],
    5 => ['code' => 'GK', 'city' => 'Gresik'],
    6 => ['code' => 'GK', 'city' => 'Gresik'],
];

$streetPool = [
    'Surabaya' => ['Ahmad Yani', 'Basuki Rahmat', 'Darmo', 'MERR', 'Kertajaya', 'Mayjen Sungkono', 'Ngagel', 'Rungkut Asri'],
    'Sidoarjo' => ['Pahlawan', 'Gajah Mada', 'Raya Buduran', 'Raya Taman', 'Raya Gedangan', 'Kahuripan', 'Waru Indah', 'Ponti'],
    'Gresik' => ['RA Kartini', 'Panglima Sudirman', 'Raya Manyar', 'Dr. Wahidin', 'Veteran', 'Raya Cerme', 'Gubernur Suryo', 'Duduksampeyan'],
];

$assetPrefixMap = [
    'ODP' => 'ODP',
    'ODC' => 'ODC',
    'Tiang' => 'TNG',
    'FO Cable' => 'FOC',
    'Joint Closure' => 'JCL',
];

$assetTypeCycle = ['FO Cable', 'Joint Closure', 'ODP', 'ODC', 'Tiang'];
$assetStatusCycle = ['active', 'monitoring', 'maintenance', 'inactive', 'active'];

$disturbanceTypeLabels = [
    'backbone_down' => 'Indikasi backbone utama mengalami penurunan layanan dan perlu isolasi segmen jaringan.',
    'cable_cut' => 'Kabel FO putus atau indikasi putus pada jalur distribusi.',
    'device_issue' => 'Perangkat aktif mengalami gangguan operasional dan memerlukan pengecekan lapangan.',
    'environment' => 'Gangguan lingkungan mempengaruhi jalur fiber dan akses teknisi.',
    'high_loss' => 'Redaman tinggi terdeteksi pada jalur fiber dan perlu pengukuran ulang.',
    'link_flap' => 'Koneksi naik turun pada segmen jaringan dan perlu stabilisasi.',
    'odp_damage' => 'ODP mengalami kerusakan fisik dan perlu penanganan teknis.',
    'power_issue' => 'Gangguan catu daya pada perangkat jaringan membutuhkan pemeriksaan.',
];

$disturbanceTypes = array_keys($disturbanceTypeLabels);
$disturbanceStatusCycle = ['open', 'on_progress', 'waiting_validation', 'resolved'];
$taskPriorityCycle = ['low', 'medium', 'high', 'critical'];
$taskStatusCycle = ['draft', 'assigned', 'on_progress', 'waiting_validation', 'completed'];
$reportStatusCycle = ['submitted', 'approved', 'rejected'];

$assetsToAdd = 100;
$disturbancesToAdd = 64;
$tasksToAdd = 64;
$disturbanceReportsToAdd = 42;
$pruningReportsToAdd = 42;
$inspectionReportsToAdd = 16;
$logsToAdd = 160;

$activeUsers = DB::table('users')->where('is_active', 1)->orderBy('id')->get(['id', 'name', 'role', 'region_id']);
$operatorIds = $activeUsers->where('role', 'operator')->pluck('id')->values()->all();
$adminIds = $activeUsers->where('role', 'admin')->pluck('id')->values()->all();
$allUserIds = $activeUsers->pluck('id')->values()->all();

if ($operatorIds === [] || $adminIds === [] || $allUserIds === []) {
    fwrite(STDERR, 'User aktif tidak cukup untuk membuat data operasional.'.PHP_EOL);
    exit(1);
}

$existingAssets = DB::table('assets')->orderBy('id')->get();

if ($existingAssets->isEmpty()) {
    fwrite(STDERR, 'Tabel assets kosong, tidak ada basis data untuk ekspansi.'.PHP_EOL);
    exit(1);
}

$maxAssetNumber = $existingAssets
    ->map(function ($asset): int {
        if (preg_match('/(\d+)$/', (string) $asset->asset_code, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    })
    ->max();

$maxDisturbanceNumber = DB::table('disturbances')
    ->pluck('disturbance_code')
    ->map(function (string $code): int {
        if (preg_match('/(\d+)$/', $code, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    })
    ->max() ?? 0;

$maxTaskNumber = DB::table('pruning_tasks')
    ->pluck('task_code')
    ->map(function (string $code): int {
        if (preg_match('/(\d+)$/', $code, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    })
    ->max() ?? 0;

$maxReportNumber = DB::table('field_reports')
    ->pluck('report_code')
    ->map(function (string $code): int {
        if (preg_match('/(\d+)$/', $code, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    })
    ->max() ?? 0;

function offsetCoordinate(float $value, int $index, float $stepA, float $stepB): float
{
    $first = (($index % 5) - 2) * $stepA;
    $second = ((intdiv($index, 5) % 4) - 1.5) * $stepB;

    return round($value + $first + $second, 6);
}

DB::transaction(function () use (
    $existingAssets,
    $assetsToAdd,
    $assetTypeCycle,
    $assetStatusCycle,
    $assetPrefixMap,
    $regionMeta,
    $streetPool,
    $adminIds,
    $operatorIds,
    $allUserIds,
    $disturbancesToAdd,
    $disturbanceTypes,
    $disturbanceTypeLabels,
    $disturbanceStatusCycle,
    $tasksToAdd,
    $taskPriorityCycle,
    $taskStatusCycle,
    $disturbanceReportsToAdd,
    $pruningReportsToAdd,
    $inspectionReportsToAdd,
    $reportStatusCycle,
    $logsToAdd,
    $maxAssetNumber,
    $maxDisturbanceNumber,
    $maxTaskNumber,
    $maxReportNumber
): void {
    $now = Carbon::now();
    $newAssets = [];
    $newDisturbances = [];
    $newTasks = [];
    $newReports = [];

    for ($i = 0; $i < $assetsToAdd; $i++) {
        $base = $existingAssets[$i % $existingAssets->count()];
        $regionId = (int) $base->region_id;
        $meta = $regionMeta[$regionId] ?? ['code' => 'SU', 'city' => 'Surabaya'];
        $assetType = $assetTypeCycle[$i % count($assetTypeCycle)];
        $assetCode = sprintf('%s-%s-%03d', $assetPrefixMap[$assetType], $meta['code'], $maxAssetNumber + $i + 1);
        $addressNumber = 200 + $i;
        $street = $streetPool[$meta['city']][($i + $regionId) % count($streetPool[$meta['city']])];
        $createdAt = $now->copy()->subDays(90 - ($i % 45))->setTime(8 + ($i % 8), ($i * 7) % 60);
        $assetData = [
            'asset_code' => $assetCode,
            'asset_name' => sprintf('%s %s %03d', $assetType, $meta['city'], $maxAssetNumber + $i + 1),
            'asset_type' => $assetType,
            'region_id' => $regionId,
            'latitude' => offsetCoordinate((float) $base->latitude, $i, 0.00027, 0.00005),
            'longitude' => offsetCoordinate((float) $base->longitude, $i + 3, 0.00031, 0.00006),
            'address' => sprintf('Jl. %s No.%d, %s', $street, $addressNumber, $meta['city']),
            'status' => $assetStatusCycle[$i % count($assetStatusCycle)],
            'installation_date' => $now->copy()->subDays(600 - ($i * 3 % 360))->toDateString(),
            'notes' => 'Data ekspansi operasional untuk sinkronisasi dashboard, map, dan laporan.',
            'created_by' => $adminIds[$i % count($adminIds)],
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        $assetId = DB::table('assets')->insertGetId($assetData);
        $newAssets[] = (object) ['id' => $assetId, ...$assetData];
    }

    $assetPool = collect(array_merge($newAssets, $existingAssets->all()))->values();

    for ($i = 0; $i < $disturbancesToAdd; $i++) {
        $asset = $assetPool[$i % $assetPool->count()];
        $type = $disturbanceTypes[$i % count($disturbanceTypes)];
        $status = $disturbanceStatusCycle[$i % count($disturbanceStatusCycle)];
        $reportedAt = $now->copy()->subDays(18 - ($i % 12))->setTime(6 + ($i % 10), ($i * 9) % 60);
        $resolvedAt = $status === 'resolved' ? $reportedAt->copy()->addHours(6 + ($i % 4)) : null;
        $data = [
            'disturbance_code' => sprintf('GGN-2026-%03d', $maxDisturbanceNumber + $i + 1),
            'asset_id' => $asset->id,
            'region_id' => $asset->region_id,
            'type' => $type,
            'severity' => ($i % 5) + 1,
            'status' => $status,
            'latitude' => $asset->latitude,
            'longitude' => $asset->longitude,
            'description' => $disturbanceTypeLabels[$type].' Lokasi mengikuti aset operasional terbaru di '.$regionMeta[$asset->region_id]['city'].'.',
            'reported_at' => $reportedAt,
            'resolved_at' => $resolvedAt,
            'created_by' => $adminIds[$i % count($adminIds)],
            'assigned_to' => $operatorIds[$i % count($operatorIds)],
            'created_at' => $reportedAt,
            'updated_at' => $resolvedAt ?? $reportedAt,
        ];

        $disturbanceId = DB::table('disturbances')->insertGetId($data);
        $newDisturbances[] = (object) ['id' => $disturbanceId, ...$data];
    }

    for ($i = 0; $i < $tasksToAdd; $i++) {
        $asset = $assetPool[($i * 2) % $assetPool->count()];
        $status = $taskStatusCycle[$i % count($taskStatusCycle)];
        $createdAt = $now->copy()->subDays(20 - ($i % 10))->setTime(7 + ($i % 8), ($i * 5) % 60);
        $assignedTo = $status === 'draft' ? null : $operatorIds[($i + 1) % count($operatorIds)];
        $dueDate = $now->copy()->addDays(($i % 16) - 5)->toDateString();
        $data = [
            'task_code' => sprintf('PNG-2026-%03d', $maxTaskNumber + $i + 1),
            'asset_id' => $asset->id,
            'region_id' => $asset->region_id,
            'assigned_to' => $assignedTo,
            'title' => sprintf('Pemangkasan koridor %s %03d', $regionMeta[$asset->region_id]['city'], $maxTaskNumber + $i + 1),
            'description' => 'Tugas pruning dan inspeksi jalur fiber untuk menjaga clearance jaringan dan akses teknisi.',
            'priority' => $taskPriorityCycle[$i % count($taskPriorityCycle)],
            'status' => $status,
            'latitude' => $asset->latitude,
            'longitude' => $asset->longitude,
            'due_date' => $dueDate,
            'created_by' => $adminIds[$i % count($adminIds)],
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        $taskId = DB::table('pruning_tasks')->insertGetId($data);
        $newTasks[] = (object) ['id' => $taskId, ...$data];
    }

    for ($i = 0; $i < $disturbanceReportsToAdd; $i++) {
        $disturbance = $newDisturbances[$i % count($newDisturbances)];
        $status = $reportStatusCycle[$i % count($reportStatusCycle)];
        $submittedAt = Carbon::parse((string) $disturbance->reported_at)->addHours(2 + ($i % 5));
        $approvedAt = $status === 'approved' ? $submittedAt->copy()->addHours(3) : null;
        $adminNote = $status === 'rejected' ? 'Dokumentasi lapangan perlu dilengkapi ulang.' : null;

        if ($status === 'submitted') {
            DB::table('disturbances')->where('id', $disturbance->id)->update([
                'status' => 'waiting_validation',
                'updated_at' => $submittedAt,
            ]);
            $disturbance->status = 'waiting_validation';
        } elseif ($status === 'approved') {
            DB::table('disturbances')->where('id', $disturbance->id)->update([
                'status' => 'resolved',
                'resolved_at' => $approvedAt,
                'updated_at' => $approvedAt,
            ]);
            $disturbance->status = 'resolved';
            $disturbance->resolved_at = $approvedAt;
        }

        $data = [
            'report_code' => sprintf('LPR-2026-%03d', $maxReportNumber + $i + 1),
            'task_id' => null,
            'disturbance_id' => $disturbance->id,
            'asset_id' => $disturbance->asset_id,
            'operator_id' => $disturbance->assigned_to,
            'report_type' => 'disturbance',
            'condition_before' => 'Gangguan terdeteksi pada jalur dan dilakukan verifikasi kondisi awal.',
            'action_taken' => 'Pemeriksaan lapangan, pengukuran, dan tindakan perbaikan awal dilakukan oleh operator.',
            'condition_after' => $status === 'approved' ? 'Kondisi jaringan kembali stabil dan siap dipantau.' : 'Titik masih memerlukan validasi lanjutan.',
            'latitude' => $disturbance->latitude,
            'longitude' => $disturbance->longitude,
            'attachments' => json_encode([]),
            'status' => $status,
            'admin_note' => $adminNote,
            'submitted_at' => $submittedAt,
            'approved_at' => $approvedAt,
            'created_at' => $submittedAt,
            'updated_at' => $approvedAt ?? $submittedAt,
        ];

        $reportId = DB::table('field_reports')->insertGetId($data);
        $newReports[] = (object) ['id' => $reportId, ...$data];
    }

    for ($i = 0; $i < $pruningReportsToAdd; $i++) {
        $task = $newTasks[$i % count($newTasks)];
        $status = $reportStatusCycle[($i + 1) % count($reportStatusCycle)];
        $submittedAt = Carbon::parse((string) $task->created_at)->addDays(1 + ($i % 3))->addHours(4);
        $approvedAt = $status === 'approved' ? $submittedAt->copy()->addHours(5) : null;
        $adminNote = $status === 'rejected' ? 'Hasil pruning belum sesuai checklist validasi.' : null;
        $taskStatus = match ($status) {
            'submitted' => 'waiting_validation',
            'approved' => 'completed',
            default => 'rejected',
        };

        DB::table('pruning_tasks')->where('id', $task->id)->update([
            'status' => $taskStatus,
            'updated_at' => $approvedAt ?? $submittedAt,
        ]);
        $task->status = $taskStatus;

        $data = [
            'report_code' => sprintf('LPR-2026-%03d', $maxReportNumber + $disturbanceReportsToAdd + $i + 1),
            'task_id' => $task->id,
            'disturbance_id' => null,
            'asset_id' => $task->asset_id,
            'operator_id' => $task->assigned_to ?? $operatorIds[$i % count($operatorIds)],
            'report_type' => 'pruning',
            'condition_before' => 'Vegetasi menutup sebagian jalur akses dan clearance jaringan.',
            'action_taken' => 'Pemangkasan, pembersihan area, dan inspeksi visual jalur fiber dilakukan di lapangan.',
            'condition_after' => $status === 'approved' ? 'Jalur bersih dan aman untuk operasional.' : 'Perlu tindak lanjut atau dokumentasi ulang.',
            'latitude' => $task->latitude,
            'longitude' => $task->longitude,
            'attachments' => json_encode([]),
            'status' => $status,
            'admin_note' => $adminNote,
            'submitted_at' => $submittedAt,
            'approved_at' => $approvedAt,
            'created_at' => $submittedAt,
            'updated_at' => $approvedAt ?? $submittedAt,
        ];

        $reportId = DB::table('field_reports')->insertGetId($data);
        $newReports[] = (object) ['id' => $reportId, ...$data];
    }

    for ($i = 0; $i < $inspectionReportsToAdd; $i++) {
        $asset = $assetPool[($i * 4) % $assetPool->count()];
        $operatorId = $operatorIds[$i % count($operatorIds)];
        $submittedAt = $now->copy()->subDays(10 - ($i % 7))->setTime(9 + ($i % 7), ($i * 4) % 60);
        $approvedAt = $i % 2 === 0 ? $submittedAt->copy()->addHours(6) : null;
        $status = $approvedAt ? 'approved' : 'submitted';
        $data = [
            'report_code' => sprintf('LPR-2026-%03d', $maxReportNumber + $disturbanceReportsToAdd + $pruningReportsToAdd + $i + 1),
            'task_id' => null,
            'disturbance_id' => null,
            'asset_id' => $asset->id,
            'operator_id' => $operatorId,
            'report_type' => 'inspection',
            'condition_before' => 'Inspeksi rutin dilakukan untuk memeriksa kesiapan aset jaringan.',
            'action_taken' => 'Cek fisik, identifikasi port, dan verifikasi koordinat aset telah dilakukan.',
            'condition_after' => $approvedAt ? 'Aset terverifikasi dan siap operasional.' : 'Menunggu validasi hasil inspeksi oleh admin.',
            'latitude' => $asset->latitude,
            'longitude' => $asset->longitude,
            'attachments' => json_encode([]),
            'status' => $status,
            'admin_note' => null,
            'submitted_at' => $submittedAt,
            'approved_at' => $approvedAt,
            'created_at' => $submittedAt,
            'updated_at' => $approvedAt ?? $submittedAt,
        ];

        $reportId = DB::table('field_reports')->insertGetId($data);
        $newReports[] = (object) ['id' => $reportId, ...$data];
    }

    $logActions = [
        'Melakukan sinkronisasi aset kabel',
        'Mencatat gangguan lapangan terbaru',
        'Menugaskan inspeksi dan pruning jaringan',
        'Memvalidasi laporan operasional',
        'Memeriksa dashboard dan peta monitoring',
    ];
    $logModules = ['assets', 'disturbances', 'pruning', 'reports', 'system'];

    for ($i = 0; $i < $logsToAdd; $i++) {
        $logTime = $now->copy()->subDays(12 - ($i % 9))->setTime(7 + ($i % 10), ($i * 6) % 60);
        DB::table('activity_logs')->insert([
            'user_id' => $allUserIds[$i % count($allUserIds)],
            'action' => sprintf('%s batch %03d', $logActions[$i % count($logActions)], $i + 1),
            'module' => $logModules[$i % count($logModules)],
            'created_at' => $logTime,
            'updated_at' => $logTime,
        ]);
    }
});

echo "Pengayaan database selesai.".PHP_EOL;
echo "Assets saat ini: ".DB::table('assets')->count().PHP_EOL;
echo "Disturbances saat ini: ".DB::table('disturbances')->count().PHP_EOL;
echo "Pruning tasks saat ini: ".DB::table('pruning_tasks')->count().PHP_EOL;
echo "Field reports saat ini: ".DB::table('field_reports')->count().PHP_EOL;
echo "Activity logs saat ini: ".DB::table('activity_logs')->count().PHP_EOL;
