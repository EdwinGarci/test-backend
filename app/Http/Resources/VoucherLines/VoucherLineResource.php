<?php

namespace App\Http\Resources\VoucherLines;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\VoucherLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="VoucherLineResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Producto A"),
 *     @OA\Property(property="quantity", type="number", format="float", example=10),
 *     @OA\Property(property="unit_price", type="number", format="float", example=50.00),
 *     @OA\Property(property="voucher", ref="#/components/schemas/VoucherResource"),
 * )
 */
class VoucherLineResource extends JsonResource
{
    /**
     * @var VoucherLine
     */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'quantity' => $this->resource->quantity,
            'unit_price' => $this->resource->unit_price,
            'voucher' => $this->whenLoaded(
                'voucher',
                fn () => VoucherResource::make($this->resource->voucher),
            ),
        ];
    }
}
