<?php

namespace App\Http\Controllers\Api;

use App\Models\PruningTask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PruningTaskController extends CrudController
{
    protected string $model = PruningTask::class;

    protected array $storeRules = [
        'task_code' => ['required', 'string', 'max:50'],
        'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        'region_id' => ['required', 'integer', 'min:0'],
        'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        'title' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'priority' => ['required', 'string'],
        'status' => ['required', 'string'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'due_date' => ['nullable', 'date'],
        'created_by' => ['nullable', 'integer', 'exists:users,id'],
    ];

    protected array $updateRules = [
        'task_code' => ['sometimes', 'string', 'max:50'],
        'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        'region_id' => ['sometimes', 'integer', 'min:0'],
        'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        'title' => ['sometimes', 'string', 'max:255'],
        'description' => ['sometimes', 'string'],
        'priority' => ['sometimes', 'string'],
        'status' => ['sometimes', 'string'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'due_date' => ['nullable', 'date'],
        'created_by' => ['nullable', 'integer', 'exists:users,id'],
    ];

    protected function filter(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('region_id'), fn ($q) => $q->where('region_id', $request->integer('region_id')))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
    }
}
