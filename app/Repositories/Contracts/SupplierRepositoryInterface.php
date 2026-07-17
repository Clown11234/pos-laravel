<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SupplierRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    // For  Dropdown list
    public function all(): Collection;
    // Create New Supplier
    public function create(array $data): Supplier;
    // Search with ID
    public function findById(int $id): Supplier;
    //Edit Supplier
    public function update(int $id, array $data): bool;
    // Delete Supplier
    public function delete(int $id): bool;


}
