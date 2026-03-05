{{-- resources/views/emails/transactions_success.blade.php --}}
@php
    // Guard variabel agar fleksibel kalau kadang pakai $payload
    $user = $user ?? ($payload['user'] ?? null);
    $trx = $trx ?? ($payload['trx'] ?? null);

    // Helper mini untuk format tanggal & rupiah
    use Carbon\Carbon;

    $fmtDate = function ($d) {
        try {
            return Carbon::parse($d)->translatedFormat('d M Y');
        } catch (\Throwable $e) {
            return $d ?? '-';
        }
    };
    $rupiah = function ($n) {
        if ($n === null || $n === '') {
            return '-';
        }
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    };

    $statusMap = [
        'pending' => ['Menunggu', '#f59e0b', '#000'],
        'waiting_payment' => ['Menunggu Bayar', '#38bdf8', '#000'],
        'approved' => ['Disetujui', '#22c55e', '#fff'],
        'rejected' => ['Ditolak', '#ef4444', '#fff'],
        'done' => ['Selesai', '#16a34a', '#fff'],
    ];
    $st = strtolower($trx->status ?? 'pending');
    [$statusLabel, $statusBg, $statusColor] = $statusMap[$st] ?? ['-', '#e5e7eb', '#111827'];

    // Affiliation label
    $affMap = [
        'internal_pu' => 'Internal PUPR',
        'eksternal' => 'Eksternal',
    ];
    $affLabel = $affMap[$trx->affiliation ?? ''] ?? ucfirst($trx->affiliation ?? '-');

    // Ambil nama properti jika relasi tersedia
    $roomName = optional($trx->properties)->name ?? '-';
@endphp

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Transaksi Berhasil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Inline CSS ramah email --}}
    <style>
        /* Reset ringan */
        body {
            margin: 0;
            padding: 0;
            background: #f6f7fb;
            color: #111827;
        }

        table {
            border-collapse: collapse;
        }

        img {
            border: 0;
            outline: 0;
            text-decoration: none;
            display: block;
        }

        /* Container kartu */
        .wrapper {
            width: 100%;
            padding: 24px 12px;
            background: #f6f7fb;
        }

        .card {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        /* Header brand */
        .header {
            background: #003A70;
            color: #ffffff;
            padding: 18px 20px;
            font-weight: 700;
            font-size: 18px;
        }

        .subheader {
            padding: 12px 20px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Body */
        .body {
            padding: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #003A70;
        }

        .muted {
            color: #6b7280;
            font-size: 14px;
            margin: 0 0 16px;
        }

        /* Tabel detail */
        .detail {
            width: 100%;
        }

        .detail td {
            padding: 6px 0;
            vertical-align: top;
            font-size: 14px;
        }

        .label {
            width: 160px;
            color: #374151;
        }

        .value {
            color: #111827;
        }

        /* Badge status */
        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            background: #e5e7eb;
            color: #111827;
            font-weight: 600;
        }

        /* Footer */
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
        }

        .footer {
            padding: 0 20px 20px;
            color: #6b7280;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            background: #003A70;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
        }

        /* Responsif sederhana */
        @media (max-width: 480px) {
            .label {
                width: 120px;
            }

            .title {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                Bapekom 6 Surabaya — Konfirmasi Transaksi
            </div>

            <div class="subheader">
                Transaksi #{{ $trx->code ?? $trx->id }}
            </div>

            <div class="body">
                <h1 class="title">Halo {{ $user->name ?? 'Pengguna' }},</h1>
                <p class="muted">Berikut ringkasan transaksi peminjaman ruangan Anda. Simpan email ini sebagai arsip.
                </p>

                <table class="detail" role="presentation" aria-hidden="true">
                    <tr>
                        <td class="label">Instansi</td>
                        <td class="value">{{ $trx->instansi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kegiatan</td>
                        <td class="value">{{ $trx->kegiatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Ruangan</td>
                        <td class="value">{{ $roomName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal</td>
                        <td class="value">{{ $fmtDate($trx->start ?? null) }} — {{ $fmtDate($trx->end ?? null) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">
                            <span class="badge" style="background: {{ $statusBg }}; color: {{ $statusColor }};">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Afiliasi</td>
                        <td class="value">{{ $affLabel }}</td>
                    </tr>
                    <tr>
                        <td class="label">Unit Dipesan</td>
                        <td class="value">{{ $trx->ordered_unit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total Biaya</td>
                        <td class="value">{{ $rupiah($trx->total_harga ?? null) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kontak</td>
                        <td class="value">
                            {{ $trx->phone_number ?? '-' }}<br>
                            {{ $trx->email ?? '-' }}
                        </td>
                    </tr>
                    @if (!empty($trx->description))
                        <tr>
                            <td class="label">Keterangan</td>
                            <td class="value">{{ $trx->description }}</td>
                        </tr>
                    @endif
                </table>

                <div class="divider"></div>

                {{-- CTA opsional: misal ke halaman detail (ubah URL sesuai route kamu) --}}
                {{-- <p><a href="{{ url('/transactions/'.$trx->id) }}" class="btn" target="_blank" rel="noopener">Lihat Detail Transaksi</a></p> --}}

                <p class="muted">Jika ada pertanyaan, balas email ini atau hubungi admin kami.</p>
                <p>
                    Terima kasih,<br>
                    <strong>Bapekom 6 Surabaya</strong><br>
                    <span style="font-size: 12px; color: #666;">
                        Collab with MBKM UTM 2024
                    </span>
                </p>
            </div>

            <div class="footer">
                Email ini dibuat otomatis pada {{ now()->format('d-m-Y H:i') }}. Mohon tidak membalas jika tidak perlu.
            </div>
        </div>
    </div>
</body>

</html>
