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

    @php
        $matrixData = $matrixData ?? null;
        $matrixMode = $matrixMode ?? 'embed';
        $showMatrixOnly = $showMatrixOnly ?? false;
    @endphp

    <div class="container-fluid flex-grow-1 p-0">
        <div class="row g-0">
            <div class="col-12 px-3 py-3">
                <div class="card card-modern @if ($showMatrixOnly) d-none @endif" id="transactionsListWrapper">
                    {{-- Header card pakai aksen border-bottom biru dari .card-modern --}}
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="mb-0 text-brand">Peminjaman Ruangan</h5>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('transactions.ruangan.matrix') }}"
                                class="btn btn-outline-primary btn-modern d-flex align-items-center js-show-matrix"
                                data-matrix-url="{{ route('transactions.ruangan.matrix') }}">
                                <i class="bx bx-table bx-sm me-1"></i>
                                Lihat Rekap Tabel
                            </a>
                            <button class="btn btn-success btn-modern d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#exportRuanganModal" data-bs-html="true"
                                data-bs-original-title="<i class='bx bx-spreadsheet bx-xs'></i> <span>Export to Excel</span>">
                                <i class="bx bx-cloud-download bx-sm me-1"></i>
                                Export
                            </button>
                        </div>
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
                                                @elseif ($t->status === 'cancelled')
                                                    <span class="badge bg-danger">Dibatalkan</span>
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

                <div id="matrixPanelWrapper" class="{{ $matrixData ? '' : 'd-none' }}">
                    @if ($matrixData)
                        @include(
                            'admin.transactions.matrix_panel',
                            array_merge($matrixData, ['mode' => $matrixMode ?? 'embed']))
                    @endif
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
            <script>
                (function() {
                    const form = document.getElementById('exportRuanganForm');
                    const startEl = document.getElementById('startMonth');
                    const endEl = document.getElementById('endMonth');
                    const submitBtn = document.getElementById('btnExportSubmit');

                    // set default saat modal ditampilkan (optional)
                    const modalEl = document.getElementById('exportRuanganModal');
                    modalEl.addEventListener('shown.bs.modal', function() {
                        // fokus ke bulan awal
                        startEl.focus();
                    });

                    form.addEventListener('submit', function(e) {
                        // Bootstrap validation
                        if (!form.checkValidity()) {
                            e.preventDefault();
                            e.stopPropagation();
                        } else {
                            // Normalisasi: tukar jika end < start
                            const s = startEl.value; // 'YYYY-MM'
                            const eMonth = endEl.value;
                            if (s && eMonth && eMonth < s) {
                                // swap
                                endEl.value = s;
                                startEl.value = eMonth;
                            }

                            // UX: disable tombol + spinner
                            submitBtn.disabled = true;
                            submitBtn.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mempersiapkan...';
                        }
                        form.classList.add('was-validated');
                    }, false);
                })();
            </script>
            <script>
                (function() {
                    const trigger = document.querySelector('.js-show-matrix');
                    const listWrapper = document.getElementById('transactionsListWrapper');
                    const matrixWrapper = document.getElementById('matrixPanelWrapper');

                    if (!trigger || !listWrapper || !matrixWrapper) {
                        return;
                    }

                    const toggleView = (showMatrix) => {
                        if (showMatrix) {
                            listWrapper.classList.add('d-none');
                            matrixWrapper.classList.remove('d-none');
                        } else {
                            matrixWrapper.classList.add('d-none');
                            listWrapper.classList.remove('d-none');
                        }
                    };

                    const setLoading = () => {
                        matrixWrapper.innerHTML =
                            '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 mb-0">Memuat rekap ruangan...</p></div>';
                    };

                    const loadMatrix = (url, params = '') => {
                        if (!url) return;
                        toggleView(true);
                        setLoading();
                        const finalUrl = params ? `${url}?${params}` : url;
                        fetch(finalUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then((res) => {
                                if (!res.ok) throw new Error('Request failed');
                                return res.json();
                            })
                            .then((data) => {
                                matrixWrapper.innerHTML = data.html || '';
                                attachMatrixEvents();
                            })
                            .catch(() => {
                                matrixWrapper.innerHTML =
                                    `<div class="alert alert-danger m-4">Gagal memuat rekap. <a href="${url}" class="alert-link">Buka di tab baru</a> atau coba lagi.</div><div class="mt-3 text-center"><button type="button" class="btn btn-outline-secondary js-matrix-error-back">Kembali</button></div>`;
                                const errBack = matrixWrapper.querySelector('.js-matrix-error-back');
                                if (errBack) {
                                    errBack.addEventListener('click', () => toggleView(false), {
                                        once: true
                                    });
                                }
                            });
                    };

                    const attachMatrixEvents = () => {
                        const closeBtn = matrixWrapper.querySelector('.js-matrix-close');
                        if (closeBtn) {
                            closeBtn.addEventListener('click', () => toggleView(false), {
                                once: true
                            });
                        }

                        const form = matrixWrapper.querySelector('[data-matrix-form="embed"]');
                        if (form) {
                            form.addEventListener('submit', (e) => {
                                e.preventDefault();
                                const params = new URLSearchParams(new FormData(form)).toString();
                                loadMatrix(form.getAttribute('action'), params);
                            });
                        }

                        const resetBtn = matrixWrapper.querySelector('.js-matrix-reset');
                        if (resetBtn) {
                            resetBtn.addEventListener('click', () => {
                                const url = resetBtn.dataset.url || trigger.dataset.matrixUrl;
                                loadMatrix(url);
                            });
                        }
                    };

                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.dataset.matrixUrl || this.getAttribute('href');
                        loadMatrix(url);
                    });
                })();
            </script>
        @endpush
    </div>
@endsection
