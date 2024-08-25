<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Jobs\ProcessVouchersUpload;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Post(
 *     path="/api/v1/vouchers",
 *     tags={"Vouchers"},
 *     summary="Sube y procesa archivos XML de vouchers",
 *     description="Permite al usuario subir archivos XML con información de vouchers para su procesamiento. Los archivos se procesan en segundo plano.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="files",
 *                     type="array",
 *                     @OA\Items(type="string", format="binary")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=202,
 *         description="Los vouchers se están procesando.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Los vouchers se están procesando.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error en el procesamiento de los archivos XML.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Error en el procesamiento de los archivos XML.")
 *         )
 *     ),
 *     @OA\Tag(name="Vouchers")
 * )
 */
class StoreVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $xmlFiles = $request->file('files');

            if (!is_array($xmlFiles)) {
                $xmlFiles = [$xmlFiles];
            }

            $xmlContents = [];
            foreach ($xmlFiles as $xmlFile) {
                $xmlContents[] = file_get_contents($xmlFile->getRealPath());
            }

            $user = auth()->user();

            ProcessVouchersUpload::dispatch($xmlContents, $user);

            return response([
                'message' => 'Los vouchers se están procesando.',
            ], 202);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
