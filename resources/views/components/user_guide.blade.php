@extends('layout.index')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.nav')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/vendor/libs/select2/css/select2.min.css') }}" rel="stylesheet">
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

    <div class="container-xxl flex-grow-1 container-p-y">

        @auth
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Buku Panduan /</span>
                {{ auth()->user()->role == 'admin' ? 'Admin' : 'Pengguna' }}
            </h4>
        @endauth

        @guest
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Buku Panduan /</span> Pengunjung
            </h4>
        @endguest

        @auth
            @if (auth()->user()->role == 'admin')
                <div class="row">
                    <!-- Panduan Sumber Informasi Kegiatan -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-primary border-5 border-top-0 border-bottom-0 border-end-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Panduan Sumber Informasi Kegiatan</h5>
                                <p class="card-text flex-grow-1">
                                    Panduan ini berisi informasi terkait sumber data kegiatan yang dapat diakses
                                    melalui sistem, termasuk cara melihat, mencari, dan memahami detail kegiatan.
                                </p>
                                <a href="{{ asset('storage/user_guide/Sumber_Informasi_kegiatan.pdf') }}"
                                    class="btn btn-outline-primary mt-auto" target="_blank">
                                    Lihat Panduan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Admin - Management Ruangan -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-danger border-5 border-top-0 border-bottom-0 border-end-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Panduan Admin: Management Ruangan</h5>
                                <p class="card-text flex-grow-1">
                                    Panduan untuk admin dalam mengelola data ruangan, menambah, mengubah,
                                    atau menghapus ruangan, serta memastikan informasi ketersediaan ruangan tetap akurat.
                                </p>
                                <a href="{{ asset('storage/user_guide/admin_management_ruangan.pdf') }}"
                                    class="btn btn-outline-danger mt-auto" target="_blank">
                                    Lihat Panduan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Admin - Management Peminjaman Ruangan -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-success border-5 border-top-0 border-bottom-0 border-end-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Panduan Admin: Management Peminjaman Ruangan</h5>
                                <p class="card-text flex-grow-1">
                                    Panduan ini menjelaskan langkah-langkah admin dalam mengelola peminjaman ruangan,
                                    mulai dari persetujuan, verifikasi, hingga monitoring riwayat transaksi peminjaman.
                                </p>
                                <a href="{{ asset('storage/user_guide/admin_management_peminjaman_ruangan.pdf') }}"
                                    class="btn btn-outline-success mt-auto" target="_blank">
                                    Lihat Panduan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <!-- Panduan Sumber Informasi Kegiatan -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-primary border-5 border-top-0 border-bottom-0 border-end-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Panduan Sumber Informasi Kegiatan</h5>
                                <p class="card-text flex-grow-1">
                                    Panduan ini berisi informasi terkait sumber data kegiatan yang dapat diakses
                                    melalui sistem, termasuk cara melihat, mencari, dan memahami detail kegiatan.
                                </p>
                                <a href="{{ asset('storage/user_guide/Sumber_Informasi_kegiatan.pdf') }}"
                                    class="btn btn-outline-primary mt-auto" target="_blank">
                                    Lihat Panduan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Pengunjung (Mobile) -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-warning border-5 border-top-0 border-bottom-0 border-end-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Panduan Pengunjung (Mobile)</h5>
                                <p class="card-text flex-grow-1">
                                    Panduan ini menjelaskan cara penggunaan website melalui perangkat mobile,
                                    mulai dari pemesanan ruangan hingga pengecekan riwayat peminjaman.
                                </p>
                                <a href="{{ asset('storage/user_guide/pengunjung_mobile.pdf') }}"
                                    class="btn btn-outline-warning mt-auto" target="_blank">
                                    Lihat Panduan
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Panduan Pengunjung (Desktop) -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border border-success border-5 border-top-0 border-bottom-0 border-end-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Panduan Pengunjung (Desktop)</h5>
                                <p class="card-text flex-grow-1">
                                    Panduan ini berisi langkah-langkah penggunaan website melalui perangkat desktop,
                                    mencakup proses login, peminjaman ruangan, hingga manajemen riwayat peminjaman.
                                </p>
                                <a href="{{ asset('storage/user_guide/pengunjung_dekstop.pdf') }}"
                                    class="btn btn-outline-success mt-auto" target="_blank">
                                    Lihat Panduan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        @guest
            <div class="row">
                <!-- Panduan Sumber Informasi Kegiatan -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border border-primary border-5 border-top-0 border-bottom-0 border-end-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Panduan Sumber Informasi Kegiatan</h5>
                            <p class="card-text flex-grow-1">
                                Panduan ini berisi informasi terkait sumber data kegiatan yang dapat diakses
                                melalui sistem, termasuk cara melihat, mencari, dan memahami detail kegiatan.
                            </p>
                            <a href="{{ asset('storage/user_guide/Sumber_Informasi_kegiatan.pdf') }}"
                                class="btn btn-outline-primary mt-auto" target="_blank">
                                Lihat Panduan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Panduan Pengunjung (Mobile) -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border border-warning border-5 border-top-0 border-bottom-0 border-end-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Panduan Pengunjung (Mobile)</h5>
                            <p class="card-text flex-grow-1">
                                Panduan ini menjelaskan cara penggunaan website melalui perangkat mobile,
                                mulai dari pemesanan ruangan hingga pengecekan riwayat peminjaman.
                            </p>
                            <a href="{{ asset('storage/user_guide/pengunjung_mobile.pdf') }}"
                                class="btn btn-outline-warning mt-auto" target="_blank">
                                Lihat Panduan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Panduan Pengunjung (Desktop) -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border border-success border-5 border-top-0 border-bottom-0 border-end-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Panduan Pengunjung (Desktop)</h5>
                            <p class="card-text flex-grow-1">
                                Panduan ini berisi langkah-langkah penggunaan website melalui perangkat desktop,
                                mencakup proses login, peminjaman ruangan, hingga manajemen riwayat peminjaman.
                            </p>
                            <a href="{{ asset('storage/user_guide/pengunjung_dekstop.pdf') }}"
                                class="btn btn-outline-success mt-auto" target="_blank">
                                Lihat Panduan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @endguest

    </div>
@endsection
