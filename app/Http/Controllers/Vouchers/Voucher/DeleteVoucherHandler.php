<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Response;

class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(string $id): Response
    {
        try {
            $deleted = $this->voucherService->deleteVoucherById($id);

            if (!$deleted) {
                return response([
                    'error' => 'Voucher no encontrado o no se pudo eliminar.',
                ], 404);
            }

            return response([
                'message' => "Eliminado correctamente",
            ], 200);

        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
