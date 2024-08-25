<?php

namespace App\Http\Resources\Vouchers;

use App\Http\Resources\Users\UserResource;
use App\Http\Resources\VoucherLines\VoucherLineResource;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="VoucherResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="issuer_name", type="string", example="Empresa XYZ"),
 *     @OA\Property(property="issuer_document_type", type="string", example="RUC"),
 *     @OA\Property(property="issuer_document_number", type="string", example="12345678901"),
 *     @OA\Property(property="receiver_name", type="string", example="Juan Pérez"),
 *     @OA\Property(property="receiver_document_type", type="string", example="DNI"),
 *     @OA\Property(property="receiver_document_number", type="string", example="98765432"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=1000.00),
 *     @OA\Property(property="series", type="string", example="A001"),
 *     @OA\Property(property="number", type="string", example="123456"),
 *     @OA\Property(property="voucher_type", type="string", example="Factura"),
 *     @OA\Property(property="currency", type="string", example="USD"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="lines", type="array",
 *         @OA\Items(ref="#/components/schemas/VoucherLineResource")
 *     ),
 * )
 */
class VoucherResource extends JsonResource
{
    /**
     * @var Voucher
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'issuer_name' => $this->resource->issuer_name,
            'issuer_document_type' => $this->resource->issuer_document_type,
            'issuer_document_number' => $this->resource->issuer_document_number,
            'receiver_name' => $this->resource->receiver_name,
            'receiver_document_type' => $this->resource->receiver_document_type,
            'receiver_document_number' => $this->resource->receiver_document_number,
            'total_amount' => $this->resource->total_amount,
            'series' => $this->resource->series,
            'number' => $this->resource->number,
            'voucher_type' => $this->resource->voucher_type,
            'currency' => $this->resource->currency,
            'user' => $this->whenLoaded(
                'user',
                fn () => UserResource::make($this->resource->user),
            ),
            'lines' => $this->whenLoaded(
                'lines',
                fn () => VoucherLineResource::collection($this->resource->lines),
            ),
        ];
    }
}
