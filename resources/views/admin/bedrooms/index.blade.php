@extends('layout.admin_layout')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/datatables/datatables.min.css') }}" rel="stylesheet">
    <style>
        /* ==== SCOPED KE HALAMAN INI SAJA ==== */
        .rooms-page .card-modern .card-header {
            background: #fff;

            /* no garis default */
            padding-bottom: 30px;
            /* ruang dalam header */

            /* jarak ke konten di bawah */
            position: relative;
        }

        /* nav-tabs di header: boleh WRAP, tanpa scroll & border default */
        .rooms-page .card-header .nav-tabs.card-header-tabs {
            border-bottom: 0 !important;
            margin-top: .5rem;
            gap: .5rem;
            flex-wrap: wrap;
            /* ← boleh turun baris */
            overflow: visible;
            /* bukan scroll */
        }

        /* matikan border bawah bawaan nav-tabs Bootstrap */
        .rooms-page .card-header .nav-tabs {
            border-bottom: 0 !important;
        }

        /* link tab: clean, no pill, 1 baris */
        .rooms-page .card-header .nav-tabs .nav-link {
            margin-bottom: .25rem;
            /* spasi antar baris */
            padding: .5rem .9rem;
            border: 0 !important;
            border-radius: 0;
            background: transparent !important;
            white-space: nowrap;
            color: var(--pupr-ink);
        }

        .rooms-page .card-header .nav-tabs .nav-link.active {
            font-weight: 700;
            color: var(--pupr-blue);
            box-shadow: inset 0 -3px 0 0 var(--pupr-blue);
        }


        /* hover feedback */
        .rooms-page .card-header .nav-tabs .nav-link:hover {
            color: var(--pupr-blue);
        }

        /* konten tepat di bawah tabs: no rounded & no double border */
        .rooms-page #propTabsContent,
        .rooms-page #propTabsContent .tab-pane,
        .rooms-page #propTabsContent .prop-card,
        .rooms-page #propTabsContent .card,
        .rooms-page #propTabsContent .card-modern {
            background: #fff;
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
            border-top: 0 !important;
        }

        /* prop-card pasca tabs: ratakan atas */
        .rooms-page .prop-card.rounded-top-0 {
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
            border-top: 0 !important;
        }
    </style>
@endsection




