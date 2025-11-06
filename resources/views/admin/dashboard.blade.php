@extends('layout.auth_layout')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/driver.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-soft {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .05)
        }

        .muted {
            color: #6c757d;
            font-size: .9rem
        }

        .chart-box {
            height: 320px;
            min-width: 720px
        }

        .chart-scroll {
            overflow-x: auto
        }

        .card-body form select {
            min-width: 120px;
        }

        .card-body form label {
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .card-body form .btn-sm {
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
        }

        .card-body {
            padding: 1rem 1.25rem;
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">{{ session('success') }}</x-toast>
    @endif
    @if (session('failed'))
        <x-toast bgColor="bg-danger" title="Failed">{{ session('failed') }}</x-toast>
    @endif

    <div class="p-3">

        {{-- Row 1: Kegiatan + Ringkasan Stok --}}
        <div class="row g-3">
            {{-- Kegiatan --}}
            <div class="col-lg-6 mb-4">
                <div class="card" id="kegiatan">
                    <div class="card-body">
                        <div class="mb-3">
                            <h1 class="h3 fw-bold text-dark mb-4">Dashboard</h1>
                            <h4 class="text-dark mb-0">
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-task"></i>
                                </span>
                                Kegiatan
                            </h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-3">
                                <thead>
                                    <tr>
                                        <th>Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $e)
                                        <tr>
                                            <td class="text-break">{{ ucfirst($e->kegiatan) }}</td>
                                            <td>{{ date('d-m-Y', strtotime($e->start)) }} |
                                                {{ date('d-m-Y', strtotime($e->end)) }}</td>
                                            <td>{{ $e->properties->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ route('bookings') }}" class="btn btn-success  w-100">Selengkapnya</a>
                    </div>
                </div>
            </div>

            {{-- Ringkasan stok per jenis --}}
            <div class="col-lg-6">
                <div class="col-12 mb-3">
                    <div class="card border-0 rounded-3">
                        <div class="card-body text-center py-2">
                            <span class="fw-bold text-primary fs-5">Ruangan Per Hari Ini</span>
                        </div>
                    </div>
                </div>
                <div class="row gx-3 gy-3 mb-3">

                    {{-- Helper: bikin array biar DRY --}}
                    @php
                        $summary = [
                            [
                                'label' => 'Aula',
                                'badgeClass' => 'bg-label-primary',
                                'stock' => $aulaStock,
                                'available' => $availableAula,
                                'occupied' => $aulaNotAvailable,
                                'btnClass' => 'btn-primary',
                            ],
                            [
                                'label' => 'Kelas',
                                'badgeClass' => 'bg-label-warning',
                                'stock' => $kelasStock,
                                'available' => $availableKelas,
                                'occupied' => $kelasNotAvailable,
                                'btnClass' => 'btn-warning',
                            ],
                            [
                                'label' => 'Asrama',
                                'badgeClass' => 'bg-label-success',
                                'stock' => $asramaStock,
                                'available' => $availableAsrama,
                                'occupied' => $asramaNotAvailable,
                                'btnClass' => 'btn-success',
                            ],
                            [
                                'label' => 'Paviliun',
                                'badgeClass' => 'bg-label-secondary', // pakai kelas, bukan inline style
                                'stock' => $paviliunStock,
                                'available' => $availablePaviliun,
                                'occupied' => $paviliunNotAvailable,
                                'btnClass' => 'btn-secondary',
                            ],
                        ];
                    @endphp

                    @foreach ($summary as $i => $s)
                        <div class="col-12 col-md-6">
                            <div class="card compact-card" id="summary-{{ Str::slug($s['label']) }}">
                                <div class="card-body compact-body">
                                    <h4 class="text-dark mb-2">
                                        <span class="badge {{ $s['badgeClass'] }} me-2">
                                            <i class="bx bx-buildings"></i>
                                        </span>
                                        {{ $s['label'] }}
                                    </h4>

                                    <div class="d-flex justify-content-evenly flex-wrap text-center">
                                        <div>
                                            <span class="d-block mb-1">Kapasitas</span>
                                            <h4 class="mb-0 text-danger">{{ $s['stock'] }}</h4>
                                        </div>
                                        <div>
                                            <span class="d-block mb-1">Kosong</span>
                                            <h4 class="mb-0 text-success">{{ $s['available'] }}</h4>
                                        </div>
                                        <div>
                                            <span class="d-block mb-1">Terisi</span>
                                            <h4 class="mb-0 text-warning">{{ $s['occupied'] }}</h4>
                                        </div>
                                    </div>

                                    <a href="{{ route('bookings') }}" class="btn {{ $s['btnClass'] }} mt-3 w-100">
                                        Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- FILTER PERIODE (UI terkelompok & jelas) --}}
        <div class="card card-soft mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">

                {{-- Kiri: Filter --}}
                <form method="GET" action="{{ route('dashboardAdmin') }}"
                    class="w-100 w-md-auto d-flex flex-column gap-2 mb-0" id="filterForm">

                    {{-- MODE SWITCH --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="form-label mb-0 fw-semibold text-uppercase small text-secondary">Mode
                                Periode</span>
                            <div class="btn-group" role="group" aria-label="Mode Periode">
                                @php $isRange = $useRange ?? false; @endphp
                                <input type="radio" class="btn-check" name="mode_radio" id="mode-single"
                                    autocomplete="off" {{ $isRange ? '' : 'checked' }}>
                                <label class="btn btn-sm btn-outline-primary" for="mode-single">Per Bulan</label>

                                <input type="radio" class="btn-check" name="mode_radio" id="mode-range" autocomplete="off"
                                    {{ $isRange ? 'checked' : '' }}>
                                <label class="btn btn-sm btn-outline-primary" for="mode-range">Rentang Bulan</label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                            <a href="{{ route('dashboardAdmin') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        </div>
                    </div>

                    {{-- HIDDEN: nilai yang benar-benar dikirim (sesuai controller) --}}
                    <input type="hidden" name="use_range" id="useRangeHidden" value="{{ $isRange ? 1 : 0 }}">

                    {{-- GROUP: PER BULAN --}}
                    <fieldset id="group-single" class="border rounded p-2 bg-light">
                        <legend class="float-none w-auto px-2 small text-secondary mb-0">Per Bulan</legend>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <label class="form-label mb-0 fw-semibold text-uppercase small text-secondary">Tahun</label>
                            <select name="year" class="form-select form-select-sm w-auto">
                                @foreach ($years as $y)
                                    <option value="{{ $y }}" @selected((int) $year === (int) $y)>{{ $y }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="form-label mb-0 fw-semibold text-uppercase small text-secondary">Bulan</label>
                            <select name="month" class="form-select form-select-sm w-auto">
                                @foreach ($monthOptions as $val => $label)
                                    <option value="{{ $val }}" @selected((int) $month === (int) $val)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih 1 bulan tertentu atau “Semua Bulan”.</small>
                        </div>
                    </fieldset>

                    {{-- GROUP: RENTANG BULAN --}}
                    <fieldset id="group-range" class="border rounded p-2 bg-light">
                        <legend class="float-none w-auto px-2 small text-secondary mb-0">Rentang Bulan</legend>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <label class="form-label mb-0 fw-semibold text-uppercase small text-secondary">Periode</label>
                            <input type="month" name="start_month" id="startMonth"
                                class="form-control form-control-sm w-auto"
                                value="{{ request('start_month', now()->format('Y-m')) }}">
                            <span class="mx-1">s.d.</span>
                            <input type="month" name="end_month" id="endMonth"
                                class="form-control form-control-sm w-auto"
                                value="{{ request('end_month', request('start_month', now()->format('Y-m'))) }}">
                            <small class="text-muted">Sistem otomatis menyamakan/menukar jika rentang terbalik.</small>
                        </div>
                    </fieldset>
                </form>
                <p class="muted mt-2 mb-3 px-3">
                    Sumbu-X menampilkan <b>SEMUA properti</b> per tipe. Nilai Y = jumlah hari terpesan <b>approved</b> pada
                    <b>{{ $periodLabel }}</b>.
                </p>

                {{-- Kanan: Export Excel (pojok kanan atas) --}}
                <form method="GET" action="{{ route('export.perTahun') }}"
                    class="d-flex align-items-center gap-2 ms-auto align-self-start mt-2 mt-md-0">
                    <label for="exportYear" class="form-label mb-0 small text-secondary">Tahun</label>
                    <select name="year" id="exportYear" class="form-select form-select-sm w-auto">
                        @foreach ($years as $yy)
                            <option value="{{ $yy }}" {{ (int) now()->year === (int) $yy ? 'selected' : '' }}>
                                {{ $yy }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </form>


            </div>

        </div>

        {{-- 5 CHART: per tipe (X = semua properti, Y = jumlah pesanan) --}}
        @php
            $titleMap = [
                'aula' => 'Aula',
                'paviliun' => 'Paviliun',
                'kelas' => 'Kelas',
                'asrama' => 'Asrama',
                'fasilitas' => 'Fasilitas',
            ];
        @endphp

        <div class="row g-3">
            @foreach ($types as $t)
                <div class="col-12">
                    <div class="card card-soft h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">{{ $titleMap[$t] ?? ucfirst($t) }}</h5>
                                <span class="muted">
                                    {{ $month ? $monthOptions[$month] : 'Jan–Des' }} {{ $year }}
                                </span>
                            </div>

                            @if (empty($charts[$t]['labels']))
                                <div class="muted">Belum ada data properti untuk tipe ini.</div>
                            @else
                                <div class="chart-scroll">
                                    <div class="chart-box" id="box_{{ $t }}">
                                        <canvas id="chart_{{ $t }}"></canvas>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const isRangeServer = {{ $useRange ?? false ? 'true' : 'false' }};
            const useRangeHidden = document.getElementById('useRangeHidden');

            const groupSingle = document.getElementById('group-single');
            const groupRange = document.getElementById('group-range');

            const modeSingle = document.getElementById('mode-single');
            const modeRange = document.getElementById('mode-range');

            const startMonth = document.getElementById('startMonth');
            const endMonth = document.getElementById('endMonth');

            function setMode(rangeMode) {
                useRangeHidden.value = rangeMode ? 1 : 0;

                groupRange.style.display = rangeMode ? 'block' : 'none';
                groupSingle.style.display = rangeMode ? 'none' : 'block';

                // Enable/disable agar field yang tidak dipakai tidak terkirim
                groupRange.querySelectorAll('input,select').forEach(el => el.disabled = !rangeMode);
                groupSingle.querySelectorAll('input,select').forEach(el => el.disabled = rangeMode);
            }

            // Inisialisasi tampilan awal dari server
            setMode(isRangeServer);

            // Toggle via radio
            modeSingle.addEventListener('change', () => setMode(false));
            modeRange.addEventListener('change', () => setMode(true));

            // Validasi ringan untuk rentang: end >= start
            function clampEnd() {
                if (!startMonth.value) return;
                // set minimal end = start
                endMonth.min = startMonth.value;
                if (endMonth.value && endMonth.value < startMonth.value) {
                    endMonth.value = startMonth.value;
                }
            }
            startMonth?.addEventListener('change', clampEnd);
            clampEnd();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const CHARTS = @json($charts);

            // Warna per tipe
            const COLOR = {
                aula: '#0d6efd',
                paviliun: '#20c997',
                kelas: '#ffc107',
                asrama: '#6f42c1',
                fasilitas: '#fd7e14',
            };

            Object.keys(CHARTS).forEach(type => {
                const cfg = CHARTS[type];
                if (!cfg || !cfg.labels || cfg.labels.length === 0) return;

                const labels = cfg.labels; // semua properti (X)
                const data = cfg.data; // jumlah pesanan (Y)

                // auto-extend width kalau label banyak
                const box = document.getElementById('box_' + type);
                if (box && labels.length > 8) {
                    box.style.minWidth = (labels.length * 110) + 'px'; // 110px per bar (atur sesuai selera)
                }

                const el = document.getElementById('chart_' + type);
                if (!el) return;

                new Chart(el.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah Pesanan',
                            data,
                            backgroundColor: (COLOR[type] ?? '#6c757d') +
                                '33', // transparan
                            borderColor: (COLOR[type] ?? '#6c757d'),
                            borderWidth: 1,
                            borderRadius: 8,
                            barPercentage: .8,
                            categoryPercentage: .7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    // tampilkan label penuh di tooltip
                                    title: items => items[0]?.label || ''
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 0,
                                    // potong label panjang biar rapi
                                    callback: function(value, index) {
                                        const lbl = labels[index] ?? '';
                                        return lbl.length > 16 ? lbl.slice(0, 16) + '…' : lbl;
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(0,0,0,.05)'
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
