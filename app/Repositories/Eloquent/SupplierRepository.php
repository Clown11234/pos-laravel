<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
class SupplierRepository implements SupplierRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Supplier::latest()->paginate($perPage);
    }

    // For DP
    public function all(): Collection
    {
        return Supplier::all();
    }

    // Create New Supplier
    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }
    // Search with ID
    public function findById(int $id): Supplier
    {
        return Supplier::findOrFail($id);
    }
    // Edit Supplier
    public function update(int $id, array $data): bool
    {
        $supplier = $this->findById($id);
        return $supplier->update($data);
    }

    // Delete Supplier
    public function delete(int $id): bool
    {
        $supplier = $this->findById($id);
        return $supplier->delete();
    }
}
