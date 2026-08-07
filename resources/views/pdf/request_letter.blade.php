<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 25px 35px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            color: #222;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #1c2d4a;
            padding-bottom: 8px;
            margin-bottom: 4px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 60px;
        }

        .logo-cell img {
            width: 48px;
            height: 48px;
        }

        .brand-name {
            font-size: 18px;
            font-weight: bold;
            color: #1c2d4a;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 10.5px;
            color: #555;
            margin: 0;
        }

        .kode-cell {
            text-align: right;
            font-size: 10.5px;
            color: #555;
        }

        .kode-cell strong {
            font-size: 15px;
            color: #1c2d4a;
        }

        .doc-title {
            text-align: center;
            margin: 16px 0 3px 0;
        }

        .doc-title h1 {
            font-size: 20px;
            margin: 0;
            letter-spacing: 0.5px;
            color: #1c2d4a;
        }

        .doc-subtitle {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12.5px;
            font-weight: bold;
            color: #1c2d4a;
            text-transform: uppercase;
            border-bottom: 2px solid #c8a13c;
            padding-bottom: 4px;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        table.info-table {
            width: 100%;
        }

        table.info-table td {
            padding: 3.5px 0;
            vertical-align: top;
            font-size: 13px;
        }

        table.info-table td.label {
            width: 125px;
            color: #555;
        }

        table.info-table td.sep {
            width: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border: 1px solid #999;
            background-color: #f2f2f2;
            color: #555;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0.3px;
        }

        .status-badge.approved {
            border-color: #2e7d32;
            background-color: #eef7ee;
            color: #2e7d32;
        }

        .status-badge.pending {
            border-color: #b8860b;
            background-color: #fdf6e3;
            color: #8a6d00;
        }

        .status-badge.rejected {
            border-color: #b71c1c;
            background-color: #fdeaea;
            color: #b71c1c;
        }

        .status-badge.waiting-payment {
            border-color: #b8860b;
            background-color: #fdf6e3;
            color: #8a6d00;
        }

        .status-badge.cancelled {
            border-color: #757575;
            background-color: #eeeeee;
            color: #616161;
        }

        .detail-box {
            margin-top: 4px;
        }

        .footer-table {
            width: 100%;
            margin-top: 24px;
        }

        .validity-box {
            border: 1px dashed #1c2d4a;
            border-left: 4px solid #c8a13c;
            border-radius: 2px;
            padding: 11px 13px;
            font-size: 11px;
            color: #333;
        }

        .validity-box .title {
            color: #1c2d4a;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .validity-box .idtx {
            color: #888;
            font-size: 9.5px;
            margin-top: 6px;
        }

        .sign-cell {
            text-align: center;
            font-size: 11px;
            vertical-align: top;
            padding-left: 20px;
        }

        .sign-cell .qr {
            margin: 6px 0;
        }

        .sign-cell .qr img {
            width: 70px;
            height: 70px;
        }

        .sign-cell .role {
            font-weight: bold;
            margin-top: 2px;
        }

        .page-footer {
            margin-top: 26px;
            font-size: 9px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if (file_exists(public_path('assets/img/favicon/logo.png')))
                    <img src="{{ public_path('assets/img/favicon/logo.png') }}">
                @endif
            </td>
            <td>
                <p class="brand-name">TOPANG+</p>
                <p class="brand-sub">Sistem Monitoring Okupansi Peminjaman Ruangan — Bapekom PU Wilayah VI Surabaya</p>
            </td>
            <td class="kode-cell">
                KODE BOOKING<br>
                <strong>#{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}</strong>
            </td>
        </tr>
    </table>

    <div class="doc-title">
        <h1>RESERVASI PEMINJAMAN RUANGAN</h1>
    </div>
    <p class="doc-subtitle">Dokumen ini adalah bukti sah pengajuan peminjaman ruang/sarana Bapekom PU Wilayah VI Surabaya
    </p>

    <div class="detail-box">
        <div class="section-title">Informasi Pemohon</div>
        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="sep">:</td>
                <td><strong>{{ $t->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Instansi</td>
                <td class="sep">:</td>
                <td>{{ $t->instansi ?? $t->affiliation }}</td>
            </tr>
            <tr>
                <td class="label">Kontak</td>
                <td class="sep">:</td>
                <td>{{ $t->phone_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="sep">:</td>
                <td>{{ $t->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="detail-box">
        <div class="section-title">Status Permohonan</div>
        <span class="status-badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
        <p style="margin-top:7px;">Tgl Pengajuan:
            {{ \Carbon\Carbon::parse($t->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</p>
    </div>

    <div class="detail-box">
        <div class="section-title">Detail Peminjaman</div>
        <table class="info-table">
            <tr>
                <td class="label">Kegiatan</td>
                <td class="sep">:</td>
                <td><strong>{{ $t->kegiatan }}</strong></td>
            </tr>
            <tr>
                <td class="label">Venue</td>
                <td class="sep">:</td>
                <td>{{ $t->properties->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Peserta</td>
                <td class="sep">:</td>
                <td>{{ $t->jumlah_peserta ?? '-' }} Orang</td>
            </tr>
            <tr>
                <td class="label">Waktu Penggunaan</td>
                <td class="sep">:</td>
                <td>
                    {{ \Carbon\Carbon::parse($t->start)->translatedFormat('d M Y') }}
                    @if ($t->jam_start)
                        {{ \Carbon\Carbon::parse($t->jam_start)->format('H:i') }}
                    @endif
                    s/d
                    {{ \Carbon\Carbon::parse($t->end)->translatedFormat('d M Y') }}
                    @if ($t->jam_end)
                        {{ \Carbon\Carbon::parse($t->jam_end)->format('H:i') }}
                    @endif
                </td>
            </tr>
            @if ($t->description)
                <tr>
                    <td class="label">Catatan</td>
                    <td class="sep">:</td>
                    <td>{{ $t->description }}</td>
                </tr>
            @endif
            @if ($statusInfo['class'] === 'rejected' && $t->rejection_reason)
                <tr>
                    <td class="label">Alasan Penolakan</td>
                    <td class="sep">:</td>
                    <td>{{ $t->rejection_reason }}</td>
                </tr>
            @endif
        </table>
    </div>

    <table class="footer-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="validity-box">
                    <div class="title">Dokumen Sah &amp; Valid</div>
                    Dokumen ini diterbitkan otomatis oleh sistem berdasarkan data pengajuan yang tersimpan.
                    @if (!empty($transactionUuid))
                        <div class="idtx">ID Transaksi: {{ $transactionUuid }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="page-footer">
        Dicetak otomatis oleh sistem pada {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB —
        Kode Booking #{{ str_pad($t->id, 6, '0', STR_PAD_LEFT) }}
    </div>

</body>

</html>
