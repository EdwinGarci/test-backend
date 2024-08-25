<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Response;

/**
 * @OA\Delete(
 *     path="/api/v1/vouchers/{id}",
 *     tags={"Vouchers"},
 *     summary="Elimina un voucher por ID",
 *     description="Permite eliminar un voucher específico utilizando su ID.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del voucher a eliminar.",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Voucher eliminado correctamente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Eliminado correctamente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Voucher no encontrado o no se pudo eliminar.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Voucher no encontrado o no se pudo eliminar.")
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
