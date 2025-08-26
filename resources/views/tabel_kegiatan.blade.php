@extends('layout.index')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.nav')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/driver.css') }}">
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

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Dashboard /</span> Kegiatan
        </h4>

        <div class="card" id="kegiatan">
            <div class="card-body">
                <div class="card-title">
                    <h4 class="text-nowrap mb-3">
                        <span class="badge bg-label-success me-2">
                            <i class="bx bx-task"></i>
                        </span>
                        Daftar Kegiatan
                    </h4>
                </div>

                <div class="table-responsive">
                    <table id="datatable-kegiatan" class="display table table-hover mb-3 align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Instansi</th>
                                <th>Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Penanggung Jawab</th>
                                <th>Kontak</th>
                                <th>Ruangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $e)
                                <tr>
                                    <td>{{ $e->instansi }}</td>
                                    <td>
                                        <strong>{{ ucfirst($e->kegiatan) }}</strong><br>
                                        <small class="text-muted">{{ $e->description }}</small>
                                    </td>
                                    <td>{{ date('d-m-Y', strtotime($e->start)) }} s/d
                                        {{ date('d-m-Y', strtotime($e->end)) }}</td>
                                    <td>{{ $e->name }}</td>
                                    <td>{{ $e->phone_number }}</td>
                                    <td>{{ $e->properties->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada kegiatan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            "use strict";

            // Tabel kegiatan
            $("#datatable-kegiatan").DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [
                    [2, "asc"]
                ], // urutkan default berdasarkan tanggal
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Tidak ada hasil yang cocok",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari total _MAX_ data)",
                },
                columnDefs: [{
                        targets: [1],
                        className: "fw-semibold"
                    }, // bold kolom kegiatan
                    {
                        targets: [4],
                        className: "text-nowrap"
                    }, // no HP tidak kepotong
                ],
            });
        });
    </script>
@endsection
