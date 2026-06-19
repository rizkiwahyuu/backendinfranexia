<?php

namespace App\Http\Controllers\Api;

class MetaController
{
    public function __invoke(): array
    {
        return [
            'regions' => ['Surabaya Utara', 'Surabaya Selatan', 'Surabaya Timur', 'Surabaya Barat', 'Sidoarjo', 'Gresik'],
            'asset_types' => ['ODP', 'ODC', 'Tiang', 'Kabel Feeder', 'Kabel Distribusi', 'Closure', 'STO'],
            'disturbance_types' => [
                'cable_cut' => 'Kabel Putus',
                'high_loss' => 'Redaman Tinggi',
                'odp_damage' => 'ODP Rusak',
                'odc_issue' => 'ODC Bermasalah',
                'device_issue' => 'Gangguan Perangkat',
                'environment' => 'Gangguan Lingkungan',
                'other' => 'Lainnya',
            ],
            'priority_labels' => [
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
                'critical' => 'Kritis',
            ],
            'status_labels' => [
                'active' => 'Aktif',
                'monitoring' => 'Monitoring',
                'damaged' => 'Rusak',
                'inactive' => 'Nonaktif',
                'open' => 'Open',
                'on_progress' => 'On Progress',
                'waiting_validation' => 'Menunggu Validasi',
                'resolved' => 'Resolved',
                'closed' => 'Closed',
                'draft' => 'Draft',
                'assigned' => 'Assigned',
                'completed' => 'Completed',
                'submitted' => 'Submitted',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ],
        ];
    }
}
