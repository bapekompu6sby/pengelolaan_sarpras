@extends('layout.index')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.nav')
@endsection

@section('head')
    <style>
        .prop-card {
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            overflow: hidden;
            background: #fff
        }

        .prop-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f3f5
        }

        .prop-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: .75rem
        }

        .room-card {
            border: 1px solid #eef1f4;
            border-radius: .9rem;
            padding: .9rem;
            transition: .18s;
            height: 100%;
            background: #fff
        }

        .room-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .7rem 1.2rem rgba(0, 0, 0, .06)
        }

        .room-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: .25rem
        }

        .room-meta {
            font-size: .83rem;
            color: #6c757d
        }

        .empty-state {
            border: 1px dashed #dfe3e6;
            border-radius: .75rem;
            padding: 1rem;
            color: #6c757d;
            background: #fafbfc
        }

        .toolbar .form-control {
            min-width: 260px
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header + toolbar --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
            <h4 class="fw-bold mb-2">List Penginapan & Kamar</h4>
            {{-- <div class="toolbar d-flex gap-2">
                <input id="searchBox" type="text" class="form-control" placeholder="Cari properti/kamar…">
            </div> --}}
        </div>

        @if ($properties->isEmpty())
            <div class="empty-state">Belum ada data properti.</div>
        @else
            {{-- Tabs header: PROPERTI --}}
            <ul class="nav nav-tabs" id="propTabs" role="tablist">
                @foreach ($properties as $p)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="prop-tab-{{ $p->id }}"
                            data-bs-toggle="tab" data-bs-target="#prop-pane-{{ $p->id }}" type="button"
                            role="tab" aria-controls="prop-pane-{{ $p->id }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            data-prop-name="{{ \Illuminate\Support\Str::lower($p->name) }}">
                            {{ $p->name }}
                            <span class="badge bg-secondary ms-1">{{ $p->kamar->count() }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>

            {{-- Tabs content: PROPERTI -> LANTAI --}}
            <div class="tab-content mt-3 p-0" id="propTabsContent">
                @foreach ($properties as $p)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="prop-pane-{{ $p->id }}"
                        role="tabpanel" aria-labelledby="prop-tab-{{ $p->id }}">

                        <div class="prop-card mb-3">
                            {{-- Header properti --}}
                            <div class="prop-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <img class="prop-img" src="{{ asset('uploads/' . ($p->image_path ?? '')) }}"
                                        alt="{{ $p->name }}">
                                    <div>
                                        <h5 class="mb-1">
                                            {{ $p->name }}
                                            <span class="badge bg-secondary ms-1">{{ ucfirst($p->type) }}</span>
                                        </h5>
                                        <div class="text-muted small">
                                            Unit: <strong>{{ $p->unit ?? '—' }}</strong> ·
                                            Jumlah kamar: <strong>{{ $p->kamar->count() }}</strong>
                                        </div>
                                    </div>
                                </div>

                                @auth
                                    @if (auth()->user()->role !== 'supervisor')
                                        <button class="btn btn-success btn-sm"
                                            onclick="openAddKamarModal({{ $p->id }}, '{{ $p->name }}')">
                                            Tambah Kamar
                                        </button>
                                    @endif
                                @endauth
                            </div>

                            <div class="card-body">
                                @if (($p->floors ?? collect())->isEmpty())
                                    <div class="empty-state">
                                        Belum ada kamar dengan informasi lantai. Silakan lengkapi kolom <em>lantai</em> pada
                                        data kamar.
                                    </div>
                                @else
                                    {{-- Tabs LANTAI --}}
                                    <ul class="nav nav-tabs" id="floor-tabs-{{ $p->id }}" role="tablist">
                                        @foreach ($p->floors as $lantai => $roomsOnFloor)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
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

                                    <div class="tab-content mt-3" id="floor-tab-content-{{ $p->id }}">
                                        @foreach ($p->floors as $lantai => $roomsOnFloor)
                                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                id="floor-pane-{{ $p->id }}-{{ $lantai }}" role="tabpanel"
                                                aria-labelledby="floor-tab-{{ $p->id }}-{{ $lantai }}"
                                                tabindex="0">

                                                <div class="row g-3 rooms-grid-{{ $p->id }}">
                                                    @foreach ($roomsOnFloor as $k)
                                                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xxl-2 room-wrapper"
                                                            data-room="{{ \Illuminate\Support\Str::lower($k->nama_kamar) }}">
                                                            <div class="room-card">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-start">
                                                                    <div>
                                                                        <div class="room-title">{{ $k->nama_kamar }}</div>
                                                                        <div class="room-meta">
                                                                            Kapasitas: {{ $k->kapasitas ?? 1 }} · Lantai
                                                                            {{ (int) $k->lantai }}
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @auth
                                                                    @if (auth()->user()->role !== 'supervisor')
                                                                        <div class="mt-3 d-flex gap-2">
                                                                            <button
                                                                                class="btn btn-sm btn-warning btn-edit-kamar"
                                                                                data-id="{{ $k->id }}"
                                                                                data-nama="{{ $k->nama_kamar }}"
                                                                                data-kapasitas="{{ $k->kapasitas ?? 1 }}"
                                                                                data-lantai="{{ (int) $k->lantai }}"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#editKamarModal">
                                                                                Edit
                                                                            </button>

                                                                            <form action="{{ route('kamar.destroy', $k->id) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="btn btn-sm btn-outline-danger"
                                                                                    onclick="return confirm('Yakin ingin menghapus kamar ini?')">
                                                                                    Remove
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

        {{-- Modal Tambah Kamar --}}
        <div class="modal fade" id="addKamarModal" tabindex="-1" aria-labelledby="addKamarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form id="addKamarForm" method="POST" action="{{ route('kamar.store') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addKamarModalLabel">Tambah Kamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="properties_id" name="properties_id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama_kamar" class="form-label">Nama Kamar</label>
                                    <input type="text" class="form-control" id="nama_kamar" name="nama_kamar"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label for="kapasitas" class="form-label">Kapasitas</label>
                                    <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                                        value="1" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="lantai" class="form-label">Lantai</label>
                                    <input type="number" class="form-control" id="lantai" name="lantai"
                                        min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Tambah Kamar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Modal Edit Kamar (POST saja) --}}
        <div class="modal fade" id="editKamarModal" tabindex="-1" aria-labelledby="editKamarModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="editKamarForm" method="POST" action="">
                        @csrf {{-- POST saja, tanpa method spoof --}}
                        <div class="modal-header">
                            <h5 class="modal-title" id="editKamarModalLabel">Edit Kamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="kamar_id" name="kamar_id">

                            <div class="mb-3">
                                <label for="nama_kamar_edit" class="form-label">Nama Kamar</label>
                                <input type="text" class="form-control" id="nama_kamar_edit" name="nama_kamar"
                                    required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="kapasitas_edit" class="form-label">Kapasitas</label>
                                    <input type="number" class="form-control" id="kapasitas_edit" name="kapasitas"
                                        min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lantai_edit" class="form-label">Lantai</label>
                                    <input type="number" class="form-control" id="lantai_edit" name="lantai"
                                        min="1" required>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update Kamar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
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
