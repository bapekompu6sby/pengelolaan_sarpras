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

        {{-- FILTER BULAN & TAHUN --}}
        <div class="card card-soft mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                {{-- Kiri: Filter untuk grafik --}}
                <form method="GET" action="{{ route('dashboardAdmin') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-0">
                    <label class="form-label mb-0 fw-semibold text-uppercase small text-secondary">Tahun</label>
                    <select name="year" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected((int) $year === (int) $y)>{{ $y }}</option>
                        @endforeach
                    </select>

                    <label class="form-label mb-0 fw-semibold text-uppercase small text-secondary">Bulan</label>
                    <select name="month" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @foreach ($monthOptions as $val => $label)
                            <option value="{{ $val }}" @selected((int) $month === (int) $val)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <a href="{{ route('dashboardAdmin') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </form>

                {{-- Kanan: Tombol Export Excel --}}
                <form method="GET" action="{{ route('export.perTahun') }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </form>
            </div>

            <p class="muted mt-2 mb-3 px-3">
                Sumbu-X menampilkan <b>SEMUA properti</b> per tipe. Nilai Y = jumlah pesanan <b>approved</b>
                {{ $month ? "pada bulan {$monthOptions[$month]}" : 'Jan–Des' }} {{ $year }}.
            </p>
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
