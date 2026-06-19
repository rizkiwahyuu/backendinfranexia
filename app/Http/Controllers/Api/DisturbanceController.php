<?php

namespace App\Http\Controllers\Api;

use App\Models\Disturbance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DisturbanceController extends CrudController
{
    protected string $model = Disturbance::class;

    protected array $storeRules = [
        'disturbance_code' => ['required', 'string', 'max:50'],
        'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        'region_id' => ['required', 'integer', 'min:0'],
        'type' => ['required', 'string'],
        'severity' => ['required', 'integer', 'min:1', 'max:5'],
        'status' => ['required', 'string'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'description' => ['required', 'string'],
        'reported_at' => ['nullable', 'date'],
        'resolved_at' => ['nullable', 'date'],
        'created_by' => ['nullable', 'integer', 'exists:users,id'],
        'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
    ];

    protected array $updateRules = [
        'disturbance_code' => ['sometimes', 'string', 'max:50'],
        'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
        'region_id' => ['sometimes', 'integer', 'min:0'],
        'type' => ['sometimes', 'string'],
        'severity' => ['sometimes', 'integer', 'min:1', 'max:5'],
        'status' => ['sometimes', 'string'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'description' => ['sometimes', 'string'],
        'reported_at' => ['nullable', 'date'],
        'resolved_at' => ['nullable', 'date'],
        'created_by' => ['nullable', 'integer', 'exists:users,id'],
        'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
    ];

    protected function filter(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('region_id'), fn ($q) => $q->where('region_id', $request->integer('region_id')))
            ->when($request->filled('asset_id'), fn ($q) => $q->where('asset_id', $request->integer('asset_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
    }
}
