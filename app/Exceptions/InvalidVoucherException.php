<?php

namespace App\Exceptions;

use InvalidArgumentException;

class InvalidVoucherException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct("Voucher does not exist or is not a valid voucher");
    }
}
