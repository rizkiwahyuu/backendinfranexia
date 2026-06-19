<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AssetController extends CrudController
{
    protected string $model = Asset::class;

    protected array $storeRules = [
        'asset_code' => ['required', 'string', 'max:50'],
        'asset_name' => ['required', 'string', 'max:255'],
        'asset_type' => ['required', 'string', 'max:100'],
        'region_id' => ['required', 'integer', 'min:0'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'address' => ['required', 'string', 'max:255'],
        'status' => ['required', 'string'],
        'installation_date' => ['nullable', 'date'],
        'notes' => ['nullable', 'string'],
        'created_by' => ['nullable', 'integer', 'exists:users,id'],
    ];

    protected array $updateRules = [
        'asset_code' => ['sometimes', 'string', 'max:50'],
        'asset_name' => ['sometimes', 'string', 'max:255'],
        'asset_type' => ['sometimes', 'string', 'max:100'],
        'region_id' => ['sometimes', 'integer', 'min:0'],
        'latitude' => ['nullable', 'numeric'],
        'longitude' => ['nullable', 'numeric'],
        'address' => ['sometimes', 'string', 'max:255'],
        'status' => ['sometimes', 'string'],
        'installation_date' => ['nullable', 'date'],
        'notes' => ['nullable', 'string'],
        'created_by' => ['nullable', 'integer', 'exists:users,id'],
    ];

    protected function filter(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('region_id'), fn ($q) => $q->where('region_id', $request->integer('region_id')))
            ->when($request->filled('asset_type'), fn ($q) => $q->where('asset_type', $request->asset_type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request): void {
                $q->where(function ($inner) use ($request): void {
                    $inner->where('asset_code', 'like', "%{$request->search}%")
                        ->orWhere('asset_name', 'like', "%{$request->search}%")
                        ->orWhere('address', 'like', "%{$request->search}%");
                });
            });
    }
}
