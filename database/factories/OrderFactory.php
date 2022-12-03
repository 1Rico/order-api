<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grand_total'=> random_int(1, 10),
            'order_nr'=> '638bb55d30109',
            'voucher_code'=> uniqid(),
            'user_id'=> 1,
        ];
    }
}
