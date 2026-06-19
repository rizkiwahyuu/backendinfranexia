<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActivityLogController extends CrudController
{
    protected string $model = ActivityLog::class;

    protected array $storeRules = [
        'user_id' => ['nullable', 'integer', 'exists:users,id'],
        'action' => ['required', 'string', 'max:255'],
        'module' => ['required', 'string', 'max:100'],
    ];

    protected function filter(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('module'), fn ($q) => $q->where('module', $request->module));
    }
}
