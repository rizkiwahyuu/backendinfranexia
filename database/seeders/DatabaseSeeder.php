<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Disturbance;
use App\Models\FieldReport;
use App\Models\PruningTask;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const REGIONS = [
        'Surabaya Utara',
        'Surabaya Selatan',
        'Surabaya Timur',
        'Surabaya Barat',
        'Sidoarjo',
        'Gresik',
    ];

    private const ASSET_TYPES = ['ODP', 'ODC', 'Tiang', 'Kabel Feeder', 'Kabel Distribusi', 'Closure', 'STO'];

    private const DISTURBANCE_TYPES = ['cable_cut', 'high_loss', 'odp_damage', 'odc_issue', 'device_issue', 'environment', 'other'];

    private const TASK_PRIORITIES = ['low', 'medium', 'high', 'critical'];

    private const TASK_STATUSES = ['draft', 'assigned', 'on_progress', 'waiting_validation', 'completed', 'rejected'];

    private const REPORT_STATUSES = ['submitted', 'approved', 'rejected'];

    private const OPERATIONAL_MONTHS = [1, 2, 3, 4, 5];

    private const DISTURBANCE_MONTH_PROFILE = [24, 30, 38, 46, 62];

    private const TASK_MONTH_PROFILE = [20, 28, 36, 48, 68];

    private const REPORT_MONTH_PROFILE = [18, 26, 34, 50, 72];

    private ?Collection $surabayaPathPoints = null;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Utama', 'email' => 'admin@sigaptif.id', 'password' => 'admin123', 'role' => 'admin', 'phone' => '081234567890', 'region_id' => 0, 'is_active' => true],
            ['name' => 'Siti Aminah', 'email' => 'siti@sigaptif.id', 'password' => 'admin123', 'role' => 'admin', 'phone' => '081234567891', 'region_id' => 1, 'is_active' => true],
            ['name' => 'Budi Santoso', 'email' => 'budi@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567892', 'region_id' => 2, 'is_active' => true],
            ['name' => 'Andi Pratama', 'email' => 'andi@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567893', 'region_id' => 3, 'is_active' => true],
            ['name' => 'Doni Wahyudi', 'email' => 'doni@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567894', 'region_id' => 4, 'is_active' => true],
            ['name' => 'Rina Marlina', 'email' => 'rina@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567895', 'region_id' => 5, 'is_active' => true],
            ['name' => 'Joko Susanto', 'email' => 'joko@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567896', 'region_id' => 0, 'is_active' => false],
            ['name' => 'Maya Lestari', 'email' => 'maya@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567897', 'region_id' => 1, 'is_active' => true],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@sigaptif.id', 'password' => 'operator123', 'role' => 'operator', 'phone' => '081234567898', 'region_id' => 2, 'is_active' => true],
            ['name' => 'Nadia Putri', 'email' => 'nadia@sigaptif.id', 'password' => 'admin123', 'role' => 'admin', 'phone' => '081234567899', 'region_id' => 5, 'is_active' => true],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $assets = [
            ['asset_code' => 'ODP-SU-001', 'asset_name' => 'ODP Perak Utara', 'asset_type' => 'ODP', 'region_id' => 0, 'latitude' => -7.2281, 'longitude' => 112.7363, 'address' => 'Jl. Perak Utara No.15', 'status' => 'active', 'installation_date' => '2023-03-15', 'notes' => 'Kapasitas 16 port', 'created_by' => 1],
            ['asset_code' => 'ODP-SU-002', 'asset_name' => 'ODP Kenjeran', 'asset_type' => 'ODP', 'region_id' => 0, 'latitude' => -7.2352, 'longitude' => 112.7729, 'address' => 'Jl. Kenjeran No.88', 'status' => 'active', 'installation_date' => '2023-05-20', 'notes' => null, 'created_by' => 1],
            ['asset_code' => 'ODC-SS-001', 'asset_name' => 'ODC Wonokromo', 'asset_type' => 'ODC', 'region_id' => 1, 'latitude' => -7.3101, 'longitude' => 112.7374, 'address' => 'Jl. Wonokromo No.42', 'status' => 'monitoring', 'installation_date' => '2022-11-10', 'notes' => 'Kapasitas besar', 'created_by' => 2],
            ['asset_code' => 'TNG-SS-001', 'asset_name' => 'Tiang Darmo', 'asset_type' => 'Tiang', 'region_id' => 1, 'latitude' => -7.2906, 'longitude' => 112.7310, 'address' => 'Jl. Raya Darmo No.55', 'status' => 'active', 'installation_date' => '2021-06-01', 'notes' => null, 'created_by' => 2],
            ['asset_code' => 'ODP-ST-001', 'asset_name' => 'ODP MERR', 'asset_type' => 'ODP', 'region_id' => 2, 'latitude' => -7.2780, 'longitude' => 112.7850, 'address' => 'Jl. MERR IIC', 'status' => 'active', 'installation_date' => '2024-01-10', 'notes' => 'Baru dipasang', 'created_by' => 1],
            ['asset_code' => 'KBL-ST-001', 'asset_name' => 'Kabel Feeder Rungkut', 'asset_type' => 'Kabel Feeder', 'region_id' => 2, 'latitude' => -7.3250, 'longitude' => 112.7720, 'address' => 'Jl. Rungkut Industri', 'status' => 'active', 'installation_date' => '2022-08-15', 'notes' => '96 core', 'created_by' => 1],
            ['asset_code' => 'ODP-SB-001', 'asset_name' => 'ODP Lakarsantri', 'asset_type' => 'ODP', 'region_id' => 3, 'latitude' => -7.3150, 'longitude' => 112.6630, 'address' => 'Jl. Lakarsantri No.10', 'status' => 'damaged', 'installation_date' => '2022-02-20', 'notes' => 'Rusak akibat hujan', 'created_by' => 1],
            ['asset_code' => 'STO-SB-001', 'asset_name' => 'STO Tandes', 'asset_type' => 'STO', 'region_id' => 3, 'latitude' => -7.2680, 'longitude' => 112.6850, 'address' => 'Jl. Tandes No.100', 'status' => 'active', 'installation_date' => '2020-01-01', 'notes' => 'STO utama barat', 'created_by' => 2],
            ['asset_code' => 'ODP-SD-001', 'asset_name' => 'ODP Waru', 'asset_type' => 'ODP', 'region_id' => 4, 'latitude' => -7.3565, 'longitude' => 112.7285, 'address' => 'Jl. Waru Sidoarjo', 'status' => 'active', 'installation_date' => '2023-07-01', 'notes' => null, 'created_by' => 1],
            ['asset_code' => 'CLR-SD-001', 'asset_name' => 'Closure Sidoarjo Kota', 'asset_type' => 'Closure', 'region_id' => 4, 'latitude' => -7.4480, 'longitude' => 112.7130, 'address' => 'Jl. Gajah Mada Sidoarjo', 'status' => 'monitoring', 'installation_date' => '2023-01-15', 'notes' => 'Perlu dicek berkala', 'created_by' => 2],
            ['asset_code' => 'ODP-GR-001', 'asset_name' => 'ODP Gresik Kota', 'asset_type' => 'ODP', 'region_id' => 5, 'latitude' => -7.1574, 'longitude' => 112.6533, 'address' => 'Jl. Pahlawan Gresik', 'status' => 'active', 'installation_date' => '2023-09-01', 'notes' => null, 'created_by' => 10],
            ['asset_code' => 'TNG-GR-001', 'asset_name' => 'Tiang Cerme', 'asset_type' => 'Tiang', 'region_id' => 5, 'latitude' => -7.2150, 'longitude' => 112.5850, 'address' => 'Jl. Raya Cerme', 'status' => 'inactive', 'installation_date' => '2019-06-01', 'notes' => 'Sudah tidak aktif', 'created_by' => 10],
            ['asset_code' => 'KBD-SU-001', 'asset_name' => 'Kabel Distribusi Semampir', 'asset_type' => 'Kabel Distribusi', 'region_id' => 0, 'latitude' => -7.2310, 'longitude' => 112.7450, 'address' => 'Jl. Semampir Tengah', 'status' => 'active', 'installation_date' => '2023-02-01', 'notes' => '48 core', 'created_by' => 1],
            ['asset_code' => 'ODP-SS-003', 'asset_name' => 'ODP Gayungan', 'asset_type' => 'ODP', 'region_id' => 1, 'latitude' => -7.3350, 'longitude' => 112.7250, 'address' => 'Jl. Gayungan No.20', 'status' => 'active', 'installation_date' => '2024-02-01', 'notes' => null, 'created_by' => 2],
            ['asset_code' => 'ODC-SU-002', 'asset_name' => 'ODC Tambak Wedi', 'asset_type' => 'ODC', 'region_id' => 0, 'latitude' => -7.2258, 'longitude' => 112.7814, 'address' => 'Jl. Tambak Wedi Baru', 'status' => 'active', 'installation_date' => '2021-04-12', 'notes' => 'Backbone utara', 'created_by' => 1],
            ['asset_code' => 'ODP-SU-003', 'asset_name' => 'ODP Bulak Banteng', 'asset_type' => 'ODP', 'region_id' => 0, 'latitude' => -7.2187, 'longitude' => 112.7554, 'address' => 'Jl. Bulak Banteng Lor', 'status' => 'monitoring', 'installation_date' => '2022-09-22', 'notes' => 'Port hampir penuh', 'created_by' => 1],
            ['asset_code' => 'KBF-SS-002', 'asset_name' => 'Kabel Feeder Ketintang', 'asset_type' => 'Kabel Feeder', 'region_id' => 1, 'latitude' => -7.3176, 'longitude' => 112.7246, 'address' => 'Jl. Ketintang Madya', 'status' => 'active', 'installation_date' => '2021-12-18', 'notes' => '144 core', 'created_by' => 2],
            ['asset_code' => 'CLR-SS-002', 'asset_name' => 'Closure Dukuh Menanggal', 'asset_type' => 'Closure', 'region_id' => 1, 'latitude' => -7.3433, 'longitude' => 112.7212, 'address' => 'Jl. Dukuh Menanggal', 'status' => 'active', 'installation_date' => '2022-05-11', 'notes' => null, 'created_by' => 2],
            ['asset_code' => 'ODP-ST-002', 'asset_name' => 'ODP Nginden', 'asset_type' => 'ODP', 'region_id' => 2, 'latitude' => -7.3005, 'longitude' => 112.7683, 'address' => 'Jl. Nginden Semolo', 'status' => 'active', 'installation_date' => '2023-10-05', 'notes' => null, 'created_by' => 1],
            ['asset_code' => 'TNG-ST-002', 'asset_name' => 'Tiang Panjang Jiwo', 'asset_type' => 'Tiang', 'region_id' => 2, 'latitude' => -7.2986, 'longitude' => 112.7594, 'address' => 'Jl. Panjang Jiwo', 'status' => 'monitoring', 'installation_date' => '2020-08-21', 'notes' => 'Kemiringan ringan', 'created_by' => 1],
            ['asset_code' => 'ODP-SB-002', 'asset_name' => 'ODP Manukan', 'asset_type' => 'ODP', 'region_id' => 3, 'latitude' => -7.2562, 'longitude' => 112.6629, 'address' => 'Jl. Manukan Tama', 'status' => 'active', 'installation_date' => '2023-08-08', 'notes' => null, 'created_by' => 1],
            ['asset_code' => 'KBD-SB-002', 'asset_name' => 'Kabel Distribusi Benowo', 'asset_type' => 'Kabel Distribusi', 'region_id' => 3, 'latitude' => -7.2440, 'longitude' => 112.6404, 'address' => 'Jl. Raya Benowo', 'status' => 'active', 'installation_date' => '2022-06-30', 'notes' => '72 core', 'created_by' => 2],
            ['asset_code' => 'ODC-SD-002', 'asset_name' => 'ODC Gedangan', 'asset_type' => 'ODC', 'region_id' => 4, 'latitude' => -7.3905, 'longitude' => 112.7261, 'address' => 'Jl. Raya Gedangan', 'status' => 'active', 'installation_date' => '2021-03-17', 'notes' => null, 'created_by' => 1],
            ['asset_code' => 'ODP-SD-002', 'asset_name' => 'ODP Taman', 'asset_type' => 'ODP', 'region_id' => 4, 'latitude' => -7.3612, 'longitude' => 112.6886, 'address' => 'Jl. Raya Taman', 'status' => 'damaged', 'installation_date' => '2023-11-12', 'notes' => 'Box retak', 'created_by' => 1],
            ['asset_code' => 'ODP-GR-002', 'asset_name' => 'ODP Manyar', 'asset_type' => 'ODP', 'region_id' => 5, 'latitude' => -7.1181, 'longitude' => 112.6280, 'address' => 'Jl. Manyar Raya', 'status' => 'active', 'installation_date' => '2024-03-03', 'notes' => null, 'created_by' => 10],
            ['asset_code' => 'KBF-GR-002', 'asset_name' => 'Kabel Feeder Kebomas', 'asset_type' => 'Kabel Feeder', 'region_id' => 5, 'latitude' => -7.1692, 'longitude' => 112.6214, 'address' => 'Jl. Kebomas', 'status' => 'active', 'installation_date' => '2022-01-19', 'notes' => '96 core', 'created_by' => 10],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }

        $disturbances = [
            ['disturbance_code' => 'GGN-2026-001', 'asset_id' => 7, 'region_id' => 3, 'type' => 'cable_cut', 'severity' => 5, 'status' => 'open', 'latitude' => -7.3150, 'longitude' => 112.6630, 'description' => 'Kabel FO putus akibat galian proyek jalan.', 'reported_at' => '2026-06-10 08:30:00', 'resolved_at' => null, 'created_by' => 1, 'assigned_to' => 4],
            ['disturbance_code' => 'GGN-2026-002', 'asset_id' => 3, 'region_id' => 1, 'type' => 'high_loss', 'severity' => 3, 'status' => 'on_progress', 'latitude' => -7.3101, 'longitude' => 112.7374, 'description' => 'Redaman tinggi terdeteksi di ODC Wonokromo.', 'reported_at' => '2026-06-09 14:15:00', 'resolved_at' => null, 'created_by' => 1, 'assigned_to' => 3],
            ['disturbance_code' => 'GGN-2026-003', 'asset_id' => null, 'region_id' => 0, 'type' => 'environment', 'severity' => 4, 'status' => 'open', 'latitude' => -7.2400, 'longitude' => 112.7500, 'description' => 'Pohon tumbang menimpa kabel distribusi.', 'reported_at' => '2026-06-11 22:00:00', 'resolved_at' => null, 'created_by' => 2, 'assigned_to' => null],
            ['disturbance_code' => 'GGN-2026-004', 'asset_id' => 10, 'region_id' => 4, 'type' => 'odp_damage', 'severity' => 2, 'status' => 'waiting_validation', 'latitude' => -7.4480, 'longitude' => 112.7130, 'description' => 'Closure retak ringan, sudah diperbaiki.', 'reported_at' => '2026-06-08 10:00:00', 'resolved_at' => '2026-06-09 16:30:00', 'created_by' => 5, 'assigned_to' => 5],
            ['disturbance_code' => 'GGN-2026-005', 'asset_id' => 2, 'region_id' => 0, 'type' => 'device_issue', 'severity' => 3, 'status' => 'resolved', 'latitude' => -7.2352, 'longitude' => 112.7729, 'description' => 'ONU di ODP Kenjeran restart berulang.', 'reported_at' => '2026-06-05 09:00:00', 'resolved_at' => '2026-06-06 11:30:00', 'created_by' => 1, 'assigned_to' => 3],
            ['disturbance_code' => 'GGN-2026-006', 'asset_id' => 11, 'region_id' => 5, 'type' => 'high_loss', 'severity' => 4, 'status' => 'on_progress', 'latitude' => -7.1574, 'longitude' => 112.6533, 'description' => 'Redaman tinggi pada jalur distribusi Gresik Kota.', 'reported_at' => '2026-06-11 07:45:00', 'resolved_at' => null, 'created_by' => 10, 'assigned_to' => 6],
            ['disturbance_code' => 'GGN-2026-007', 'asset_id' => 5, 'region_id' => 2, 'type' => 'cable_cut', 'severity' => 5, 'status' => 'open', 'latitude' => -7.2780, 'longitude' => 112.7850, 'description' => 'Kabel feeder utama MERR terputus.', 'reported_at' => '2026-06-12 06:00:00', 'resolved_at' => null, 'created_by' => 1, 'assigned_to' => 9],
            ['disturbance_code' => 'GGN-2026-008', 'asset_id' => 9, 'region_id' => 4, 'type' => 'environment', 'severity' => 2, 'status' => 'closed', 'latitude' => -7.3565, 'longitude' => 112.7285, 'description' => 'Banjir ringan di area ODP Waru.', 'reported_at' => '2026-06-01 18:00:00', 'resolved_at' => '2026-06-02 08:00:00', 'created_by' => 5, 'assigned_to' => 5],
            ['disturbance_code' => 'GGN-2026-009', 'asset_id' => 16, 'region_id' => 0, 'type' => 'high_loss', 'severity' => 3, 'status' => 'open', 'latitude' => -7.2187, 'longitude' => 112.7554, 'description' => 'Port pelanggan mengalami loss fluktuatif.', 'reported_at' => '2026-06-13 09:20:00', 'resolved_at' => null, 'created_by' => 2, 'assigned_to' => 3],
            ['disturbance_code' => 'GGN-2026-010', 'asset_id' => 18, 'region_id' => 1, 'type' => 'odc_issue', 'severity' => 2, 'status' => 'resolved', 'latitude' => -7.3433, 'longitude' => 112.7212, 'description' => 'Seal closure longgar setelah hujan.', 'reported_at' => '2026-06-07 13:40:00', 'resolved_at' => '2026-06-08 10:10:00', 'created_by' => 2, 'assigned_to' => 8],
            ['disturbance_code' => 'GGN-2026-011', 'asset_id' => 20, 'region_id' => 2, 'type' => 'environment', 'severity' => 2, 'status' => 'on_progress', 'latitude' => -7.2986, 'longitude' => 112.7594, 'description' => 'Tiang miring perlu penguatan bracket.', 'reported_at' => '2026-06-12 15:25:00', 'resolved_at' => null, 'created_by' => 1, 'assigned_to' => 9],
            ['disturbance_code' => 'GGN-2026-012', 'asset_id' => 22, 'region_id' => 3, 'type' => 'cable_cut', 'severity' => 4, 'status' => 'waiting_validation', 'latitude' => -7.2440, 'longitude' => 112.6404, 'description' => 'Kabel distribusi terkelupas terkena pekerjaan drainase.', 'reported_at' => '2026-06-06 11:00:00', 'resolved_at' => '2026-06-07 17:00:00', 'created_by' => 1, 'assigned_to' => 4],
            ['disturbance_code' => 'GGN-2026-013', 'asset_id' => 24, 'region_id' => 4, 'type' => 'odp_damage', 'severity' => 3, 'status' => 'open', 'latitude' => -7.3612, 'longitude' => 112.6886, 'description' => 'Box ODP retak dan engsel patah.', 'reported_at' => '2026-06-13 16:45:00', 'resolved_at' => null, 'created_by' => 5, 'assigned_to' => 5],
            ['disturbance_code' => 'GGN-2026-014', 'asset_id' => 26, 'region_id' => 5, 'type' => 'device_issue', 'severity' => 2, 'status' => 'closed', 'latitude' => -7.1692, 'longitude' => 112.6214, 'description' => 'Panel distribusi butuh pembersihan konektor.', 'reported_at' => '2026-06-04 10:30:00', 'resolved_at' => '2026-06-04 15:00:00', 'created_by' => 10, 'assigned_to' => 6],
        ];

        foreach ($disturbances as $disturbance) {
            Disturbance::create($disturbance);
        }

        $tasks = [
            ['task_code' => 'PNG-2026-001', 'asset_id' => 13, 'region_id' => 0, 'assigned_to' => 3, 'title' => 'Pemangkasan pohon Semampir', 'description' => 'Ranting pohon mendekati kabel distribusi 48 core.', 'priority' => 'high', 'status' => 'assigned', 'latitude' => -7.2310, 'longitude' => 112.7450, 'due_date' => '2026-06-15', 'created_by' => 1],
            ['task_code' => 'PNG-2026-002', 'asset_id' => 6, 'region_id' => 2, 'assigned_to' => 9, 'title' => 'Clearing jalur Rungkut Industri', 'description' => 'Vegetasi menutup akses kabel feeder.', 'priority' => 'critical', 'status' => 'on_progress', 'latitude' => -7.3250, 'longitude' => 112.7720, 'due_date' => '2026-06-13', 'created_by' => 1],
            ['task_code' => 'PNG-2026-003', 'asset_id' => 11, 'region_id' => 5, 'assigned_to' => 6, 'title' => 'Inspeksi pohon Gresik Kota', 'description' => 'Pemangkasan ringan area ODP Gresik Kota.', 'priority' => 'medium', 'status' => 'waiting_validation', 'latitude' => -7.1574, 'longitude' => 112.6533, 'due_date' => '2026-06-12', 'created_by' => 10],
            ['task_code' => 'PNG-2026-004', 'asset_id' => 4, 'region_id' => 1, 'assigned_to' => null, 'title' => 'Survey jalur Darmo', 'description' => 'Cek potensi gangguan pohon sekitar tiang Darmo.', 'priority' => 'low', 'status' => 'draft', 'latitude' => -7.2906, 'longitude' => 112.7310, 'due_date' => '2026-06-20', 'created_by' => 2],
            ['task_code' => 'PNG-2026-005', 'asset_id' => 15, 'region_id' => 0, 'assigned_to' => 3, 'title' => 'Normalisasi akses Tambak Wedi', 'description' => 'Pembersihan jalur sekitar ODC Tambak Wedi.', 'priority' => 'medium', 'status' => 'assigned', 'latitude' => -7.2258, 'longitude' => 112.7814, 'due_date' => '2026-06-18', 'created_by' => 1],
            ['task_code' => 'PNG-2026-006', 'asset_id' => 17, 'region_id' => 1, 'assigned_to' => 8, 'title' => 'Pemangkasan Ketintang', 'description' => 'Dahan besar mulai menempel pada jalur feeder.', 'priority' => 'high', 'status' => 'assigned', 'latitude' => -7.3176, 'longitude' => 112.7246, 'due_date' => '2026-06-17', 'created_by' => 2],
            ['task_code' => 'PNG-2026-007', 'asset_id' => 20, 'region_id' => 2, 'assigned_to' => 9, 'title' => 'Penguatan area Panjang Jiwo', 'description' => 'Bersihkan area dan cek kebutuhan penguatan tiang.', 'priority' => 'medium', 'status' => 'on_progress', 'latitude' => -7.2986, 'longitude' => 112.7594, 'due_date' => '2026-06-19', 'created_by' => 1],
            ['task_code' => 'PNG-2026-008', 'asset_id' => 21, 'region_id' => 3, 'assigned_to' => 4, 'title' => 'Inspeksi Manukan', 'description' => 'Inspeksi jalur drop pelanggan padat.', 'priority' => 'low', 'status' => 'completed', 'latitude' => -7.2562, 'longitude' => 112.6629, 'due_date' => '2026-06-10', 'created_by' => 1],
            ['task_code' => 'PNG-2026-009', 'asset_id' => 23, 'region_id' => 4, 'assigned_to' => 5, 'title' => 'Clearing Gedangan', 'description' => 'Vegetasi menutup panel akses ODC.', 'priority' => 'medium', 'status' => 'assigned', 'latitude' => -7.3905, 'longitude' => 112.7261, 'due_date' => '2026-06-21', 'created_by' => 1],
            ['task_code' => 'PNG-2026-010', 'asset_id' => 25, 'region_id' => 5, 'assigned_to' => 6, 'title' => 'Pemangkasan Manyar', 'description' => 'Ranting menyentuh kabel drop dekat ODP Manyar.', 'priority' => 'high', 'status' => 'draft', 'latitude' => -7.1181, 'longitude' => 112.6280, 'due_date' => '2026-06-22', 'created_by' => 10],
        ];

        foreach ($tasks as $task) {
            PruningTask::create($task);
        }

        $reports = [
            ['report_code' => 'LPR-2026-001', 'task_id' => null, 'disturbance_id' => 2, 'asset_id' => 3, 'operator_id' => 3, 'report_type' => 'disturbance', 'condition_before' => 'Konektor pada ODC Wonokromo kotor.', 'action_taken' => 'Membersihkan konektor dan test OTDR ulang.', 'condition_after' => 'Redaman kembali normal.', 'latitude' => -7.3101, 'longitude' => 112.7374, 'attachments' => [], 'status' => 'submitted', 'admin_note' => null, 'submitted_at' => '2026-06-10 16:00:00', 'approved_at' => null],
            ['report_code' => 'LPR-2026-002', 'task_id' => null, 'disturbance_id' => 4, 'asset_id' => 10, 'operator_id' => 5, 'report_type' => 'disturbance', 'condition_before' => 'Closure retak di bagian seal atas.', 'action_taken' => 'Mengganti seal closure.', 'condition_after' => 'Closure tertutup rapat.', 'latitude' => -7.4480, 'longitude' => 112.7130, 'attachments' => [], 'status' => 'submitted', 'admin_note' => null, 'submitted_at' => '2026-06-09 15:00:00', 'approved_at' => null],
            ['report_code' => 'LPR-2026-003', 'task_id' => null, 'disturbance_id' => 5, 'asset_id' => 2, 'operator_id' => 3, 'report_type' => 'disturbance', 'condition_before' => 'ONU restart berulang.', 'action_taken' => 'Ganti ONU dan konfigurasi ulang.', 'condition_after' => 'Koneksi normal.', 'latitude' => -7.2352, 'longitude' => 112.7729, 'attachments' => [], 'status' => 'approved', 'admin_note' => 'Dokumentasi lengkap.', 'submitted_at' => '2026-06-06 10:00:00', 'approved_at' => '2026-06-06 14:00:00'],
            ['report_code' => 'LPR-2026-004', 'task_id' => null, 'disturbance_id' => null, 'asset_id' => 14, 'operator_id' => 4, 'report_type' => 'inspection', 'condition_before' => 'Inspeksi rutin ODP Gayungan.', 'action_taken' => 'Cek visual dan redaman port.', 'condition_after' => 'Semua port baik.', 'latitude' => -7.3350, 'longitude' => 112.7250, 'attachments' => [], 'status' => 'approved', 'admin_note' => null, 'submitted_at' => '2026-06-07 12:00:00', 'approved_at' => '2026-06-07 16:00:00'],
            ['report_code' => 'LPR-2026-005', 'task_id' => null, 'disturbance_id' => 8, 'asset_id' => 9, 'operator_id' => 5, 'report_type' => 'disturbance', 'condition_before' => 'Area ODP terendam banjir ringan.', 'action_taken' => 'Membersihkan area dan cek fisik ODP.', 'condition_after' => 'ODP aman.', 'latitude' => -7.3565, 'longitude' => 112.7285, 'attachments' => [], 'status' => 'rejected', 'admin_note' => 'Foto dokumentasi kurang jelas.', 'submitted_at' => '2026-06-02 10:00:00', 'approved_at' => null],
            ['report_code' => 'LPR-2026-006', 'task_id' => 3, 'disturbance_id' => null, 'asset_id' => 11, 'operator_id' => 6, 'report_type' => 'pruning', 'condition_before' => 'Ranting menempel pada sisi bawah kabel drop.', 'action_taken' => 'Pemangkasan ranting dan pembersihan area.', 'condition_after' => 'Jalur bersih.', 'latitude' => -7.1574, 'longitude' => 112.6533, 'attachments' => [], 'status' => 'submitted', 'admin_note' => null, 'submitted_at' => '2026-06-12 15:20:00', 'approved_at' => null],
            ['report_code' => 'LPR-2026-007', 'task_id' => 8, 'disturbance_id' => null, 'asset_id' => 21, 'operator_id' => 4, 'report_type' => 'pruning', 'condition_before' => 'Jalur drop tertutup ranting kecil.', 'action_taken' => 'Pemangkasan ringan.', 'condition_after' => 'Akses teknisi aman.', 'latitude' => -7.2562, 'longitude' => 112.6629, 'attachments' => [], 'status' => 'approved', 'admin_note' => 'Selesai.', 'submitted_at' => '2026-06-10 11:30:00', 'approved_at' => '2026-06-10 14:20:00'],
            ['report_code' => 'LPR-2026-008', 'task_id' => null, 'disturbance_id' => 10, 'asset_id' => 18, 'operator_id' => 8, 'report_type' => 'disturbance', 'condition_before' => 'Seal closure longgar.', 'action_taken' => 'Pengencangan dan penambahan sealant.', 'condition_after' => 'Closure kedap.', 'latitude' => -7.3433, 'longitude' => 112.7212, 'attachments' => [], 'status' => 'approved', 'admin_note' => null, 'submitted_at' => '2026-06-08 09:40:00', 'approved_at' => '2026-06-08 12:00:00'],
            ['report_code' => 'LPR-2026-009', 'task_id' => null, 'disturbance_id' => 12, 'asset_id' => 22, 'operator_id' => 4, 'report_type' => 'disturbance', 'condition_before' => 'Kabel distribusi terkelupas.', 'action_taken' => 'Isolasi ulang dan penguatan pelindung.', 'condition_after' => 'Link normal.', 'latitude' => -7.2440, 'longitude' => 112.6404, 'attachments' => [], 'status' => 'submitted', 'admin_note' => null, 'submitted_at' => '2026-06-07 17:10:00', 'approved_at' => null],
            ['report_code' => 'LPR-2026-010', 'task_id' => null, 'disturbance_id' => 14, 'asset_id' => 26, 'operator_id' => 6, 'report_type' => 'disturbance', 'condition_before' => 'Panel feeder kotor.', 'action_taken' => 'Pembersihan konektor dan panel.', 'condition_after' => 'Sinyal stabil.', 'latitude' => -7.1692, 'longitude' => 112.6214, 'attachments' => [], 'status' => 'approved', 'admin_note' => null, 'submitted_at' => '2026-06-04 14:00:00', 'approved_at' => '2026-06-04 16:00:00'],
        ];

        foreach ($reports as $report) {
            FieldReport::create($report);
        }

        $logs = [
            [1, 'Menambah aset ODP-SU-001', 'assets'],
            [1, 'Membuat gangguan GGN-2026-001', 'disturbances'],
            [3, 'Submit laporan LPR-2026-001', 'reports'],
            [5, 'Submit laporan LPR-2026-002', 'reports'],
            [1, 'Membuat tugas pemangkasan PNG-2026-001', 'pruning'],
            [10, 'Membuat tugas pemangkasan PNG-2026-003', 'pruning'],
            [4, 'Menyelesaikan tugas PNG-2026-008', 'pruning'],
            [2, 'Validasi laporan LPR-2026-003', 'reports'],
            [1, 'Memperbarui status gangguan GGN-2026-005', 'disturbances'],
            [6, 'Submit laporan LPR-2026-006', 'reports'],
            [8, 'Submit laporan LPR-2026-008', 'reports'],
            [10, 'Menambah aset KBF-GR-002', 'assets'],
        ];

        foreach ($logs as [$userId, $action, $module]) {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'module' => $module,
            ]);
        }

        $this->seedExtraAssets(200);
        $this->seedExtraDisturbances(200);
        $this->seedExtraPruningTasks(200);
        $this->seedExtraFieldReports(200);
        $this->seedExtraActivityLogs(200);
    }

    private function seedExtraAssets(int $count): void
    {
        $adminIds = [1, 2, 10];
        $statusCycle = ['active', 'monitoring', 'damaged', 'inactive'];
        $prefixMap = [
            'ODP' => 'ODP',
            'ODC' => 'ODC',
            'Tiang' => 'TNG',
            'Kabel Feeder' => 'KBF',
            'Kabel Distribusi' => 'KBD',
            'Closure' => 'CLR',
            'STO' => 'STO',
        ];
        $regionCodes = ['SU', 'SS', 'ST', 'SB'];
        $points = $this->getSurabayaPathPoints();

        for ($i = 1; $i <= $count; $i++) {
            $point = $this->pickDistributedPoint($points, $i, $count);
            $regionId = $this->inferSurabayaRegionId($point['lat'], $point['lng']);
            $assetType = self::ASSET_TYPES[($i - 1) % count(self::ASSET_TYPES)];
            $prefix = $prefixMap[$assetType];
            $regionCode = $regionCodes[$regionId];
            $assetNumber = 100 + $i;

            Asset::create([
                'asset_code' => sprintf('%s-%s-%03d', $prefix, $regionCode, $assetNumber),
                'asset_name' => sprintf('%s %s %03d', $assetType, self::REGIONS[$regionId], $assetNumber),
                'asset_type' => $assetType,
                'region_id' => $regionId,
                'latitude' => $point['lat'],
                'longitude' => $point['lng'],
                'address' => sprintf('Jl. %s Koridor %03d', self::REGIONS[$regionId], $assetNumber),
                'status' => $statusCycle[($i - 1) % count($statusCycle)],
                'installation_date' => sprintf('202%s-%02d-%02d', (($i % 4) + 1), (($i % 12) + 1), (($i % 27) + 1)),
                'notes' => sprintf('Data tambahan aset batch %03d', $assetNumber),
                'created_by' => $adminIds[($i - 1) % count($adminIds)],
            ]);
        }
    }

    private function seedExtraDisturbances(int $count): void
    {
        $assetsByRegion = $this->getAssetIdsByRegion();
        $operatorIds = [3, 4, 5, 6, 8, 9];
        $creatorIds = [1, 2, 10];
        $statusSequence = ['open', 'open', 'on_progress', 'waiting_validation', 'resolved', 'closed', 'on_progress', 'resolved', 'open', 'closed'];
        $regionSequence = [1, 0, 2, 1, 3, 0, 4, 2, 5, 1, 0, 3];
        $typeSequence = ['cable_cut', 'high_loss', 'environment', 'odp_damage', 'high_loss', 'odc_issue', 'device_issue', 'environment', 'cable_cut', 'other'];
        $severitySequence = [5, 4, 3, 3, 2, 4, 2, 3, 5, 1, 4, 2];

        for ($i = 1; $i <= $count; $i++) {
            $regionId = $regionSequence[($i - 1) % count($regionSequence)];
            $assetPool = $assetsByRegion[$regionId] ?? [];
            $assetId = $assetPool[($i * 3) % count($assetPool)];
            $asset = Asset::find($assetId);
            $status = $statusSequence[($i - 1) % count($statusSequence)];
            $month = $this->pickMonthFromProfile($i, self::DISTURBANCE_MONTH_PROFILE);
            $reportedDay = (($i - 1) % 28) + 1;
            $reportedHour = 7 + ($i % 10);
            $reportedAt = sprintf('2026-%02d-%02d %02d:%02d:00', $month, $reportedDay, $reportedHour, ($i * 7) % 60);
            $resolvedAt = null;

            if (in_array($status, ['resolved', 'closed'], true)) {
                $resolveMonth = $month;
                $resolveDay = min(28, $reportedDay + (($i % 3) + 1));

                if ($month < 6 && ($reportedDay >= 25 || $i % 9 === 0)) {
                    $resolveMonth = min(6, $month + 1);
                    $resolveDay = (($i * 2) % 5) + 1;
                }

                $resolvedAt = sprintf('2026-%02d-%02d %02d:%02d:00', $resolveMonth, $resolveDay, min(23, $reportedHour + 3), ($i * 11) % 60);
            }

            Disturbance::create([
                'disturbance_code' => sprintf('GGN-2026-%03d', 100 + $i),
                'asset_id' => $assetId,
                'region_id' => $asset?->region_id ?? $regionId,
                'type' => $typeSequence[($i - 1) % count($typeSequence)],
                'severity' => $severitySequence[($i - 1) % count($severitySequence)],
                'status' => $status,
                'latitude' => $asset?->latitude,
                'longitude' => $asset?->longitude,
                'description' => sprintf('Gangguan tambahan otomatis pada %s nomor %03d.', $asset?->asset_code ?? 'aset umum', 100 + $i),
                'reported_at' => $reportedAt,
                'resolved_at' => $resolvedAt,
                'created_by' => $creatorIds[($i - 1) % count($creatorIds)],
                'assigned_to' => $operatorIds[($i - 1) % count($operatorIds)],
            ]);
        }
    }

    private function seedExtraPruningTasks(int $count): void
    {
        $assetsByRegion = $this->getAssetIdsByRegion();
        $operatorIds = [3, 4, 5, 6, 8, 9];
        $creatorIds = [1, 2, 10];
        $regionSequence = [1, 0, 2, 1, 3, 4, 0, 2, 5, 1, 0, 3];
        $statusSequence = ['assigned', 'on_progress', 'assigned', 'waiting_validation', 'completed', 'draft', 'assigned', 'on_progress', 'rejected', 'completed'];
        $prioritySequence = ['critical', 'high', 'medium', 'high', 'low', 'medium', 'critical', 'medium', 'high', 'low'];

        for ($i = 1; $i <= $count; $i++) {
            $regionId = $regionSequence[($i - 1) % count($regionSequence)];
            $assetPool = $assetsByRegion[$regionId] ?? [];
            $assetId = $assetPool[($i * 5) % count($assetPool)];
            $asset = Asset::find($assetId);
            $status = $statusSequence[($i - 1) % count($statusSequence)];
            $assignedTo = $status === 'draft' ? null : $operatorIds[($i - 1) % count($operatorIds)];
            $month = $this->pickMonthFromProfile($i, self::TASK_MONTH_PROFILE);

            PruningTask::create([
                'task_code' => sprintf('PNG-2026-%03d', 100 + $i),
                'asset_id' => $assetId,
                'region_id' => $asset?->region_id ?? $regionId,
                'assigned_to' => $assignedTo,
                'title' => sprintf('Pemangkasan preventif koridor %03d', 100 + $i),
                'description' => sprintf('Pembersihan vegetasi dan inspeksi jalur di sekitar %s.', $asset?->asset_code ?? 'aset terkait'),
                'priority' => $prioritySequence[($i - 1) % count($prioritySequence)],
                'status' => $status,
                'latitude' => $asset?->latitude,
                'longitude' => $asset?->longitude,
                'due_date' => sprintf('2026-%02d-%02d', $month, (($i - 1) % 28) + 1),
                'created_by' => $creatorIds[($i - 1) % count($creatorIds)],
            ]);
        }
    }

    private function seedExtraFieldReports(int $count): void
    {
        $taskIds = PruningTask::query()->pluck('id')->all();
        $disturbanceIds = Disturbance::query()->pluck('id')->all();
        $assetsByRegion = $this->getAssetIdsByRegion();
        $operatorIds = [3, 4, 5, 6, 8, 9];
        $regionSequence = [1, 0, 2, 1, 3, 4, 0, 2, 5, 1, 0, 3];
        $statusSequence = ['approved', 'submitted', 'approved', 'submitted', 'rejected', 'approved', 'submitted', 'approved', 'submitted', 'rejected'];

        for ($i = 1; $i <= $count; $i++) {
            $isPruning = $i % 3 !== 1;
            $taskId = $isPruning ? $taskIds[($i * 7) % count($taskIds)] : null;
            $disturbanceId = $isPruning ? null : $disturbanceIds[($i * 4) % count($disturbanceIds)];
            $regionId = $regionSequence[($i - 1) % count($regionSequence)];
            $assetPool = $assetsByRegion[$regionId] ?? [];
            $assetId = $assetPool[($i * 6) % count($assetPool)];
            $asset = Asset::find($assetId);
            $status = $statusSequence[($i - 1) % count($statusSequence)];
            $month = $this->pickMonthFromProfile($i, self::REPORT_MONTH_PROFILE);
            $submittedDay = (($i - 1) % 28) + 1;
            $submittedAt = sprintf('2026-%02d-%02d %02d:%02d:00', $month, $submittedDay, 8 + ($i % 9), ($i * 9) % 60);
            $approvedAt = null;

            if ($status === 'approved') {
                $approvedMonth = $month;
                $approvedDay = min(28, $submittedDay + (($i % 2) + 1));

                if ($submittedDay >= 26 && $month < 6) {
                    $approvedMonth = $month + 1;
                    $approvedDay = (($i * 3) % 4) + 1;
                }

                $approvedAt = sprintf('2026-%02d-%02d %02d:%02d:00', $approvedMonth, $approvedDay, 13 + ($i % 5), ($i * 5) % 60);
            }

            FieldReport::create([
                'report_code' => sprintf('LPR-2026-%03d', 100 + $i),
                'task_id' => $taskId,
                'disturbance_id' => $disturbanceId,
                'asset_id' => $assetId,
                'operator_id' => $operatorIds[($i - 1) % count($operatorIds)],
                'report_type' => $isPruning ? 'pruning' : 'disturbance',
                'condition_before' => sprintf('Kondisi awal pemeriksaan batch %03d.', 100 + $i),
                'action_taken' => sprintf('Tindakan lapangan tambahan batch %03d sudah dilakukan.', 100 + $i),
                'condition_after' => sprintf('Kondisi akhir pada %s stabil dan aman.', $asset?->asset_code ?? 'aset'),
                'latitude' => $asset?->latitude,
                'longitude' => $asset?->longitude,
                'attachments' => [],
                'status' => $status,
                'admin_note' => $status === 'rejected' ? 'Perlu melengkapi dokumentasi tambahan.' : null,
                'submitted_at' => $submittedAt,
                'approved_at' => $approvedAt,
            ]);
        }
    }

    private function seedExtraActivityLogs(int $count): void
    {
        $userIds = [1, 2, 3, 4, 5, 6, 8, 9, 10];
        $modules = ['assets', 'disturbances', 'pruning', 'reports', 'system'];
        $actions = [
            'Melakukan pembaruan data aset',
            'Mencatat temuan gangguan lapangan',
            'Menugaskan inspeksi vegetasi',
            'Melakukan submit laporan operasional',
            'Memeriksa dashboard monitoring',
        ];

        for ($i = 1; $i <= $count; $i++) {
            ActivityLog::create([
                'user_id' => $userIds[($i - 1) % count($userIds)],
                'action' => sprintf('%s batch %03d', $actions[($i - 1) % count($actions)], 100 + $i),
                'module' => $modules[($i - 1) % count($modules)],
            ]);
        }
    }

    private function getSurabayaPathPoints(): Collection
    {
        if ($this->surabayaPathPoints !== null) {
            return $this->surabayaPathPoints;
        }

        $geojsonPath = dirname(base_path()).DIRECTORY_SEPARATOR.'Infranexiaa'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'komdigi-fiber-surabaya.geojson';

        if (! file_exists($geojsonPath)) {
            $this->surabayaPathPoints = collect([
                ['lng' => 112.7348, 'lat' => -7.2313],
                ['lng' => 112.7295, 'lat' => -7.3361],
                ['lng' => 112.7453, 'lat' => -7.2765],
                ['lng' => 112.7245, 'lat' => -7.3348],
            ]);

            return $this->surabayaPathPoints;
        }

        $decoded = json_decode(file_get_contents($geojsonPath), true);

        $points = collect($decoded['features'] ?? [])
            ->flatMap(function (array $feature): array {
                $geometry = $feature['geometry'] ?? [];
                $type = $geometry['type'] ?? null;
                $coordinates = $geometry['coordinates'] ?? [];

                if ($type !== 'MultiLineString') {
                    return [];
                }

                return collect($coordinates)
                    ->flatMap(fn (array $line) => $line)
                    ->map(function (array $coord): ?array {
                        if (count($coord) < 2) {
                            return null;
                        }

                        return [
                            'lng' => round((float) $coord[0], 7),
                            'lat' => round((float) $coord[1], 7),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();
            })
            ->filter(function (array $point): bool {
                return $point['lng'] >= 112.60
                    && $point['lng'] <= 112.86
                    && $point['lat'] >= -7.38
                    && $point['lat'] <= -7.18;
            })
            ->unique(fn (array $point) => $point['lng'].'|'.$point['lat'])
            ->values();

        $this->surabayaPathPoints = $points->isNotEmpty()
            ? $this->spreadSurabayaPoints($points)
            : collect([['lng' => 112.7348, 'lat' => -7.2313]]);

        return $this->surabayaPathPoints;
    }

    private function pickOperationalMonth(int $position): int
    {
        return self::OPERATIONAL_MONTHS[($position - 1) % count(self::OPERATIONAL_MONTHS)];
    }

    private function pickMonthFromProfile(int $position, array $profile): int
    {
        $cursor = 0;

        foreach ($profile as $index => $limit) {
            $cursor += $limit;

            if ($position <= $cursor) {
                return self::OPERATIONAL_MONTHS[$index];
            }
        }

        return self::OPERATIONAL_MONTHS[array_key_last(self::OPERATIONAL_MONTHS)];
    }

    private function getAssetIdsByRegion(): array
    {
        return Asset::query()
            ->orderBy('id')
            ->get(['id', 'region_id'])
            ->groupBy('region_id')
            ->map(fn (Collection $items) => $items->pluck('id')->values()->all())
            ->all();
    }

    private function spreadSurabayaPoints(Collection $points): Collection
    {
        $buckets = $points
            ->groupBy(function (array $point): string {
                return number_format($point['lat'], 3, '.', '').'|'.number_format($point['lng'], 3, '.', '');
            })
            ->sortKeys()
            ->map(fn (Collection $bucket) => $bucket->values())
            ->values();

        $spread = collect();
        $level = 0;

        while (true) {
            $added = false;

            foreach ($buckets as $bucket) {
                if ($bucket->has($level)) {
                    $spread->push($bucket[$level]);
                    $added = true;
                }
            }

            if (! $added) {
                break;
            }

            $level++;
        }

        return $spread->values();
    }

    private function pickDistributedPoint(Collection $points, int $position, int $total): array
    {
        $count = $points->count();

        if ($count === 0) {
            return ['lng' => 112.7348, 'lat' => -7.2313];
        }

        if ($total <= 1) {
            return $points->first();
        }

        $index = (int) floor((($position - 1) * ($count - 1)) / ($total - 1));

        return $points[$index];
    }

    private function inferSurabayaRegionId(float $lat, float $lng): int
    {
        if ($lat <= -7.295) {
            return 1;
        }

        if ($lng >= 112.75) {
            return 2;
        }

        if ($lng <= 112.69) {
            return 3;
        }

        return 0;
    }
}
