<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserController extends CrudController
{
    protected string $model = User::class;

    protected array $storeRules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:6'],
        'role' => ['required', 'string'],
        'phone' => ['nullable', 'string', 'max:30'],
        'region_id' => ['required', 'integer', 'min:0'],
        'is_active' => ['boolean'],
    ];

    protected array $updateRules = [
        'name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'email', 'max:255'],
        'password' => ['sometimes', 'nullable', 'string', 'min:6'],
        'role' => ['sometimes', 'string'],
        'phone' => ['nullable', 'string', 'max:30'],
        'region_id' => ['sometimes', 'integer', 'min:0'],
        'is_active' => ['sometimes', 'boolean'],
    ];

    protected function filter(Builder $query, Request $request): void
    {
        $query
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN)))
            ->when($request->filled('search'), function ($q) use ($request): void {
                $q->where(function ($inner) use ($request): void {
                    $inner->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%");
                });
            });
    }

    protected function prepareData(array $data, Request $request, ?Model $model = null): array
    {
        if (($data['password'] ?? null) === null || ($data['password'] ?? '') === '') {
            unset($data['password']);
        }

        return $data;
    }
}
