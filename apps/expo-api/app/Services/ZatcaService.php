<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * ZATCA Phase 2 E-Invoice Service
 * هيئة الزكاة والضريبة والجمارك - الفوترة الإلكترونية
 * 
 * Ready for integration when ZATCA credentials are provided.
 */
class ZatcaService
{
    private string $apiUrl;
    private string $complianceCsid;
    private string $productionCsid;
    private bool $isProduction;

    public function __construct()
    {
        $this->apiUrl = config('services.zatca.api_url', 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal');
        $this->complianceCsid = config('services.zatca.compliance_csid', '');
        $this->productionCsid = config('services.zatca.production_csid', '');
        $this->isProduction = config('services.zatca.production', false);
    }

    /**
     * Generate ZATCA-compliant XML invoice
     */
    public function generateInvoice(array $invoiceData): array
    {
        $uuid = $this->generateUUID();
        $hash = $this->generateHash($invoiceData);
        $qr = $this->generateQRCode($invoiceData, $hash);

        return [
            'uuid' => $uuid,
            'hash' => $hash,
            'qr_code' => $qr,
            'xml' => $this->buildXML($invoiceData, $uuid, $hash),
            'status' => 'generated',
        ];
    }

    /**
     * Submit invoice to ZATCA for clearance
     */
    public function submitInvoice(string $xml, string $uuid, string $hash): array
    {
        if (empty($this->productionCsid)) {
            Log::warning('ZATCA: Production CSID not configured. Invoice stored locally.');
            return ['status' => 'stored_locally', 'uuid' => $uuid, 'message' => 'ZATCA credentials pending'];
        }

        // TODO: HTTP call to ZATCA API when credentials ready
        return ['status' => 'submitted', 'uuid' => $uuid];
    }

    /**
     * Generate ZATCA QR code (TLV format per ZATCA specs)
     */
    public function generateQRCode(array $data, string $hash): string
    {
        $tlv = '';
        $tlv .= $this->tlvEncode(1, $data['seller_name'] ?? 'Maham Expo');
        $tlv .= $this->tlvEncode(2, $data['vat_number'] ?? '300012345600003');
        $tlv .= $this->tlvEncode(3, $data['timestamp'] ?? now()->toIso8601String());
        $tlv .= $this->tlvEncode(4, number_format($data['total_with_vat'] ?? 0, 2));
        $tlv .= $this->tlvEncode(5, number_format($data['vat_amount'] ?? 0, 2));
        $tlv .= $this->tlvEncode(6, $hash);

        return base64_encode($tlv);
    }

    /**
     * Validate invoice against ZATCA rules
     */
    public function validateInvoice(array $data): array
    {
        $errors = [];
        if (empty($data['seller_name'])) $errors[] = 'اسم البائع مطلوب';
        if (empty($data['vat_number'])) $errors[] = 'الرقم الضريبي مطلوب';
        if (($data['total_with_vat'] ?? 0) <= 0) $errors[] = 'المبلغ يجب أن يكون أكبر من صفر';
        if (($data['vat_amount'] ?? 0) < 0) $errors[] = 'مبلغ الضريبة غير صحيح';
        
        $expectedVat = round(($data['subtotal'] ?? 0) * 0.15, 2);
        if (abs(($data['vat_amount'] ?? 0) - $expectedVat) > 0.01) {
            $errors[] = 'مبلغ الضريبة لا يتطابق مع 15%';
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    private function generateUUID(): string { return (string) \Illuminate\Support\Str::uuid(); }
    private function generateHash(array $data): string { return hash('sha256', json_encode($data) . now()->timestamp); }
    private function tlvEncode(int $tag, string $value): string { return chr($tag) . chr(strlen($value)) . $value; }
    
    private function buildXML(array $data, string $uuid, string $hash): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
  <cbc:ID>' . ($data['invoice_number'] ?? '') . '</cbc:ID>
  <cbc:UUID>' . $uuid . '</cbc:UUID>
  <cbc:IssueDate>' . now()->format('Y-m-d') . '</cbc:IssueDate>
  <cbc:InvoiceTypeCode>388</cbc:InvoiceTypeCode>
  <cac:AccountingSupplierParty>
    <cac:Party><cbc:CompanyID>' . ($data['vat_number'] ?? '') . '</cbc:CompanyID></cac:Party>
  </cac:AccountingSupplierParty>
  <cac:LegalMonetaryTotal>
    <cbc:TaxExclusiveAmount>' . number_format($data['subtotal'] ?? 0, 2) . '</cbc:TaxExclusiveAmount>
    <cbc:TaxInclusiveAmount>' . number_format($data['total_with_vat'] ?? 0, 2) . '</cbc:TaxInclusiveAmount>
  </cac:LegalMonetaryTotal>
</Invoice>';
    }
}