@section('content')
    <div class="rooms-page px-3 py-3">

        @if ($properties->isEmpty())
            <div class="empty-state">Belum ada data properti.</div>
        @else
            <div class="card card-modern">
                <div class="card-header ">
                    {{-- Title + icon --}}
                    <div class="d-flex align-items-center gap-2 py-3">
                        <i class="bx bx-buildings fs-4 text-brand" style="line-height:1;"></i>
                        <h5 class="mb-0">List Penginapan & Kamar</h5>
                    </div>

                    <ul class="nav nav-tabs card-header-tabs w-100  " id="propTabs" role="tablist">
                        @foreach ($properties as $p)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                    id="prop-tab-{{ $p->id }}" data-bs-toggle="tab"
                                    data-bs-target="#prop-pane-{{ $p->id }}" type="button" role="tab"
                                    aria-controls="prop-pane-{{ $p->id }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $p->name }}
                                    <span class="badge bg-secondary ms-1">{{ $p->kamar->count() }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="propTabsContent">
                        @foreach ($properties as $p)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="prop-pane-{{ $p->id }}" role="tabpanel"
                                aria-labelledby="prop-tab-{{ $p->id }}">

                                {{-- Info properti --}}
                                <div class="prop-card rounded-0"> {{-- dibikin rata atas --}}
                                    <div class="prop-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset('storage/uploads/properties/covers/' . ($p->image_path ?? '')) }}"
                                                alt="{{ $p->name }}"
                                                style="width:77px;height:77px;object-fit:cover;object-position:center;border-radius:12px"
                                                loading="lazy" decoding="async">

                                            <div>
                                                <h6 class="mb-1">
                                                    {{ $p->name }}
                                                    <span class="badge-soft ms-2">{{ ucfirst($p->type) }}</span>
                                                </h6>
                                                <div class="text-muted small">
                                                    Unit: <strong>{{ $p->unit ?? '—' }}</strong> ·
                                                    Jumlah kamar: <strong>{{ $p->kamar->count() }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        @auth
                                            @if (auth()->user()->role !== 'supervisor')
                                                <button class="btn btn-outline-primary btn-sm btn-modern"
                                                    onclick="openAddKamarModal({{ $p->id }}, '{{ $p->name }}')">
                                                    Tambah Kamar di Properti Ini
                                                </button>
                                            @endif
                                        @endauth
                                    </div>

                                    <div class="card-body px-0 py-3">
                                        {{-- Jika tidak ada kamar sama sekali --}}
                                        @if (($p->floors ?? collect())->isEmpty())
                                            <div class="empty-state">
                                                Belum ada kamar dengan informasi lantai. Silakan lengkapi kolom
                                                <em>lantai</em> pada data kamar.
                                            </div>
                                        @else
                                            {{-- Tabs Lantai --}}
                                            <ul class="nav nav-tabs" id="floor-tabs-{{ $p->id }}" role="tablist">
                                                @foreach ($p->floors as $lantai => $roomsOnFloor)
                                                    <li class="nav-item" role="presentation">
                                                        <button
                                                            class="nav-link {{ $loop->first ? 'active' : '' }} border border-1 border-black"
                                                            id="floor-tab-{{ $p->id }}-{{ $lantai }}"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#floor-pane-{{ $p->id }}-{{ $lantai }}"
                                                            type="button" role="tab"
                                                            aria-controls="floor-pane-{{ $p->id }}-{{ $lantai }}"
                                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                            Lantai {{ (int) $lantai }}
                                                            <span
                                                                class="badge bg-secondary ms-1">{{ $roomsOnFloor->count() }}</span>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="tab-content px-0 py-3" id="floor-tab-content-{{ $p->id }}">
                                                @foreach ($p->floors as $lantai => $roomsOnFloor)
                                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                        id="floor-pane-{{ $p->id }}-{{ $lantai }}"
                                                        role="tabpanel"
                                                        aria-labelledby="floor-tab-{{ $p->id }}-{{ $lantai }}"
                                                        tabindex="0">

                                                        <div class="table-responsive ">
                                                            <table id="datatable"
                                                                class="table table-modern table-compact table-sticky table-hover align-middle mb-0 ">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Nama Kamar</th>
                                                                        <th>Kapasitas</th>
                                                                        <th>Lantai</th>
                                                                        @auth
                                                                            @if (auth()->user()->role !== 'supervisor')
                                                                                <th>Aksi</th>
                                                                            @endif
                                                                        @endauth
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($roomsOnFloor as $k)
                                                                        <tr
                                                                            data-room="{{ \Illuminate\Support\Str::lower($k->nama_kamar) }}">
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td class="fw-semibold">{{ $k->nama_kamar }}
                                                                            </td>
                                                                            <td>{{ $k->kapasitas ?? 1 }}</td>
                                                                            <td>{{ (int) $k->lantai }}</td>
                                                                            @auth
                                                                                @if (auth()->user()->role !== 'supervisor')
                                                                                    <td class="text-end">
                                                                                        <div class="segmented-pill">
                                                                                            <button
                                                                                                class="btn btn-primary btn-sm btn-modern"
                                                                                                data-id="{{ $k->id }}"
                                                                                                data-nama="{{ $k->nama_kamar }}"
                                                                                                data-kapasitas="{{ $k->kapasitas ?? 1 }}"
                                                                                                data-lantai="{{ (int) $k->lantai }}"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#editKamarModal"
                                                                                                title="Edit kamar">
                                                                                                <i
                                                                                                    class="bx bx-edit-alt"></i><span>Edit</span>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn btn-danger btn-sm btn-modern"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#modalDeleteRoom{{ $k->id }}"
                                                                                                title="Hapus kamar">
                                                                                                <i
                                                                                                    class="bx bx-trash"></i><span>Hapus</span>
                                                                                            </button>
                                                                                        </div>
                                                                                    </td>
                                                                                @endif
                                                                            @endauth
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="5">
                                                                                <div class="empty-state mb-0">Belum ada
                                                                                    kamar pada lantai ini.</div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                </div> {{-- /prop-card --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div> {{-- /card card-modern --}}
        @endif

    </div>

    @push('modals')
        @include('admin.bedrooms.modal')
        @foreach ($properties as $p)
            @foreach ($p->floors ?? [] as $lantai => $roomsOnFloor)
                @foreach ($roomsOnFloor as $k)
                    @include('admin.bedrooms.modal', ['k' => $k, 'p' => $p])
                @endforeach
            @endforeach
        @endforeach
    @endpush
@endsection


@section('script')
    <script>
        // Buka modal tambah kamar (tetap)
        function openAddKamarModal(propertiesId, propertiesName) {
            document.getElementById('addKamarModalLabel').textContent = `Tambah Kamar di ${propertiesName}`;
            document.getElementById('properties_id').value = propertiesId;
            document.getElementById('addKamarForm').reset();
            new bootstrap.Modal(document.getElementById('addKamarModal')).show();
        }

        // Modal Edit: POST ke /kamar/edit/{id}
        document.getElementById('editKamarModal').addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            const id = btn.getAttribute('data-id');

            this.querySelector('#kamar_id').value = id;
            this.querySelector('#nama_kamar_edit').value = btn.getAttribute('data-nama') || '';
            this.querySelector('#kapasitas_edit').value = btn.getAttribute('data-kapasitas') || 1;
            this.querySelector('#lantai_edit').value = btn.getAttribute('data-lantai') || '';

            // jika route update kamu pakai POST custom:
            this.querySelector('#editKamarForm').action = "{{ url('/kamar/edit') }}/" + id;

            // (alternatif) kalau pakai resource PUT:
            // this.querySelector('#editKamarForm').action = "{{ route('kamar.update', ':id') }}".replace(':id', id);
            // dan tambahkan @method('PUT') di form.
        });
    </script>
@endsection
