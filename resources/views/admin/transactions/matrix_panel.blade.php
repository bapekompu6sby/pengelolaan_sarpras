@php
    $mode = $mode ?? 'embed';
    $isEmbed = $mode === 'embed';
    $properties = $properties ?? collect();
    $dates = $dates ?? collect();
    $matrix = $matrix ?? [];
    $totals = $totals ?? [];
    $maxTotal = $maxTotal ?? 0;
    $period = $period ?? ['start' => now(), 'end' => now()];
    $filters = $filters ?? ['start_month' => now()->format('Y-m'), 'end_month' => now()->format('Y-m')];
@endphp

@once
    <style>
        .matrix-table-wrapper {
            max-height: 70vh;
            overflow: auto;
        }

        .matrix-table thead th {
            position: sticky;
            top: 0;
            z-index: 3;
        }

        .matrix-table .sticky-col {
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 4;
        }

        .matrix-table tbody th.sticky-col {
            z-index: 2;
        }

        .matrix-action-buttons .btn {
            border-radius: 999px;
            padding: 0.55rem 1.6rem;
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .matrix-action-buttons .btn:hover,
        .matrix-action-buttons .btn:focus {
            transform: translateY(-1px);
            box-shadow: 0 0.65rem 1.15rem rgba(15, 61, 122, 0.15);
        }

        .btn-back {
            background: #fff;
            border: 1px solid #dbe5f1;
            color: #1f2a37;
        }

        .btn-back:hover {
            background: #f7f9fc;
            color: #101828;
        }

        .btn-export {
            background: linear-gradient(135deg, #45d16f, #1bb457);
            border: none;
            color: #fff;
            box-shadow: 0 0.65rem 1.35rem rgba(27, 180, 87, 0.35);
        }

        .btn-export:hover {
            color: #fff;
            background: linear-gradient(135deg, #3bc965, #18a84f);
        }

        .matrix-filter-actions .btn {
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.6rem 1rem;
        }

        .matrix-filter-actions .btn-primary {
            background: #0f3d7a;
            border-color: #0f3d7a;
            box-shadow: 0 0.55rem 1.1rem rgba(15, 61, 122, 0.25);
        }

        .matrix-filter-actions .btn-primary:hover {
            background: #0c3162;
            border-color: #0c3162;
        }

        .matrix-filter-actions .btn-reset {
            background: #fff;
            border: 1px solid #dbe5f1;
            color: #475467;
        }

        .matrix-filter-actions .btn-reset:hover {
            background: #eff3f9;
            color: #1f2a37;
        }

        .matrix-panel {
            animation: fadeIn 0.25s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .matrix-mobile-card {
            border: 1px solid #edf1f7;
            border-radius: 1rem;
            box-shadow: 0 0.75rem 1.25rem rgba(15, 61, 122, 0.08);
        }

        .matrix-mobile-card .card-header {
            background: #f6f8fb;
            border-bottom: 1px solid #edf1f7;
            font-weight: 600;
        }

        .matrix-mobile-property {
            border: 1px solid #edf1f7;
            border-radius: 0.65rem;
            padding: 0.85rem;
            margin-bottom: 0.85rem;
            background: #fff;
        }

        .matrix-mobile-property:last-child {
            margin-bottom: 0;
        }

        .matrix-mobile-property .entry-card {
            background: #f9fafc;
            border-radius: 0.75rem;
            padding: 0.75rem;
            border: 1px solid #eef2f7;
        }

        .matrix-mobile-summary .summary-item {
            border-bottom: 1px dashed #dbe5f1;
        }

        .matrix-mobile-summary .summary-item:last-child {
            border-bottom: 0;
        }
    </style>
@endonce

<div class="matrix-panel" data-mode="{{ $mode }}">
    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">
            {{ session('success') }}
        </x-toast>
    @endif

    @if (session('failed'))
        <x-toast bgColor="bg-danger" title="Failed">
            {{ session('failed') }}
        </x-toast>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid flex-grow-1 p-0">
        <div class="row g-0">
            <div class="col-12 px-3 py-3">
                <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between mb-3">
                    <div>
                        <h4 class="mb-1 text-brand">Rekap Peminjaman Ruangan</h4>
                        <p class="text-muted mb-0">
                            Periode: {{ $period['start']->translatedFormat('d M Y') }} -
                            {{ $period['end']->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="matrix-action-buttons d-flex flex-wrap gap-2">
                        @if ($isEmbed)
                            <button type="button" class="btn btn-back d-flex align-items-center js-matrix-close">
                                <i class="bx bx-left-arrow-alt me-1"></i>
                                Kembali ke daftar
                            </button>
                        @else
                            <a href="{{ route('transactions') }}" class="btn btn-back d-flex align-items-center">
                                <i class="bx bx-left-arrow-alt me-1"></i>
                                Kembali ke daftar
                            </a>
                        @endif
                        <a href="{{ route('transactions.ruangan.export.matrix', $filters) }}"
                            class="btn btn-export d-flex align-items-center">
                            <i class="bx bx-cloud-download bx-sm me-1"></i>
                            Export XLSX
                        </a>
                    </div>
                </div>

                <div class="card card-modern">
                    <div class="card-header">
                        <form method="GET" action="{{ route('transactions.ruangan.matrix') }}"
                            @if ($isEmbed) data-matrix-form="embed" @endif>
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label for="matrixStartMonth" class="form-label mb-1">Bulan awal</label>
                                    <input type="month" id="matrixStartMonth" name="start_month"
                                        class="form-control form-control-sm"
                                        value="{{ old('start_month', $filters['start_month']) }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="matrixEndMonth" class="form-label mb-1">Bulan akhir</label>
                                    <input type="month" id="matrixEndMonth" name="end_month"
                                        class="form-control form-control-sm"
                                        value="{{ old('end_month', $filters['end_month']) }}">
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2 matrix-filter-actions">
                                    <button
                                        class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                                        <i class="bx bx-show-alt me-1"></i>
                                        Tampilkan
                                    </button>
                                    @if ($isEmbed)
                                        <button type="button"
                                            class="btn btn-reset d-flex align-items-center justify-content-center js-matrix-reset"
                                            data-url="{{ route('transactions.ruangan.matrix') }}">
                                            Reset
                                        </button>
                                    @else
                                        <a href="{{ route('transactions.ruangan.matrix') }}"
                                            class="btn btn-reset d-flex align-items-center justify-content-center">Reset</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        @if ($properties->isEmpty())
                            <div class="alert alert-info mb-0">
                                Belum ada data ruangan yang dapat ditampilkan.
                            </div>
                        @else
                            <div class="table-responsive matrix-table-wrapper d-none d-xl-block">
                                <table class="table table-bordered table-sm align-top matrix-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sticky-col" style="min-width: 170px;">Tanggal</th>
                                            @foreach ($properties as $property)
                                                <th style="min-width: 260px;">{{ $property->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dates as $date)
                                            <tr>
                                                <th class="sticky-col bg-white">
                                                    {{ $date->translatedFormat('d M Y') }}
                                                </th>
                                                @foreach ($properties as $property)
                                                    @php
                                                        $entries = $matrix[$date->toDateString()][$property->id] ?? [];
                                                    @endphp
                                                    <td>
                                                        @forelse ($entries as $entry)
                                                            <div class="small mb-2 pb-2 border-bottom">
                                                                <div class="fw-semibold">
                                                                    {{ $entry['kegiatan'] ?: 'Tanpa nama kegiatan' }}
                                                                </div>
                                                                @if ($entry['instansi'])
                                                                    <div class="text-muted">{{ $entry['instansi'] }}</div>
                                                                @endif
                                                                <div>
                                                                    {{ $entry['name'] ?: 'Pemesan tidak diketahui' }}
                                                                    @if ($entry['phone'])
                                                                        <span class="text-muted">({{ $entry['phone'] }})</span>
                                                                    @endif
                                                                </div>
                                                                <div class="text-muted">{{ $entry['range'] }}</div>
                                                            </div>
                                                        @empty
                                                            <span class="text-muted">&mdash;</span>
                                                        @endforelse
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <th class="sticky-col bg-secondary text-white">
                                                Total dipinjam (hari)
                                            </th>
                                            @foreach ($properties as $property)
                                                @php
                                                    $value = $totals[$property->id] ?? 0;
                                                    $highlight = $maxTotal > 0 && $value === $maxTotal;
                                                    $classes = 'text-center';
                                                    if ($highlight) {
                                                        $classes .= ' fw-semibold text-white bg-success';
                                                    }
                                                @endphp
                                                <td class="{{ $classes }}">
                                                    {{ $value }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="matrix-mobile-view d-xl-none">
                                @foreach ($dates as $date)
                                    @php
                                        $dayMap = $matrix[$date->toDateString()] ?? [];
                                        $filledProps = $properties->filter(function ($property) use ($dayMap) {
                                            return !empty($dayMap[$property->id] ?? []);
                                        });
                                    @endphp
                                    <div class="card matrix-mobile-card mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>{{ $date->translatedFormat('d M Y') }}</span>
                                            <span class="badge bg-label-primary text-uppercase">Rekap</span>
                                        </div>
                                        <div class="card-body">
                                            @if ($filledProps->isEmpty())
                                                <p class="text-muted small mb-0">Tidak ada peminjaman ruangan.</p>
                                            @else
                                                @foreach ($filledProps as $property)
                                                    @php
                                                        $entries = $dayMap[$property->id] ?? [];
                                                    @endphp
                                                    <div class="matrix-mobile-property">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="property-title fw-semibold">{{ $property->name }}</span>
                                                            <span class="badge rounded-pill bg-label-success">
                                                                {{ count($entries) }} kegiatan
                                                            </span>
                                                        </div>
                                                        @foreach ($entries as $entry)
                                                            <div class="entry-card mb-2">
                                                                <div class="fw-semibold small">{{ $entry['kegiatan'] ?: 'Tanpa nama kegiatan' }}</div>
                                                                @if ($entry['instansi'])
                                                                    <div class="small text-muted">{{ $entry['instansi'] }}</div>
                                                                @endif
                                                                <div class="small">
                                                                    {{ $entry['name'] ?: 'Pemesan tidak diketahui' }}
                                                                    @if ($entry['phone'])
                                                                        <span class="text-muted">({{ $entry['phone'] }})</span>
                                                                    @endif
                                                                </div>
                                                                <div class="small text-muted">{{ $entry['range'] }}</div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="card matrix-mobile-summary mt-4">
                                    <div class="card-header bg-white">
                                        <strong>Total dipinjam (hari)</strong>
                                    </div>
                                    <div class="card-body p-0">
                                        @foreach ($properties as $property)
                                            @php
                                                $value = $totals[$property->id] ?? 0;
                                                $highlight = $maxTotal > 0 && $value === $maxTotal;
                                            @endphp
                                            <div
                                                class="summary-item d-flex align-items-center justify-content-between px-3 py-2 {{ $highlight ? 'bg-success text-white fw-semibold' : '' }}">
                                                <span>{{ $property->name }}</span>
                                                <span>{{ $value }} hari</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
