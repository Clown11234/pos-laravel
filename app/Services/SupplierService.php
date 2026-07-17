<?php

namespace App\Services;

use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SupplierService
{
    protected SupplierRepositoryInterface $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function getPaginatedSuppliers(int $perPage = 10): LengthAwarePaginator
    {
        return $this->supplierRepository->paginate($perPage);
    }

    public function getAllSuppliers(): Collection
    {
        return $this->supplierRepository->all();
    }

    public function createSupplier(array $data): Supplier
    {
        // Beginner Logic: အသစ်ဆောက်ချိန်တွင် due_amount မပါလျှင် 0 အလိုအလျောက်ထားမည်
        $data['due_amount'] = $data['due_amount'] ?? 0.00;
        return $this->supplierRepository->create($data);
    }

    public function getSupplierById(int $id): Supplier
    {
        return $this->supplierRepository->findById($id);
    }

    public function updateSupplier(int $id, array $data): bool
    {
        return $this->supplierRepository->update($id, $data);
    }

    public function deleteSupplier(int $id): bool
    {
        return $this->supplierRepository->delete($id);
    }

    // အကြွေးတွက်မယ်
    public function updateDueAmount(int $id, float $amount, string $type = 'add'): bool
    {
        $supplier = $this->supplierRepository->findById($id);

        if ($type === 'add') {
            $supplier->due_amount += $amount;
        } elseif ($type === 'deduct') {
            $supplier->due_amount -= $amount;
        }

        return $supplier->save();
    }
}
