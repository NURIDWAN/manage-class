<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perjanjian Kas {{ $agreement->agreement_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-top: 24px;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .signature {
            margin-top: 40px;
            text-align: center;
        }
        .signature img {
            max-height: 120px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #e5e7eb;
            font-size: 11px;
        }
    </style>
</head>
<body>
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <div class="header">
        <h2>Perjanjian Pembayaran Kas</h2>
        <p>Nomor: {{ $agreement->agreement_number }}</p>
    </div>

    <div>
        <div class="section-title">Detail Perjanjian</div>
        <table>
            <tr>
                <td width="30%">Nama Anggota</td>
                <td width="70%">: {{ $agreement->user?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Perjanjian</td>
                <td>: {{ optional($agreement->agreement_date)->translatedFormat('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jumlah Pembayaran</td>
                <td>: Rp {{ number_format($agreement->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Jatuh Tempo</td>
                <td>: {{ optional($agreement->due_date)->translatedFormat('d F Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: <span class="badge">{{ ucfirst(str_replace('_', ' ', $agreement->status)) }}</span></td>
            </tr>
        </table>

        @if ($agreement->notes)
            <div class="section-title">Catatan</div>
            <p>{{ $agreement->notes }}</p>
        @endif
    </div>

    <div class="signature">
        <p>Disetujui oleh,</p>
        @if ($agreement->signature_path && Storage::disk('public')->exists($agreement->signature_path))
            <img src="{{ Storage::disk('public')->path($agreement->signature_path) }}" alt="Tanda tangan">
        @else
            <p>(Tanda tangan belum diunggah)</p>
        @endif
        <p><strong>{{ $agreement->user?->name ?? '-' }}</strong></p>
    </div>
</body>
</html>
