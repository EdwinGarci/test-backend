<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Response;

/**
 * @OA\Get(
 *     path="/api/v1/vouchers",
 *     tags={"Vouchers"},
 *     summary="Obtener lista de vouchers",
 *     description="Recupera una lista de vouchers con posibilidad de paginación y filtros opcionales.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", example=1),
 *         description="Número de página para la paginación"
 *     ),
 *     @OA\Parameter(
 *         name="paginate",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", example=10),
 *         description="Cantidad de elementos por página"
 *     ),
 *     @OA\Parameter(
 *         name="series",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", example="A001"),
 *         description="Serie del voucher para filtrar"
 *     ),
 *     @OA\Parameter(
 *         name="number",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", example="123456"),
 *         description="Número del voucher para filtrar"
 *     ),
 *     @OA\Parameter(
 *         name="startDate",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2024-01-01"),
 *         description="Fecha de inicio para filtrar (formato YYYY-MM-DD)"
 *     ),
 *     @OA\Parameter(
 *         name="endDate",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2024-12-31"),
 *         description="Fecha de fin para filtrar (formato YYYY-MM-DD)"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de vouchers obtenida exitosamente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/VoucherResource")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No se encontraron vouchers con los parámetros especificados."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor."
 *     ),
 *     @OA\Tag(name="Vouchers")
 * )
 */
class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): Response
    {
        $vouchers = $this->voucherService->getVouchers(
            $request->query('page'),
            $request->query('paginate'),
            $request->query('series'),                    
            $request->query('number'),        
            $request->query('startDate'),
            $request->query('endDate')
        );

        return response([
            'data' => VoucherResource::collection($vouchers),
        ], 200);
    }
}
