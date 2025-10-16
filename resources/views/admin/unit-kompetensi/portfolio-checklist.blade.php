{{-- resources/views/admin/unit-kompetensi/portfolio-checklist.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Portofolio - {{ $checklist['unit_info']['kode_unit'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .header h2 {
            color: #34495e;
            margin: 0 0 15px 0;
            font-size: 18px;
            font-weight: normal;
        }

        .unit-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .unit-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .unit-info td {
            padding: 5px 10px;
            border: none;
        }

        .unit-info td:first-child {
            font-weight: bold;
            width: 30%;
            color: #2c3e50;
        }

        .checklist-section {
            margin-bottom: 30px;
        }

        .section-title {
            background: #3498db;
            color: white;
            padding: 10px 15px;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .section-title.required {
            background: #e74c3c;
        }

        .section-title.optional {
            background: #f39c12;
        }

        .document-list {
            border: 1px solid #ddd;
        }

        .document-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border-bottom: 1px solid #eee;
            min-height: 60px;
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            margin-right: 15px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .document-content {
            flex: 1;
        }

        .document-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .document-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .document-status {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        .status-required {
            background: #e74c3c;
            color: white;
        }

        .status-optional {
            background: #f39c12;
            color: white;
        }

        .document-number {
            color: #7f8c8d;
            font-weight: bold;
            margin-right: 10px;
            min-width: 30px;
        }

        .summary {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }

        .summary h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-item {
            text-align: center;
            background: white;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #bdc3c7;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .instructions {
            background: #d5dbdb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .instructions h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #bdc3c7;
            font-size: 12px;
            color: #7f8c8d;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            padding: 20px 0;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
            height: 60px;
        }

        @media print {
            body {
                margin: 0;
                padding: 15mm;
            }

            .header {
                page-break-after: avoid;
            }

            .document-item {
                page-break-inside: avoid;
            }

            .summary {
                page-break-before: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CHECKLIST PORTOFOLIO KOMPETENSI</h1>
        <h2>{{ $checklist['unit_info']['scheme'] ?? 'Skema Sertifikasi' }}</h2>
    </div>

    <div class="unit-info">
        <table>
            <tr>
                <td>Kode Unit</td>
                <td>: {{ $checklist['unit_info']['kode_unit'] }}</td>
            </tr>
            <tr>
                <td>Judul Unit</td>
                <td>: {{ $checklist['unit_info']['judul_unit'] }}</td>
            </tr>
            <tr>
                <td>Skema Sertifikasi</td>
                <td>: {{ $checklist['unit_info']['scheme'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Generate</td>
                <td>: {{ $checklist['summary']['generated_at'] }}</td>
            </tr>
        </table>
    </div>

    <div class="instructions">
        <h4>Petunjuk Penggunaan:</h4>
        <ul>
            <li>Centang (✓) pada kotak yang tersedia jika dokumen sudah tersedia dan sesuai</li>
            <li>Dokumen dengan label <strong>WAJIB</strong> harus tersedia untuk penilaian</li>
            <li>Dokumen dengan label <strong>OPSIONAL</strong> dapat melengkapi portofolio namun tidak wajib</li>
            <li>Pastikan semua dokumen wajib telah terpenuhi sebelum mengajukan penilaian</li>
            <li>Dokumen harus asli atau salinan yang telah dilegalisasi</li>
        </ul>
    </div>

    @if($checklist['required_documents']->count() > 0)
    <div class="checklist-section">
        <div class="section-title required">
            DOKUMEN WAJIB ({{ $checklist['required_documents']->count() }})
        </div>
        <div class="document-list">
            @foreach($checklist['required_documents'] as $document)
            <div class="document-item">
                <div class="checkbox"></div>
                <span class="document-number">{{ $document['no'] }}.</span>
                <div class="document-content">
                    <div class="document-name">{{ $document['document_name'] }}</div>
                    @if($document['description'])
                        <div class="document-description">{{ $document['description'] }}</div>
                    @endif
                    <span class="document-status status-required">{{ $document['status'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($checklist['optional_documents']->count() > 0)
    <div class="checklist-section">
        <div class="section-title optional">
            DOKUMEN OPSIONAL ({{ $checklist['optional_documents']->count() }})
        </div>
        <div class="document-list">
            @foreach($checklist['optional_documents'] as $document)
            <div class="document-item">
                <div class="checkbox"></div>
                <span class="document-number">{{ $document['no'] }}.</span>
                <div class="document-content">
                    <div class="document-name">{{ $document['document_name'] }}</div>
                    @if($document['description'])
                        <div class="document-description">{{ $document['description'] }}</div>
                    @endif
                    <span class="document-status status-optional">{{ $document['status'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="summary">
        <h3>Ringkasan Dokumen</h3>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $checklist['summary']['total_documents'] }}</div>
                <div class="stat-label">Total Dokumen</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $checklist['summary']['required_count'] }}</div>
                <div class="stat-label">Wajib</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $checklist['summary']['optional_count'] }}</div>
                <div class="stat-label">Opsional</div>
            </div>
        </div>
        
        <p><strong>Catatan:</strong> Pastikan semua dokumen wajib ({{ $checklist['summary']['required_count'] }}) telah tersedia sebelum melakukan penilaian kompetensi.</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>
                <strong>Kandidat</strong><br>
                <small>Nama & Tanggal</small>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>
                <strong>Asesor</strong><br>
                <small>Nama & Tanggal</small>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>
            <strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i:s') }} |
            <strong>Unit:</strong> {{ $checklist['unit_info']['kode_unit'] }} |
            <strong>Sistem:</strong> {{ config('app.name') }}
        </p>
        <p>
            <em>Dokumen ini digenerate otomatis oleh sistem. Untuk informasi lebih lanjut, 
            hubungi administrator atau asesor yang bersangkutan.</em>
        </p>
    </div>

    <script>
        // Auto print when loaded (optional)
        // window.onload = function() { window.print(); }
        
        // Add print button functionality
        function printChecklist() {
            window.print();
        }
        
        // Add checkbox functionality for interactive use
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('click', function() {
                    this.innerHTML = this.innerHTML === '✓' ? '' : '✓';
                    this.style.backgroundColor = this.innerHTML === '✓' ? '#27ae60' : 'transparent';
                    this.style.color = 'white';
                    this.style.textAlign = 'center';
                    this.style.fontWeight = 'bold';
                    this.style.fontSize = '16px';
                });
            });
        });
    </script>
</body>
</html>