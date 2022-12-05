<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class VoucherControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_voucher_will_fail_with_404_if_voucher_not_found(): void
    {
        $response = $this->actingAs($this->createUser(), 'sanctum')->json('GET', '/api/vouchers/-1');
        $response->assertStatus(404);
    }

    public function test_update_voucher_will_fail_with_404_if_voucher_not_found(): void
    {
        $response = $this->actingAs($this->createUser(), 'sanctum')->json('PUT', '/api/vouchers/-1');
        $response->assertStatus(404);
    }

    public function test_update_voucher_will_fail_with_401_if_user_not_logged_in(): void
    {
        $response = $this->json('PUT', '/api/vouchers/1');
        $response->assertStatus(401);
    }

    public function test_can_create_a_voucher(): void
    {
        $response = $this->actingAs($this->createUser(), 'sanctum')->json('POST', '/api/vouchers', [
            'discount_amount' => $price = random_int(10, 100),
            'to_date' => '2022-12-03T00:17:50.000000Z'
        ]);

        $response
            ->assertJsonStructure([
                'data' => [
                    'to_date', 'discount_amount', 'voucher_code', 'updated_at', 'created_at', 'id'
                ]
            ])
            ->assertJson([
                'data' => [
                    'discount_amount' => $price,
                    'to_date' => '2022-12-03T00:17:50.000000Z'
                ]
            ])
            ->assertStatus(201);
    }

    public function test_can_update_a_voucher(): void
    {
        $authUser = $this->createUser();
        $voucher = $this->createVoucherResource();

        $response = $this->actingAs($authUser, 'sanctum')->json('PUT', "/api/vouchers/$voucher->id", [
            'voucher_code' => $voucher->voucher_code . '_updated',
            'discount_amount' => $voucher->discount_amount + 10,
            'from_date' => $voucher->from_date,
            'to_date' => $voucher->to_date
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('vouchers', [
            'voucher_code' => $voucher->voucher_code . '_updated',
            'discount_amount' => $voucher->discount_amount + 10,
            'from_date' => $voucher->from_date,
            'to_date' => $voucher->to_date,
        ]);
    }

    public function test_can_delete_a_voucher(): void
    {
        $authUser = $this->createUser();
        $voucher = $this->createVoucherResource();

        $response = $this->actingAs($authUser, 'sanctum')->json('DELETE', "/api/vouchers/$voucher->id");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('vouchers', [
            'id' => $voucher->id,
        ]);
    }

    public function test_can_get_a_voucher(): void
    {
        $authUser = $this->createUser();
        $voucher = $this->createVoucherResource();

        $response = $this->actingAs($authUser, 'sanctum')->json('GET', "/api/vouchers/$voucher->id");
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => $voucher->id,
                    'voucher_code' => $voucher->voucher_code,
                    'discount_amount' => $voucher->discount_amount,
                    'times_used' => (int)$voucher->times_used,
                    'to_date' => $voucher->to_date,
                    'from_date' => $voucher->from_date,
                    'user_id' => $voucher->user_id,
                    'created_at' => $voucher->created_at,
                    'updated_at' => $voucher->updated_at
                ]
            ]);
    }
}
