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
            background: #f1f3f5 !important
        }

        .room-card {
            border: 1px solid #e9ecef;
            border-radius: .85rem;
            padding: 1rem;
            background: #fff;
            transition: transform .18s, box-shadow .18s, border-color .18s;
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

        .section-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 999px;
            padding: .35rem .75rem;
            font-weight: 600;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- header + legenda --}}
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Kamar Terpakai & Jadwal</h4>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="status-chip" title="Sedang dipakai hari ini"><span class="dot dot-red"></span>Terpakai</span>
                <span class="status-chip" title="Ada jadwal mendatang"><span class="dot dot-amber"></span>Terjadwal</span>
                {{-- <span class="status-chip"><span class="dot dot-green"></span>Free</span> --}}
            </div>
        </div>

        @php
            $hasGroups = isset($groups) && $groups instanceof \Illuminate\Support\Collection && $groups->isNotEmpty();
        @endphp

        @if ($hasGroups)
            {{-- render per properti --}}
            @foreach ($groups as $propName => $items)
                <div class="mt-4 mb-2">
                    <span class="section-chip">
                        {{ $propName }}
                        <span class="badge bg-primary">{{ $items->count() }} Kamar</span>
                    </span>
                </div>

                @if ($items->isEmpty())
                    <div class="alert alert-light border">Belum ada jadwal.</div>
                @else
                    <div class="row g-3">
                        @foreach ($items as $room)
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <div class="room-card">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <div class="room-title">{{ $room['kamar']->nama_kamar }}</div>
                                            <div class="room-meta">
                                                Kapasitas: {{ $room['kapasitas'] }}
                                            </div>
                                        </div>
                                        <div>
                                            @if ($room['occupied'])
                                                <span class="status-chip"><span class="dot dot-red"></span>Terpakai</span>
                                            @elseif (!empty($room['upcoming']))
                                                <span class="status-chip"><span
                                                        class="dot dot-amber"></span>Terjadwal</span>
                                            @endif
                                            {{-- @else
                                                <span class="status-chip"><span class="dot dot-green"></span>Free</span>
                                            @endif --}}
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        @php $upcomings = collect($room['upcoming']); @endphp
                                        @forelse ($upcomings as $u)
                                            <ul class="list-compact">
                                                <li class="small py-1">
                                                    @php
                                                        $pemesan = $u['pemesan'] ?? ($u['guest'] ?? '—');
                                                        $kegiatan = $u['kegiatan'] ?? null;
                                                        $names = collect($u['penghunis'] ?? []);
                                                        $limit = 5;
                                                        $extra = max(0, $names->count() - $limit);
                                                    @endphp

                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <strong>{{ $u['range'] }}</strong>
                                                        <span class="badge bg-light text-dark text-truncate-max"
                                                            title="{{ $pemesan }}">
                                                            {{ $pemesan }}
                                                        </span>
                                                    </div>

                                                    @if (!empty($kegiatan))
                                                        <div class="mt-1">
                                                            <div class="text-muted small">Kegiatan</div>
                                                            <div class="text-break">{{ $kegiatan }}</div>
                                                        </div>
                                                    @endif

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
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary mt-2 btn-edit-penghuni"
                                                        data-detail="{{ $u['detail_id'] }}"
                                                        data-kapasitas="{{ $room['kapasitas'] }}"
                                                        data-names='@json($u['penghunis'] ?? [])' data-bs-toggle="modal"
                                                        data-bs-target="#editPenghuniModal">
                                                        Edit Penghuni
                                                    </button>

                                                </li>
                                            </ul>
                                        @empty
                                            <div class="text-muted small">Belum ada jadwal.</div>
                                        @endforelse
                                    </div>

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
            @endforeach
        @else
            {{-- fallback: render semua tanpa group --}}
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
                                        <div class="room-meta">
                                            Kapasitas: {{ $room['kapasitas'] }}
                                        </div>
                                    </div>
                                    <div>
                                        @if ($room['occupied'])
                                            <span class="status-chip"><span class="dot dot-red"></span>Terpakai</span>
                                        @elseif (!empty($room['upcoming']))
                                            <span class="status-chip"><span class="dot dot-amber"></span>Terjadwal</span>
                                        @endif
                                        {{-- @else
                                            <span class="status-chip"><span class="dot dot-green"></span>Free</span>
                                        @endif --}}
                                    </div>
                                </div>

                                <div class="mt-2">
                                    @php $upcomings = collect($room['upcoming']); @endphp
                                    @forelse ($upcomings as $u)
                                        <ul class="list-compact">
                                            <li class="small py-1">
                                                @php
                                                    $pemesan = $u['pemesan'] ?? ($u['guest'] ?? '—');
                                                    $kegiatan = $u['kegiatan'] ?? null;
                                                    $names = collect($u['penghunis'] ?? []);
                                                    $limit = 5;
                                                    $extra = max(0, $names->count() - $limit);
                                                @endphp

                                                <div class="d-flex align-items-center justify-content-between">
                                                    <strong>{{ $u['range'] }}</strong>
                                                    <span class="badge bg-light text-dark text-truncate-max"
                                                        title="{{ $pemesan }}">
                                                        {{ $pemesan }}
                                                    </span>
                                                </div>

                                                @if (!empty($kegiatan))
                                                    <div class="mt-1 text-truncate-max" title="{{ $kegiatan }}">
                                                        Kegiatan: {{ $kegiatan }}
                                                    </div>
                                                @endif

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
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary mt-2 btn-edit-penghuni"
                                                    data-detail="{{ $u['detail_id'] }}"
                                                    data-kapasitas="{{ $room['kapasitas'] }}"
                                                    data-names='@json($u['penghunis'] ?? [])' data-bs-toggle="modal"
                                                    data-bs-target="#editPenghuniModal">
                                                    Edit Penghuni
                                                </button>

                                            </li>
                                        </ul>
                                    @empty
                                        <div class="text-muted small">Belum ada jadwal.</div>
                                    @endforelse
                                </div>

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
        @endif
        <!-- Modal Edit Penghuni (reusable) -->
        <div class="modal fade" id="editPenghuniModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('penghuni.update') }}" class="modal-content">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="detail_id" id="ep-detail-id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Nama Penghuni</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Isi sesuai kapasitas. Boleh dikosongkan jika belum terisi.
                        </p>
                        <div id="ep-fields" class="vstack gap-2">
                            <!-- input nama akan di-inject via JS -->
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-edit-penghuni');
                if (!btn) return;

                const detailId = btn.dataset.detail;
                const kapasitas = parseInt(btn.dataset.kapasitas || '1', 10);
                let names = [];
                try {
                    names = JSON.parse(btn.dataset.names || '[]');
                } catch (_) {}

                // isi hidden detail_id
                document.getElementById('ep-detail-id').value = detailId;

                // render fields
                const wrap = document.getElementById('ep-fields');
                wrap.innerHTML = '';
                for (let i = 0; i < kapasitas; i++) {
                    const val = (names[i] ?? '').toString();
                    const group = document.createElement('div');
                    group.className = 'input-group';

                    const span = document.createElement('span');
                    span.className = 'input-group-text';
                    span.textContent = `Penghuni ${i+1}`;

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'names[]';
                    input.className = 'form-control';
                    input.placeholder = 'Kosongkan bila tidak ada';
                    input.value = val;

                    group.appendChild(span);
                    group.appendChild(input);
                    wrap.appendChild(group);
                }
            });
        </script>

    </div>
@endsection
