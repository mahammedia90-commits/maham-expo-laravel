<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Document::create(['name' => 'Sponsorship Contract', 'type' => 'contract', 'file_path' => '/uploads/contract_001.pdf', 'file_size' => '2048', 'mime_type' => 'application/pdf', 'status' => 'approved', 'uploaded_by' => 1, 'related_id' => 1, 'related_type' => 'sponsor']);
        \App\Models\Document::create(['name' => 'KYC Verification', 'type' => 'kyc', 'file_path' => '/uploads/kyc_002.pdf', 'file_size' => '1512', 'mime_type' => 'application/pdf', 'status' => 'pending', 'uploaded_by' => 1, 'related_id' => 2, 'related_type' => 'business_profile']);
        \App\Models\Document::create(['name' => 'Invoice #2026-001', 'type' => 'invoice', 'file_path' => '/uploads/inv_2026_001.pdf', 'file_size' => '890', 'mime_type' => 'application/pdf', 'status' => 'approved', 'uploaded_by' => 1, 'related_id' => 1, 'related_type' => 'invoice']);
    }
}
