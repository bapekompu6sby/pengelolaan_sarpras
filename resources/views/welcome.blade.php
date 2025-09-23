@extends('layout.auth_layout')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/driver.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">{{ session('success') }}</x-toast>
    @endif
    @if (session('failed'))
        <x-toast bgColor="bg-danger" title="Failed">{{ session('failed') }}</x-toast>
    @endif

    <div class="p-3">

        {{-- Row 1: Kegiatan + Ringkasan Stok --}}
        <div class="row g-3">
            {{-- Kegiatan --}}
            <div class="col-lg-6 mb-4">
                <div class="card" id="kegiatan">
                    <div class="card-body">
                        <div class="mb-3">
                            <h1 class="h3 fw-bold text-dark mb-4">Dashboard</h1>
                            <h4 class="text-dark mb-0">
                                <span class="badge bg-label-success me-2">
                                    <i class="bx bx-task"></i>
                                </span>
                                Kegiatan
                            </h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-3">
                                <thead>
                                    <tr>
                                        <th>Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $e)
                                        <tr>
                                            <td class="text-break">{{ ucfirst($e->kegiatan) }}</td>
                                            <td>{{ date('d-m-Y', strtotime($e->start)) }} |
                                                {{ date('d-m-Y', strtotime($e->end)) }}</td>
                                            <td>{{ $e->properties->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ route('bookings') }}" class="btn btn-success  w-100">Selengkapnya</a>
                    </div>
                </div>
            </div>

            {{-- Ringkasan stok per jenis --}}
            <div class="col-lg-6">
                <div class="col-12 mb-3">
                    <div class="card border-0 rounded-3">
                        <div class="card-body text-center py-2">
                            <span class="fw-bold text-primary fs-5">Ruangan Per Hari Ini</span>
                        </div>
                    </div>
                </div>

                <div class="row gx-3 gy-3 mb-3">

                    {{-- Helper: bikin array biar DRY --}}
                    @php
                        $summary = [
                            [
                                'label' => 'Aula',
                                'badgeClass' => 'bg-label-primary',
                                'stock' => $aulaStock,
                                'available' => $availableAula,
                                'occupied' => $aulaNotAvailable,
                                'btnClass' => 'btn-primary',
                            ],
                            [
                                'label' => 'Kelas',
                                'badgeClass' => 'bg-label-warning',
                                'stock' => $kelasStock,
                                'available' => $availableKelas,
                                'occupied' => $kelasNotAvailable,
                                'btnClass' => 'btn-warning',
                            ],
                            [
                                'label' => 'Asrama',
                                'badgeClass' => 'bg-label-success',
                                'stock' => $asramaStock,
                                'available' => $availableAsrama,
                                'occupied' => $asramaNotAvailable,
                                'btnClass' => 'btn-success',
                            ],
                            [
                                'label' => 'Paviliun',
                                'badgeClass' => 'bg-label-secondary', // pakai kelas, bukan inline style
                                'stock' => $paviliunStock,
                                'available' => $availablePaviliun,
                                'occupied' => $paviliunNotAvailable,
                                'btnClass' => 'btn-secondary',
                            ],
                        ];
                    @endphp

                    @foreach ($summary as $i => $s)
                        <div class="col-12 col-md-6">
                            <div class="card compact-card" id="summary-{{ Str::slug($s['label']) }}">
                                <div class="card-body compact-body">
                                    <h4 class="text-dark mb-2">
                                        <span class="badge {{ $s['badgeClass'] }} me-2">
                                            <i class="bx bx-buildings"></i>
                                        </span>
                                        {{ $s['label'] }}
                                    </h4>

                                    <div class="d-flex justify-content-evenly flex-wrap text-center">
                                        <div>
                                            <span class="d-block mb-1">Kapasitas</span>
                                            <h4 class="mb-0 text-danger">{{ $s['stock'] }}</h4>
                                        </div>
                                        <div>
                                            <span class="d-block mb-1">Kosong</span>
                                            <h4 class="mb-0 text-success">{{ $s['available'] }}</h4>
                                        </div>
                                        <div>
                                            <span class="d-block mb-1">Terisi</span>
                                            <h4 class="mb-0 text-warning">{{ $s['occupied'] }}</h4>
                                        </div>
                                    </div>

                                    <a href="{{ route('bookings') }}" class="btn {{ $s['btnClass'] }} mt-3 w-100">
                                        Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- Row 2: Carousel + Kalender --}}
        <div class="row g-3">
            <div class="col-lg-6 mb-4 order-0"> <!-- check if carousel is not empty -->
                @if (count($events) != 0)
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach ($events as $key => $e)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <div class="card">
                                        <div class="d-flex align-items-end row g-3">
                                            <div class="col p-5" style="margin-left: 10px; margin-right: 10px;">
                                                <div class="card-body text-center mx-2">
                                                    <h1 class="text-dark">{{ ucfirst($e->kegiatan) }}</h1>
                                                    <h5 class="card-title text-primary">
                                                        {{ date('d-m-Y', strtotime($e->start)) }} |
                                                        {{ date('d-m-Y', strtotime($e->end)) }}</h5>
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="text-dark mb-1">{{ $e->instansi }}</h6>
                                                        <h6 class="text-dark mb-1">{{ $e->properties->name }} </h6>
                                                    </div>
                                                    <p class="mb-4 text-start"> <b>Deskripsi: </b> {{ $e->description }}
                                                    </p>
                                                </div> <a href="{{ route('ruangan.detail') }}"
                                                    class="btn btn-primary w-100">Lihat selengkapnya</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div> <a class="carousel-control-prev" href="#carouselExample" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-primary" aria-hidden="true"></span> <span
                                class="visually-hidden">Previous</span> </a> <a class="carousel-control-next"
                            href="#carouselExample" role="button" data-bs-slide="next"> <span
                                class="carousel-control-next-icon bg-primary" aria-hidden="true"></span> <span
                                class="visually-hidden">Next</span> </a>
                    </div>
                @endif
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-dark mb-2">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-calendar"></i>
                            </span>
                            Kalender
                        </h4>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/js/driver.js.iife.js') }}"></script>
    <script src="{{ asset('/assets/js/init-driver.js') }}"></script>
    <script>
        const getEvents = async () => {
            const res = await fetch('/api/events');
            return await res.json();
        };

        document.addEventListener('DOMContentLoaded', async function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialDate: new Date(),
                events: await getEvents(),
            });
            calendar.render();
        });
    </script>
@endsection
