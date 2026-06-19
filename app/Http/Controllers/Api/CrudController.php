<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class CrudController extends Controller
{
    protected string $model;
    protected array $storeRules = [];
    protected array $updateRules = [];
    protected array $relations = [];

    public function index(Request $request)
    {
        $query = $this->newQuery();
        $this->filter($query, $request);

        return $query->latest('id')->get();
    }

    public function store(Request $request)
    {
        $data = $this->prepareData($request->validate($this->storeRules), $request);

        return response()->json($this->model::create($data), 201);
    }

    public function show(int $id)
    {
        return $this->newQuery()->findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $item = $this->model::findOrFail($id);
        $data = $this->prepareData($request->validate($this->updateRules), $request, $item);
        $item->update($data);

        return $item->fresh();
    }

    public function destroy(int $id)
    {
        $this->model::findOrFail($id)->delete();

        return response()->noContent();
    }

    protected function newQuery(): Builder
    {
        return $this->model::query()->with($this->relations);
    }

    protected function filter(Builder $query, Request $request): void
    {
        //
    }

    protected function prepareData(array $data, Request $request, ?Model $model = null): array
    {
        return $data;
    }
}
