<?php
return [
    'api_url' => env('ZATCA_API_URL', 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal'),
    'compliance_csid' => env('ZATCA_COMPLIANCE_CSID', ''),
    'production_csid' => env('ZATCA_PRODUCTION_CSID', ''),
    'production' => env('ZATCA_PRODUCTION', false),
    'seller_name' => env('ZATCA_SELLER_NAME', 'Maham Expo'),
    'vat_number' => env('ZATCA_VAT_NUMBER', '300012345600003'),
];
