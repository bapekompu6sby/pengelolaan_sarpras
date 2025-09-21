@extends('layout.user_layout')

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

    <div class="container-fluid flex-grow-1 p-0">
        <div class="row g-0">
            <div class="col-12 px-3 py-3">

                <div class="card card-modern">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-calendar-event fs-4 text-brand"></i>
                            <h5 class="mb-0">Riwayat Peminjaman</h5>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if (Auth::user()->role == 'admin')
                                <a href="{{ route('transactions.ruangan.show') }}" class="btn btn-primary btn-modern"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Buat Peminjaman">
                                    <span class="tf-icons bx bx-plus-medical bx-sm"></span>
                                </a>
                            @endif

                            <a href="{{ route('transactions.ruangan.export') }}" class="btn btn-success btn-modern"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Export ke Excel">
                                <span class="tf-icons bx bx-cloud-download bx-sm"></span>
                            </a>
                        </div>
                    </div>

                    {{-- Desktop --}}
                    <div class="table-responsive text-nowrap p-4 d-none d-md-block">
                        <table id="datatable2" class="table table-modern table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    @if (Auth::user()->role == 'admin')
                                        <th style="width: 10px">✔</th>
                                    @endif
                                    <th>Nama Pemesan</th>
                                    <th>Instansi</th>
                                    <th>Kegiatan</th>
                                    <th>Ruangan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
                                        <td>{{ $t->properties->name }}</td>
                                        <td>{{ date('d-m-Y', strtotime($t->start)) }} |
                                            {{ date('d-m-Y', strtotime($t->end)) }}</td>

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
                                            <button class="btn btn-primary btn-sm btn-modern" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailAsUser{{ $t->id }}">
                                                <i class="bx bx-detail me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="d-block d-md-none p-3">
                        @forelse ($transactions as $t)
                            @php
                                // Tentukan sumber gambar (URL absolut, file lokal di /public/uploads, atau placeholder)
                                $imgPath = $t->properties->image_path ?? '';
                                $isUrl = \Illuminate\Support\Str::startsWith($imgPath, ['http://', 'https://']);
                                $local = public_path('uploads/' . ltrim($imgPath, '/'));
                                $img = $imgPath
                                    ? ($isUrl
                                        ? $imgPath
                                        : (file_exists($local)
                                            ? asset('uploads/' . ltrim($imgPath, '/'))
                                            : 'https://placehold.co/800x450?text=No+Image'))
                                    : 'https://placehold.co/800x450?text=No+Image';
                            @endphp

                            <div class="card card-modern mb-3 ring-1">
                                <div class="card-body p-3">
                                    {{-- Header: Nama & Ruangan --}}
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="fw-semibold mb-0">{{ $t->name }}</h6>
                                        <span class="small text-muted">{{ $t->properties->name }}</span>
                                    </div>

                                    {{-- Gambar (rasio 16:9, cover) --}}
                                    <div class="ratio ratio-16x9 mt-2 rounded-3 overflow-hidden">
                                        <img src="{{ $img }}" class="w-100 h-100 object-fit-cover"
                                            alt="{{ $t->properties->name ?? 'No image' }}">
                                    </div>

                                    {{-- Detail singkat --}}
                                    <div class="mt-3 small">
                                        <div><strong>Instansi:</strong> {{ ucfirst($t->instansi) }}</div>
                                        <div><strong>Kegiatan:</strong> {{ $t->kegiatan }}</div>
                                        <div><strong>Tanggal:</strong> {{ date('d-m-Y', strtotime($t->start)) }} –
                                            {{ date('d-m-Y', strtotime($t->end)) }}</div>

                                        <div class="mt-2">
                                            <strong>Status:</strong>
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
                                        </div>
                                    </div>

                                    {{-- Tombol full-width --}}
                                    <div class="d-grid mt-3">
                                        <button class="btn btn-primary btn-sm btn-modern w-100"
                                            data-bs-toggle="modal" data-bs-target="#modalDetailAsUser{{ $t->id }}">
                                            <i class="bx bx-detail me-1"></i> Detail
                                        </button>
                                    </div>
                                </div>
                            </div>


                        @empty
                            <div class="text-center text-muted">Belum ada peminjaman.</div>
                        @endforelse
                    </div>

                    {{-- Taruh semua modal detail di sini, sekali per transaksi --}}
                    @foreach ($transactions as $t)
                        @include('user.transactions.modal', ['t' => $t])
                    @endforeach

                </div>

            </div>
        </div>
    </div>
@endsection
