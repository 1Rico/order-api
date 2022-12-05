<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_order_will_fail_with_404_if_order_not_found(): void
    {
        $response = $this->actingAs($this->createUser(), 'sanctum')->json('GET', '/api/orders/-1');
        $response->assertStatus(404);
    }

    public function test_get_order_will_fail_with_401_if_user_not_logged_in(): void
    {
        $response = $this->json('GET', '/api/orders/1');
        $response->assertStatus(401);
    }

    public function test_can_create_an_order_without_voucher(): void
    {
        $authUser = $this->createUser();
        $response = $this->actingAs($authUser, 'sanctum')->json('POST', '/api/orders', [
            'grand_total' => $price = random_int(10, 100),
        ]);

        $response
            ->assertJsonStructure([
                'data' => [
                    'grand_total', 'order_nr', 'user_id', 'updated_at', 'created_at', 'id'
                ]
            ])
            ->assertJson([
                'data' => [
                    'grand_total' => $price,
                    'user_id' => $authUser->id,
                ]
            ])
            ->assertStatus(201);
    }

    public function test_can_create_an_order_with_voucher(): void
    {
        $authUser = $this->createUser();
        $voucher = $this->createVoucherResource($this->expiredVoucherData());

        $response = $this->actingAs($authUser, 'sanctum')->json('POST', '/api/orders', [
            'grand_total' => $price = random_int(100, 1000),
            'voucher_code' => $voucher->voucher_code,
        ]);

        $response
            ->assertJson(['message' => 'Voucher does not exist or is not a valid voucher'])
            ->assertStatus(400);
    }

    public function test_can_get_an_order(): void
    {
        $authUser = $this->createUser();
        $order = $this->createOrderResource();

        $response = $this->actingAs($authUser, 'sanctum')->json('GET', "/api/orders/$order->id");
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => $order->id,
                    'grand_total' => $order->grand_total,
                    'discount_amount' => $order->discount_amount,
                    'voucher_code' => $order->voucher_code,
                    'order_nr' => $order->order_nr,
                    'user_id' => $order->user_id,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at
                ]
            ]);
    }

    private function expiredVoucherData(): array
    {
        return [
            'from_date' => '2022-12-03T00:17:50.000000Z',
            'to_date' => Carbon::now()->addHour(-1)->toDateTimeString(),
            'discount_amount' => random_int(1, 100),
            'voucher_code' => uniqid(),
            'user_id' => 1,
        ];
    }
}
