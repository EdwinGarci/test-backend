<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(
        int $page, 
        int $paginate, 
        ?string $series = null, 
        ?string $number = null, 
        ?string $startDate = null, 
        ?string $endDate = null): LengthAwarePaginator
    {
        $query = Voucher::with(['lines', 'user']);

        if ($series) {
            $query->where('series', $series);
        }

        if ($number) {
            $query->where('number', $number);
        }

        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        var_dump($query->toSql(), $query->getBindings());

        return $query->paginate(perPage: $paginate, page: $page);
    }

    public function getVoucherById(string $voucherId): Voucher
    {
        return Voucher::with(['lines', 'user'])->findOrFail($voucherId);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        // Serie - Número
        $voucherID = (string) $xml->xpath('//cbc:ID')[0];
        list($series, $number) = explode('-', $voucherID);

        // Tipo de comprobante
        $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];

        // Moneda
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'series' => $series,
            'number' => $number,
            'voucher_type' => $voucherType,
            'currency' => $currency,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    public function deleteVoucherById(string $id): bool
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return false;
        }

        return $voucher->delete();
    }

    public function getTotalAmounts(): array
    {
        return Voucher::selectRaw('currency, SUM(total_amount) as total')
                ->groupBy('currency')
                ->pluck('total', 'currency')
                ->toArray();
    }


}
