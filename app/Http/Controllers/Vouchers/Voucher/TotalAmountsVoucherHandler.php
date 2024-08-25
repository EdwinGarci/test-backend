<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Services\VoucherService;
use Illuminate\Http\Response;

class TotalAmountsVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(): Response
    {
        $totals = $this->voucherService->getTotalAmounts();

        return response([
            'data' => $totals,
        ], 200);
    }
}
