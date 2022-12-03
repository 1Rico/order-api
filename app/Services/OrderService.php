<?php

namespace App\Services;

use App\Exceptions\InvalidVoucherException;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;

final class OrderService
{
    public function createOrder(array $orderParams)
    {
        if (!empty($orderParams['voucher_code'])) {
            $voucher = Voucher::where('voucher_code', $orderParams['voucher_code'])->first();

            if (!$voucher || !$voucher->isValid()) {
                throw new InvalidVoucherException();
            }

            $originalOrderGrandTotal = $orderParams['grand_total'];

            $orderParams['grand_total'] = max($orderParams['grand_total'] - $voucher->discount_amount, 0);
            if ($orderParams['grand_total'] > 0) {
                $orderParams['discount_amount'] = $voucher->discount_amount;
            } else {
                $orderParams['discount_amount'] = $originalOrderGrandTotal;
            }

            $voucher->times_used++;
            $voucher->save();
        }

        $orderParams['order_nr'] = uniqid();
        return Auth::user()->orders()->create($orderParams);
    }

}
