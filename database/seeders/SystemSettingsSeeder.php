<?php
// database/seeders/SystemSettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
use Carbon\Carbon;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // ID Assessment Settings
            [
                'group' => 'id_assessment',
                'key' => 'prefix',
                'value' => 'ASM',
                'description' => 'Prefix untuk ID Assessment'
            ],
            [
                'group' => 'id_assessment',
                'key' => 'suffix',
                'value' => Carbon::now()->format('Y'),
                'description' => 'Suffix untuk ID Assessment (biasanya tahun)'
            ],
            [
                'group' => 'id_assessment',
                'key' => 'running_length',
                'value' => '4',
                'description' => 'Panjang digit nomor urut'
            ],
            [
                'group' => 'id_assessment',
                'key' => 'separator',
                'value' => '/',
                'description' => 'Karakter pemisah antar bagian ID'
            ],
            [
                'group' => 'id_assessment',
                'key' => 'reset_period',
                'value' => 'yearly',
                'description' => 'Periode reset nomor urut (never, yearly, monthly)'
            ],
            [
                'group' => 'id_assessment',
                'key' => 'current_number',
                'value' => '1',
                'description' => 'Nomor urut saat ini'
            ],
            [
                'group' => 'id_assessment',
                'key' => 'per_skema_enabled',
                'value' => '0',
                'description' => 'Apakah menggunakan format ID berbeda per skema'
            ],

            // Document Settings
            [
                'group' => 'document',
                'key' => 'kop_surat',
                'value' => 'LEMBAGA SERTIFIKASI PROFESI PASAR MODAL',
                'description' => 'Nama lembaga untuk kop surat'
            ],
            [
                'group' => 'document',
                'key' => 'alamat_lsp',
                'value' => 'Jl. Sudirman Kav. 76-78, Jakarta 12910',
                'description' => 'Alamat lengkap LSP'
            ],
            [
                'group' => 'document',
                'key' => 'telepon_lsp',
                'value' => '021-5299-0000',
                'description' => 'Nomor telepon LSP'
            ],
            [
                'group' => 'document',
                'key' => 'email_lsp',
                'value' => 'info@lsp-pm.com',
                'description' => 'Email resmi LSP'
            ],
            [
                'group' => 'document',
                'key' => 'ketua_lsp',
                'value' => 'Dr. Budi Hartono, CFA',
                'description' => 'Nama Ketua LSP'
            ],
            [
                'group' => 'document',
                'key' => 'sekretaris_lsp',
                'value' => 'Dra. Siti Nurhaliza, MM',
                'description' => 'Nama Sekretaris LSP'
            ],
            [
                'group' => 'document',
                'key' => 'enable_digital_signature',
                'value' => '0',
                'description' => 'Aktifkan tanda tangan digital'
            ],
            [
                'group' => 'document',
                'key' => 'signature_position',
                'value' => 'bottom-center',
                'description' => 'Posisi tanda tangan (bottom-left, bottom-center, bottom-right)'
            ],
            [
                'group' => 'document',
                'key' => 'signature_size',
                'value' => 'medium',
                'description' => 'Ukuran tanda tangan (small, medium, large)'
            ],
            [
                'group' => 'document',
                'key' => 'pdf_format',
                'value' => 'A4',
                'description' => 'Format kertas PDF (A4, Letter, Legal)'
            ],
            [
                'group' => 'document',
                'key' => 'pdf_orientation',
                'value' => 'portrait',
                'description' => 'Orientasi PDF (portrait, landscape)'
            ],
            [
                'group' => 'document',
                'key' => 'include_watermark',
                'value' => '1',
                'description' => 'Sertakan watermark pada dokumen'
            ],
            [
                'group' => 'document',
                'key' => 'auto_generate',
                'value' => '1',
                'description' => 'Generate dokumen otomatis setelah assessment'
            ],

            // System Info Settings
            [
                'group' => 'system',
                'key' => 'app_name',
                'value' => 'LSP Pasar Modal',
                'description' => 'Nama aplikasi'
            ],
            [
                'group' => 'system',
                'key' => 'app_version',
                'value' => '1.0.0',
                'description' => 'Versi aplikasi'
            ],
            [
                'group' => 'system',
                'key' => 'maintenance_mode',
                'value' => '0',
                'description' => 'Mode maintenance (0 = aktif, 1 = maintenance)'
            ],
            [
                'group' => 'system',
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'description' => 'Timezone aplikasi'
            ]
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                [
                    'group' => $setting['group'],
                    'key' => $setting['key']
                ],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description']
                ]
            );
        }

        $this->command->info('System settings seeded successfully!');
    }
}