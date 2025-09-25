@extends('layout.admin_layout')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
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
                <div class="card card-modern">
                    {{-- Header card pakai aksen border-bottom biru dari .card-modern --}}
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-brand">Peminjaman Ruangan</h5>
                        <a href="{{ route('transactions.ruangan.export.matrix') }}" class="btn btn-success btn-modern"
                            data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                            title=""
                            data-bs-original-title="<i class='bx bx-spreadsheet bx-xs'></i> <span>Export to excel</span>">
                            <span class="tf-icons bx bx-cloud-download bx-sm"></span>
                        </a>
                    </div>

                    <div class="card-body mt-3">
                        {{-- Table --}}
                        <div class="table-responsive text-nowrap">
                            <table id="datatable2" class="table table-modern table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        @if (Auth::user()->role == 'admin')
                                            <th style="width:36px">✔</th>
                                        @endif
                                        <th>Nama Pemesan</th>
                                        <th>Instansi</th>
                                        <th>Kegiatan</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th style="width:120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($transactions as $t)
                                        <tr>
                                            @if (Auth::user()->role == 'admin')
                                                <td>
                                                    <input type="checkbox" class="form-check-input js-bulk-check"
                                                        value="{{ $t->id }}">
                                                </td>
                                            @endif

                                            <td>{{ $t->name }}</td>
                                            <td><strong>{{ ucfirst($t->instansi) }}</strong></td>
                                            <td>{{ $t->kegiatan }}</td>

                                            <td class="text-nowrap">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>{{ $t->properties->name }}</span>
                                                    @auth
                                                        @if (Auth::user()->role === 'admin' &&
                                                                $t->status === 'approved' &&
                                                                in_array($t->properties->type, ['asrama', 'paviliun']))
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary btn-modern rounded-pill px-3"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalTambahPenghuni-{{ $t->id }}"
                                                                title="Tambah penghuni">
                                                                <i class="bi bi-people-fill"></i>
                                                            </button>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </td>

                                            <td>
                                                <span class="me-1">
                                                    {{ date('Y-m-d', strtotime($t->start)) }} |
                                                    {{ date('Y-m-d', strtotime($t->end)) }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($t->status === 'rejected')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @elseif ($t->status === 'waiting_payment')
                                                    <span class="badge bg-info">Menunggu Pembayaran</span>
                                                @elseif ($t->status === 'pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @elseif ($t->status === 'approved')
                                                    @php
                                                        $isInternal = ($t->affiliation ?? '') === 'internal_pu';
                                                        $hasBilling = !empty($t->billing_qr);
                                                    @endphp
                                                    @if (!$isInternal && !$hasBilling)
                                                        <span class="badge bg-warning">Disetujui tapi belum bayar</span>
                                                    @else
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>

                                            <td>
                                                <button class="btn btn-warning btn-sm btn-modern" data-bs-toggle="modal"
                                                    data-bs-target="#modalDetailEdit{{ $t->id }}">
                                                    <i class="bx bx-edit-alt"></i>Detail/Edit
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Modal detail + modal penghuni --}}
                                        @include('admin.transactions.modal', ['t' => $t])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Bulk delete --}}
                        <form action="{{ route('transactions.ruangan.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="text" class="d-none" id="selected" name="selected">
                            <input type="button" class="btn btn-danger d-none" id="delete" value="Delete"
                                data-bs-toggle="modal" data-bs-target="#modalDeleteRooms">

                            <!-- Confirm modal -->
                            <div class="modal fade modal-modern" id="modalDeleteRooms" data-bs-backdrop="static"
                                tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus ruangan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menghapus data ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div> {{-- /card-body --}}
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                (function() {
                    const table = document.getElementById('datatable2');
                    const deleteBtn = document.getElementById('delete');
                    const selectedEl = document.getElementById('selected');
                    let selected = [];

                    // Checkbox bulk (delegated – aman walau DataTables redraw)
                    table.addEventListener('change', function(e) {
                        if (!e.target.classList.contains('js-bulk-check')) return;

                        const id = parseInt(e.target.value, 10);
                        if (e.target.checked) {
                            if (!selected.includes(id)) selected.push(id);
                        } else {
                            selected = selected.filter(s => s !== id);
                        }

                        selectedEl.value = selected.join(',');
                        deleteBtn.classList.toggle('d-none', selected.length === 0);
                    });

                    // Sinkron saat DataTables redraw (kalau dipakai)
                    document.addEventListener('draw.dt', function() {
                        deleteBtn.classList.toggle('d-none', selected.length === 0);
                    });
                })();
            </script>
        @endpush
    </div>
@endsection
