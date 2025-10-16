{{-- File: resources/views/pdfs/spt-verifikator.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SPT Verifikator TUK</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ========== KOP SURAT ========== */
        .kop-container {
            width: 100%;
            margin-bottom: 15px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .left-section {
            width: 30%;
            vertical-align: middle;
            text-align: center;
            padding: 10px 0;
        }

        .left-section img {
            width: 150px;
            height: auto;
        }

        .right-section {
            width: 70%;
            vertical-align: middle;
            padding: 0;
        }

        .gradient-wrapper {
            position: relative;
            height: 68px;
            overflow: hidden;
        }

        .gradient-base {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: #1a237e;
            border-radius: 34px 0 0 34px;
        }

        .gradient-light {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 25%;
            background-color: #5c6bc0;
            border-radius: 34px 0 0 34px;
            opacity: 1;
        }

        .gradient-mid1 {
            position: absolute;
            left: 20%;
            top: 0;
            bottom: 0;
            width: 25%;
            background-color: #4a5fc1;
            opacity: 0.9;
        }

        .gradient-mid2 {
            position: absolute;
            left: 40%;
            top: 0;
            bottom: 0;
            width: 25%;
            background-color: #3949ab;
            opacity: 0.85;
        }

        .gradient-dark {
            position: absolute;
            left: 60%;
            top: 0;
            bottom: 0;
            width: 40%;
            background-color: #283593;
            opacity: 0.8;
        }

        .kop-divider {
            width: 100%;
            height: 0;
            border: none;
            border-top: 3px solid #000;
            margin: 5px 0 0 0;
        }

        /* ========== HEADER ========== */
        .header {
            text-align: center;
            margin: 25px 0 18px 0;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 14pt;
            margin: 3px 0 8px 0;
            font-weight: bold;
        }

        .spt-number {
            font-size: 12pt;
            margin: 0;
            font-weight: normal;
        }

        /* ========== CONTENT ========== */
        .content {
            text-align: justify;
            margin: 12px 0;
            line-height: 1.4;
        }

        .content p {
            margin: 0 0 8px 0;
        }

        .info-box {
            margin: 12px 0 12px 70px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            line-height: 1.6;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-label {
            width: 200px;
            font-weight: normal;
        }

        .info-separator {
            width: 15px;
            text-align: center;
        }

        .info-value {
            font-weight: normal;
        }

        /* ========== SIGNATURE ========== */
        .signature-section {
            margin-top: 50px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-spacer {
            width: 55%;
        }

        .signature-box {
            width: 45%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box p {
            margin: 8px 0;
            line-height: 1.4;
        }

        .sig-title {
            font-weight: bold;
        }

        .signature-image {
            margin: 24px auto;
            height: 70px;
            display: block;
        }

        .signature-name {
            display: inline-block;
            border-bottom: 1.5px solid #000;
            padding: 0 25px 4px 25px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- ========== KOP SURAT ========== -->
    <div class="kop-container">
        <table class="kop-table">
            <tr>
                <td class="left-section">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo LSP-PM">
                </td>
                <td class="right-section">
                    <div class="gradient-wrapper">
                        <div class="gradient-base"></div>
                        <div class="gradient-light"></div>
                        <div class="gradient-mid1"></div>
                        <div class="gradient-mid2"></div>
                        <div class="gradient-dark"></div>
                    </div>
                </td>
            </tr>
        </table>
        <hr class="kop-divider">
    </div>

    <!-- ========== HEADER ========== -->
    <div class="header">
        <h1>SURAT PERINTAH TUGAS</h1>
        <h2>VERIFIKATOR TUK SEWAKTU JARAK JAUH</h2>
        <p class="spt-number">{{ $spt_number }}</p>
    </div>

    <!-- ========== CONTENT ========== -->
    <div class="content">
        <p>Sehubungan dengan permohonan Verifikasi Tempat Uji Kompetensi (TUK) Sewaktu Jarak Jauh LSP Pasar Modal. Mana LSP Pasar Modal menugaskan:</p>
    </div>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">Nama</td>
                <td class="info-separator">:</td>
                <td class="info-value">{{ $verifikator_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Nomor NIK</td>
                <td class="info-separator">:</td>
                <td class="info-value">{{ $verifikator_nik }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Pelaksanaan Asesmen</td>
                <td class="info-separator">:</td>
                <td class="info-value">{{ $assessment_date }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p>Untuk menjadi tim Verifikator Tempat Uji Kompetensi (TUK) Sewaktu Jarak Jauh</p>
        <p>Demikian penugasan ini kami sampaikan, mohon dapat memberikan konfirmasi kesediaan dengan segera. Terima kasih atas perhatiannya.</p>
    </div>

    <!-- ========== SIGNATURE ========== -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-spacer"></td>
                <td class="signature-box">
                    <p>Jakarta, {{ $date }}</p>
                    <p class="sig-title">Direktur LSP Pasar Modal</p>

                    @if ($director_signature && file_exists($director_signature))
                        <img src="{{ $director_signature }}" alt="Signature" class="signature-image">
                    @else
                        <div class="signature-image"></div>
                    @endif

                    <p><span class="signature-name">{{ $director_name }}</span></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>