<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Laporan Pembayaran Kas - {{ $monthLabel }}</title>
        <style>
            *, *::before, *::after {
                box-sizing: border-box;
            }

            body {
                font-family: "DejaVu Sans", Arial, sans-serif;
                font-size: 12px;
                color: #111827;
                margin: 0;
                padding: 32px;
                background-color: #ffffff;
            }

            h1, h2, h3 {
                margin: 0 0 8px 0;
            }

            .meta {
                margin-bottom: 24px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 12px;
            }

            th, td {
                border: 1px solid #d1d5db;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f3f4f6;
                font-weight: 600;
                font-size: 11px;
                text-transform: uppercase;
            }

            .text-right {
                text-align: right;
            }

            .section {
                margin-top: 28px;
            }

            .summary-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 12px;
                margin-top: 12px;
            }

            .summary-card {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 12px;
                background-color: #f9fafb;
            }

            .summary-card span {
                display: block;
                font-size: 11px;
                color: #6b7280;
                margin-bottom: 4px;
            }

            .summary-card strong {
                font-size: 14px;
                color: #111827;
            }
        </style>
    </head>
    <body>
        <h1>Laporan Pembayaran Kas</h1>
        <div class="meta">
            <p>
                Bulan: <strong>{{ $monthLabel }}</strong><br>
                Tanggal cetak: {{ $generatedAt->translatedFormat('d F Y H:i') }}
            </p>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <span>Total Kas Masuk</span>
                <strong>Rp {{ number_format($totalCollected, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Total Kas Keluar</span>
                <strong>Rp {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Saldo Bersih Bulan Ini</span>
                <strong>Rp {{ number_format($netBalance ?? ($totalCollected - ($totalExpenses ?? 0)), 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Tarif Bulanan per Anggota</span>
                <strong>Rp {{ number_format($targetPerMember, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-card">
                <span>Total Pembayaran Tercatat</span>
                <strong>{{ $payments->count() }} transaksi</strong>
            </div>
            <div class="summary-card">
                <span>Anggota Belum Lunas</span>
                <strong>{{ $outstandingMembers->count() }} orang</strong>
            </div>
        </div>

        <div class="section">
            <h2>Rincian Pembayaran</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th class="text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $index => $payment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $payment->user?->name ?? 'Tidak diketahui' }}</td>
                            <td>{{ $payment->user?->nim ?? '-' }}</td>
                            <td>{{ $payment->date ? \Illuminate\Support\Carbon::parse($payment->date)->translatedFormat('d F Y') : '-' }}</td>
                            <td>{{ $payment->payment_method === 'transfer' ? 'Transfer' : 'Tunai' }}</td>
                            <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-right">Belum ada pembayaran terkonfirmasi pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Rincian Pengeluaran</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th class="text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($expenses ?? collect()) as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->date ? \Illuminate\Support\Carbon::parse($expense->date)->translatedFormat('d F Y') : '-' }}</td>
                            <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-right">Belum ada pengeluaran kas pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Daftar Anggota Belum Lunas</h2>
            @if ($outstandingMembers->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Kelas</th>
                            <th class="text-right">Sudah Bayar (Rp)</th>
                            <th class="text-right">Sisa (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($outstandingMembers as $index => $member)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $member['name'] ?? '-' }}</td>
                                <td>{{ $member['nim'] ?? '-' }}</td>
                                <td>{{ $member['kelas'] ?? '-' }}</td>
                                <td class="text-right">Rp {{ number_format($member['paid'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($member['remaining'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Seluruh anggota telah melunasi iuran kas bulan ini.</p>
            @endif
        </div>
    </body>
</html>
