<?php

namespace App\Http\Controllers\Api;

use App\Models\FieldReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FieldReportController extends CrudController
{
    protected string $model = FieldReport::class;

    protected array $storeRules = [
        'report_code' => ['required', 'string', 'max:50'],
        'task_id' => ['nullable', 'integer', 'exists:pruning_tasks,id'],
        'disturbance_id' => ['nullable', 'integer', 'exists:disturbances,id'],
        'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        'operator_id' => ['nullable', 'integer', 'exists:users,id'],
        'report_type' => ['required', 'string'],
        'condition_before' => ['nullable', 'string'],
        'action_taken' => ['nullable', 'string'],
        'condition_after' => ['nullable', 'string'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'attachments' => ['nullable', 'array'],
        'status' => ['required', 'string'],
        'admin_note' => ['nullable', 'string'],
        'submitted_at' => ['nullable', 'date'],
        'approved_at' => ['nullable', 'date'],
    ];

    protected array $updateRules = [
        'report_code' => ['sometimes', 'string', 'max:50'],
        'task_id' => ['nullable', 'integer', 'exists:pruning_tasks,id'],
        'disturbance_id' => ['nullable', 'integer', 'exists:disturbances,id'],
        'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        'operator_id' => ['nullable', 'integer', 'exists:users,id'],
        'report_type' => ['sometimes', 'string'],
        'condition_before' => ['nullable', 'string'],
        'action_taken' => ['nullable', 'string'],
        'condition_after' => ['nullable', 'string'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'attachments' => ['nullable', 'array'],
        'status' => ['sometimes', 'string'],
        'admin_note' => ['nullable', 'string'],
        'submitted_at' => ['nullable', 'date'],
        'approved_at' => ['nullable', 'date'],
    ];

    protected function filter(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('operator_id'), fn ($q) => $q->where('operator_id', $request->integer('operator_id')))
            ->when($request->filled('asset_id'), fn ($q) => $q->where('asset_id', $request->integer('asset_id')))
            ->when($request->filled('report_type'), fn ($q) => $q->where('report_type', $request->report_type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
    }
}
