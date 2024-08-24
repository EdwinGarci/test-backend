<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Http\Requests\Vouchers\GetVoucherRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class GetVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(string $id): Response
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['required', 'uuid'],
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        $voucher = $this->voucherService->getVoucherById($id);

        if (!$voucher) {
            return response([
                'error' => 'Voucher no encontrado.',
            ], 404);
        }

        return response([
            'data' => new VoucherResource($voucher),
        ], 200);
    }
}
