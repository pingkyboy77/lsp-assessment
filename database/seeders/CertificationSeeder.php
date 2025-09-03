<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\CertificationScheme;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data lengkap dari CSV - setiap row jadi field dengan code_2 unique
        $data = [
            // Manajemen Risiko
            ['nama' => 'Pengembangan Sistem Manajemen Risiko', 'code_1' => 'CRP', 'kode_bidang' => '2421', 'code_2' => 'A1', 'fee_tanda_tangan' => 25000, 'bidang' => 'Manajemen Risiko', 'bidang_ing' => 'Risk Management', 'skema_ing' => 'Certified Risk Professional', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pengelolaan Manajemen Risiko', 'code_1' => 'CRA', 'kode_bidang' => '2421', 'code_2' => 'A2', 'fee_tanda_tangan' => 12500, 'bidang' => 'Manajemen Risiko', 'bidang_ing' => 'Risk Management', 'skema_ing' => 'Certified Risk Associate', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pelaksanaan Manajemen Risiko', 'code_1' => 'CRO', 'kode_bidang' => '2421', 'code_2' => 'A3', 'fee_tanda_tangan' => 12500, 'bidang' => 'Manajemen Risiko', 'bidang_ing' => 'Risk Management', 'skema_ing' => 'Affiliate Risk Manager', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            
            // Analisis Efek
            ['nama' => 'Pengelolaan Analisis Efek', 'code_1' => 'CSA', 'kode_bidang' => '2413', 'code_2' => 'B1', 'fee_tanda_tangan' => 25000, 'bidang' => 'Analisis Efek', 'bidang_ing' => 'Securities Analysis', 'skema_ing' => 'Certified Securities Analyst', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pelaksanaan Analisis Efek', 'code_1' => 'RSA', 'kode_bidang' => '2413', 'code_2' => 'B2', 'fee_tanda_tangan' => 12500, 'bidang' => 'Analisis Efek', 'bidang_ing' => 'Securities Analysis', 'skema_ing' => 'Regular Securities Analyst', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pengenalan Analisis Efek', 'code_1' => 'RSO', 'kode_bidang' => '2413', 'code_2' => 'B3', 'fee_tanda_tangan' => 12500, 'bidang' => 'Analisis Efek', 'bidang_ing' => 'Securities Analysis', 'skema_ing' => 'Affiliate Securities Analyst', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            
            // Investment Banking
            ['nama' => 'Pengelolaan Bankir Investasi', 'code_1' => 'CIB', 'kode_bidang' => '2413', 'code_2' => 'C1', 'fee_tanda_tangan' => 25000, 'bidang' => 'Investment Banking', 'bidang_ing' => 'Investment Banking', 'skema_ing' => 'Certified Investment Banker', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pelaksanaan Bankir Investasi', 'code_1' => 'RIB', 'kode_bidang' => '2413', 'code_2' => 'C2', 'fee_tanda_tangan' => 12500, 'bidang' => 'Investment Banking', 'bidang_ing' => 'Investment Banking', 'skema_ing' => 'Registered Investment Banker', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            
            // Analisis Teknikal
            ['nama' => 'Pengembangan Analisis Teknikal', 'code_1' => 'CTAD', 'kode_bidang' => '2413', 'code_2' => 'D1', 'fee_tanda_tangan' => 25000, 'bidang' => 'Analisis Teknikal', 'bidang_ing' => 'Technical Analysis', 'skema_ing' => 'Certified Technical Analysis Developer', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pengelolaan Analisis Teknikal', 'code_1' => 'CTA', 'kode_bidang' => '2413', 'code_2' => 'D2', 'fee_tanda_tangan' => 25000, 'bidang' => 'Analisis Teknikal', 'bidang_ing' => 'Technical Analysis', 'skema_ing' => 'Certified Technical Analyst', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pelaksanaan Analisis Teknikal', 'code_1' => 'RTA', 'kode_bidang' => '2413', 'code_2' => 'D3', 'fee_tanda_tangan' => 12500, 'bidang' => 'Analisis Teknikal', 'bidang_ing' => 'Technical Analysis', 'skema_ing' => 'Regular Technical Analyst', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            
            // Analisis Pendapatan Tetap
            ['nama' => 'Pelaksanaan Analisis Pendapatan Tetap', 'code_1' => 'RFIA', 'kode_bidang' => '2413', 'code_2' => 'E1', 'fee_tanda_tangan' => 12500, 'bidang' => 'Analisis Pendapatan Tetap', 'bidang_ing' => 'Fixed Income Analysis', 'skema_ing' => 'Regular Fixed Income Analyst', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pengelolaan Analisis Pendapatan Tetap', 'code_1' => 'CFIA', 'kode_bidang' => '2413', 'code_2' => 'E2', 'fee_tanda_tangan' => 25000, 'bidang' => 'Analisis Pendapatan Tetap', 'bidang_ing' => 'Fixed Income Analysis', 'skema_ing' => 'Certified Fixed Income Analyst', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            
            // Pasar Modal
            ['nama' => 'Jenjang Kualifikasi 5 Bidang Pasar Modal Subbidang Penjaminan Emisi Efek', 'code_1' => 'WPEE', 'kode_bidang' => '2412', 'code_2' => 'F', 'fee_tanda_tangan' => 25000, 'bidang' => 'Penjaminan Emisi Efek', 'bidang_ing' => 'Securities Issuance', 'skema_ing' => 'Qualification Level 5, Capital Markets Sector, Securities Issuance Subsector', 'jenjang' => 'Utama', 'kbbli_bidang' => '66141', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 5 Bidang Pasar Modal Subbidang Perantara Pedagang Efek', 'code_1' => 'WPPE', 'kode_bidang' => '2412', 'code_2' => 'G', 'fee_tanda_tangan' => 25000, 'bidang' => 'Perantara Pedagang Efek', 'bidang_ing' => 'Securites Broker-dealer', 'skema_ing' => 'Qualification Level 5, Capital Markets Sector, Securites Broker-dealer Subsector', 'jenjang' => 'Utama', 'kbbli_bidang' => '66142', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 4 Bidang Pasar Modal Subbidang Perantara Pedagang Efek Pemasaran', 'code_1' => 'WPPE P', 'kode_bidang' => '2412', 'code_2' => 'H', 'fee_tanda_tangan' => 17500, 'bidang' => 'Perantara Pedagang Efek', 'bidang_ing' => 'Securites Broker-dealer', 'skema_ing' => 'Qualification Level 4, Capital Markets Sector, Marketing Securities Broker-dealer Subsector', 'jenjang' => 'Menengah', 'kbbli_bidang' => '66142', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 4 Bidang Pasar Modal Subbidang Perantara Pedagang Efek Pemasaran Efek Bersifat Utang dan/atau Sukuk', 'code_1' => 'WPPE P EBUS', 'kode_bidang' => '2412', 'code_2' => 'I', 'fee_tanda_tangan' => 17500, 'bidang' => 'Perantara Pedagang Efek', 'bidang_ing' => 'Securites Broker-dealer', 'skema_ing' => 'Qualification Level 4, Capital Markets Sector, Debt Securities and/or Sukuk Marketing Broker-dealer Subsector', 'jenjang' => 'Menengah', 'kbbli_bidang' => '66142', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 4 Bidang Pasar Modal Subbidang Perantara Pedagang Efek Pemasaran Ekuitas', 'code_1' => 'WPPE EKUITAS', 'kode_bidang' => '2412', 'code_2' => 'J', 'fee_tanda_tangan' => 17500, 'bidang' => 'Perantara Pedagang Efek', 'bidang_ing' => 'Securites Broker-dealer', 'skema_ing' => 'Qualification Level 4, Capital Markets Sector, Equity Securities Marketing Broker-dealers Subsector', 'jenjang' => 'Menengah', 'kbbli_bidang' => '66142', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 3 Bidang Pasar Modal Subbidang Perantara Pedagang Efek Pemasaran Terbatas', 'code_1' => 'WPPE PT', 'kode_bidang' => '2412', 'code_2' => 'K', 'fee_tanda_tangan' => 12500, 'bidang' => 'Perantara Pedagang Efek', 'bidang_ing' => 'Securites Broker-dealer', 'skema_ing' => 'Qualification Level 3, Capital Markets Sector, Limited Marketing Securities Broker-dealer Subsector', 'jenjang' => 'Madya', 'kbbli_bidang' => '66142', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 4 Bidang Pasar Modal Subbidang Penjualan Efek Reksa Dana', 'code_1' => 'WAPERD', 'kode_bidang' => '2412', 'code_2' => 'L', 'fee_tanda_tangan' => 12500, 'bidang' => 'Penjual Efek Reksa Dana', 'bidang_ing' => 'Mutual Funds Salespeople', 'skema_ing' => 'Qualification Level 4, Capital Markets Sector, Mutual Funds Salespeople Subsector', 'jenjang' => 'Madya', 'kbbli_bidang' => '66146', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 5 Bidang Pasar Modal Subbidang Pengelolaan Investasi', 'code_1' => 'WMI', 'kode_bidang' => '2412', 'code_2' => 'M', 'fee_tanda_tangan' => 25000, 'bidang' => 'Pengelolaan Investasi', 'bidang_ing' => 'Investment Management', 'skema_ing' => 'Qualification Level 5, Capital Markets Sector, Investment Management Subsector', 'jenjang' => 'Utama', 'kbbli_bidang' => '66311', 'kode_web' => 'BPM'],
            ['nama' => 'Jenjang Kualifikasi 5 Bidang Pasar Modal Subbidang Manajemen Risiko', 'code_1' => 'KADIV MANRISK', 'kode_bidang' => '2421', 'code_2' => 'N', 'fee_tanda_tangan' => 25000, 'bidang' => 'Manajemen Risiko', 'bidang_ing' => 'Risk Management', 'skema_ing' => 'Qualification Level 5, Capital Markets Sector, Risk Management Subsector', 'jenjang' => 'Utama', 'kbbli_bidang' => '66142', 'kode_web' => 'BPM'],
            
            // Skema tanpa bidang spesifik
            ['nama' => 'Kepatuhan Utama', 'code_1' => 'SC', 'kode_bidang' => '2413', 'code_2' => 'O', 'fee_tanda_tangan' => 25000, 'bidang' => null, 'bidang_ing' => null, 'skema_ing' => null, 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Manajemen Portofolio Utama', 'code_1' => 'MPU', 'kode_bidang' => '2413', 'code_2' => 'P', 'fee_tanda_tangan' => 25000, 'bidang' => null, 'bidang_ing' => null, 'skema_ing' => null, 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            
            // Perdagangan Efek
            ['nama' => 'Pelaksanaan Pemasaran Ekuitas Digital', 'code_1' => 'RES', 'kode_bidang' => '2412', 'code_2' => 'Q1', 'fee_tanda_tangan' => 12500, 'bidang' => 'Perdagangan Efek', 'bidang_ing' => 'Equity Sales', 'skema_ing' => 'Regular Equity Sales Representative', 'jenjang' => 'Madya', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
            ['nama' => 'Pengelolaan Pemasaran Ekuitas Digital', 'code_1' => 'CES', 'kode_bidang' => '2412', 'code_2' => 'Q', 'fee_tanda_tangan' => 25000, 'bidang' => 'Perdagangan Efek', 'bidang_ing' => 'Equity Sales', 'skema_ing' => 'Certified Equity Sales Professional', 'jenjang' => 'Utama', 'kbbli_bidang' => '66123', 'kode_web' => 'KEU'],
        ];

        // Insert data - setiap row jadi field dengan code_2 unique
        foreach ($data as $item) {
            // Create field entry
            Field::updateOrCreate(
                ['code_2' => $item['code_2']],
                [
                    'kode_bidang' => $item['kode_bidang'],
                    'bidang' => $item['bidang'],
                    'bidang_ing' => $item['bidang_ing'],
                    'kbbli_bidang' => $item['kbbli_bidang'],
                    'kode_web' => $item['kode_web'],
                ]
            );

            // Create certification scheme
            CertificationScheme::updateOrCreate(
                ['code_1' => $item['code_1'], 'code_2' => $item['code_2']],
                [
                    'nama' => $item['nama'],
                    'fee_tanda_tangan' => $item['fee_tanda_tangan'],
                    'skema_ing' => $item['skema_ing'],
                    'jenjang' => $item['jenjang'],
                ]
            );
        }
    }
}