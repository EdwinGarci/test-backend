<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Services\VoucherService;
use Illuminate\Http\Response;

/**
 * @OA\Get(
 *     path="/api/v1/vouchers/total-amounts",
 *     tags={"Vouchers"},
 *     summary="Obtiene los montos totales de los vouchers",
 *     description="Retorna los montos totales agrupados por moneda para todos los vouchers.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Montos totales obtenidos exitosamente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object", additionalProperties=true,
 *                 @OA\Property(property="USD", type="number", format="float", example=1234.56),
 *                 @OA\Property(property="EUR", type="number", format="float", example=789.01)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Error interno del servidor.")
 *         )
 *     ),
 *     @OA\Tag(name="Vouchers")
 * )
 */
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
