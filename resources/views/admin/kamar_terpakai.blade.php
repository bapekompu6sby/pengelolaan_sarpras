@extends('layout.index')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.nav')
@endsection

@section('head')
    <style>
        .bg-secondary-subtle {
            background: #f1f3f5 !important;
        }

        .room-card {
            border: 1px solid #e9ecef;
            border-radius: .85rem;
            padding: 1rem;
            background: #fff;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            min-height: 140px
        }

        .room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .75rem 1.25rem rgba(0, 0, 0, .08);
            border-color: #dee2e6
        }

        .room-title {
            font-weight: 700;
            font-size: 1.05rem
        }

        .room-meta {
            font-size: .85rem;
            color: #6c757d
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            padding: .15rem .6rem;
            border-radius: 999px;
            background: #f8f9fa;
            border: 1px solid #e9ecef
        }

        .dot {
            width: .55rem;
            height: .55rem;
            border-radius: 50%
        }

        .dot-green {
            background: #198754
        }

        .dot-red {
            background: #dc3545
        }

        .dot-amber {
            background: #fd7e14
        }

        .list-compact {
            list-style: none;
            margin: 0;
            padding: 0
        }

        .list-compact li {
            padding: .5rem .25rem;
            border-bottom: 1px solid #f1f3f5
        }

        .list-compact li:last-child {
            border-bottom: 0
        }

        .text-truncate-max {
            max-width: 160px;
            display: inline-block;
            vertical-align: bottom;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Kamar Terpakai & Jadwal</h4>
            <div class="room-meta">
                <span class="status-chip" title="Sedang dipakai hari ini"><span class="dot dot-red"></span>Terpakai</span>
                <span class="status-chip" title="Ada jadwal mendatang"><span class="dot dot-amber"></span>Terjadwal</span>
                <span class="status-chip" title="Tidak ada jadwal & tidak terpakai hari ini"><span
                        class="dot dot-green"></span>Free</span>
            </div>
        </div>

        @if ($rooms->isEmpty())
            <div class="alert alert-info">Belum ada data pemakaian kamar dari hari ini ke depan.</div>
        @else
            <div class="row g-3">
                @foreach ($rooms as $room)
                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                        <div class="room-card">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="room-title">{{ $room['kamar']->nama_kamar }}</div>
                                    <div class="room-meta">Kapasitas: {{ $room['kapasitas'] }}</div>
                                </div>
                                <div>
                                    @if ($room['occupied'])
                                        <span class="status-chip"><span class="dot dot-red"></span>Terpakai</span>
                                    @elseif ($room['upcoming']->isNotEmpty())
                                        <span class="status-chip"><span class="dot dot-amber"></span>Terjadwal</span>
                                    @else
                                        <span class="status-chip"><span class="dot dot-green"></span>Free</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-2">
                                @forelse ($room['upcoming'] as $u)
                                    <ul class="list-compact">
                                        <li class="small py-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <strong>{{ $u['range'] }}</strong>
                                                <span class="badge bg-light text-dark text-truncate-max"
                                                    title="{{ $u['guest'] }}">
                                                    {{ $u['guest'] }}
                                                </span>
                                            </div>

                                            @php
                                                $names = collect($u['penghunis'] ?? []);
                                                $limit = 3;
                                                $extra = max(0, $names->count() - $limit);
                                            @endphp

                                            @if ($names->isNotEmpty())
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    @foreach ($names->take($limit) as $nm)
                                                        <span
                                                            class="badge rounded-pill bg-secondary-subtle text-dark border">{{ $nm }}</span>
                                                    @endforeach
                                                    @if ($extra > 0)
                                                        <span
                                                            class="badge rounded-pill bg-secondary-subtle text-dark border">+{{ $extra }}</span>
                                                    @endif
                                                </div>
                                            @endif


                                        </li>
                                    </ul>
                                @empty
                                    <div class="text-muted small">Belum ada jadwal.</div>
                                @endforelse
                            </div>

                            {{-- Opsional: total penghuni di kamar ini (kalau dikirim dari controller) --}}
                            @if (!empty($room['total_penghuni']))
                                <div class="mt-2 text-muted small">
                                    Total penghuni tercatat: {{ $room['total_penghuni'] }}
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
