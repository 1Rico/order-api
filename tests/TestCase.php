<?php

namespace Tests;

use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Voucher as VoucherResource;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createVoucherResource($options = []): VoucherResource
    {
        $voucher = Voucher::factory()->create($options);
        return new VoucherResource($voucher);
    }

    public function createOrderResource(): OrderResource
    {
        $voucher = Order::factory()->create();
        return new OrderResource($voucher);
    }

    public function createUser(): mixed
    {
        return User::factory()->create();
    }
}
