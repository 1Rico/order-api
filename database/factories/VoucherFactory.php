<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_date'=> '2022-12-03T00:17:50.000000Z',
            'to_date'=> Carbon::now()->addHour(1)->toDateTimeString(),
            'discount_amount'=> random_int(1, 100),
            'voucher_code'=> uniqid(),
            'user_id'=> 1,
        ];
    }
}
