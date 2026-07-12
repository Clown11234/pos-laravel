<?php

namespace App\Repositories\Contracts;
interface OrderRepositoryInterface
{
    public function getPaginated($perPage=15);
    public function getOrderDetails($id);
}
