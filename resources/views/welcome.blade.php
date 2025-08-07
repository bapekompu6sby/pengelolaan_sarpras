@extends('layout.index')
@section('sidebar')
@include('layout.sidebar')
@endsection
@section('nav')
@include('layout.nav')
@endsection
@section('content')

@section('head')
<link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/assets/vendor/css/driver.css') }}">
@endsection

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard/</span> Dashboard</h4>
    <div class="row">
        <div class="col-lg-6  mb-4 order-0">
            <div class="card" id="kegiatan">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="text-nowrap mb-2">
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
                                    <th>Event</th>
                                    <th>Tanggal</th>
                                    <th>Ruangan</th>
                                </tr>
                            </thead>
                            @foreach ( $events as $e )
                            <tbody>
                                <tr>
                                    <td>{{ ucfirst($e->kegiatan) }}</td>
                                    <td>{{ date("d-m-Y", strtotime($e->start)) . " | " . date("d-m-Y", strtotime($e->end))
                                        }}</td>
                                    <td>{{ $e->properties->name }}</td>
                                </tr>
                            </tbody>
                            @endforeach
                        </table>
                    </div>
                    @auth
                        @if (Auth::user()->role == 'admin')
                        <a href="{{ route('ruangan.detail') }}" class="btn btn-success" id="more-btn">More</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 mb-4">
            <div class="card" id="paviliun-sum">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="text-nowrap mb-2">
                            <span class="badge bg-label-danger me-2">
                                <i class="bx bx-buildings"></i>
                            </span>
                            Paviliun
                        </h4>
                    </div>
                    <div class="capacity">
                        <div class="d-flex justify-content-evenly flex-wrap">
                            <div class="left">
                                <span class="d-block mb-1">Terisi</span>
                                <h4 class="card-title text-nowrap mb- text-danger text-center">{{ $paviliun }}</h4>
                            </div>
                            <div class="right">
                                <span class="d-block mb-1">Kosong</span>
                                <h4 class="card-title text-nowrap mb-2 text-success text-center">{{ $paviliunAvailable
                                    }}</h4>
                            </div>
                            <div class="bottom">
                                <span class="d-block mb-1">Antrian</span>
                                <h4 class="card-title text-nowrap mb-2 text-warning text-center">{{ $paviliunQueue
                                    }}</h4>
                            </div>
                        </div>
                        @auth
                            @if(Auth::user()->role == 'admin')
                            <a href="{{ route('wisma-admin') }}" class="btn btn-danger" id="more-btn2">More</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-3 mb-4">
            <div class="card" id="asrama-sum">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="text-nowrap mb-2">
                            <span class="badge bg-label-warning me-2">
                                <i class="bx bx-home"></i>
                            </span>
                            Asrama
                        </h4>
                    </div>
                    <div class="capacity">
                        <div class="d-flex justify-content-evenly flex-wrap">
                            <div class="left">
                                <span class="d-block mb-1">Terisi</span>
                                <h4 class="card-title text-nowrap mb- text-danger text-center">{{ $wisma }}</h4>
                            </div>
                            <div class="right">
                                <span class="d-block mb-1">Kosong</span>
                                <h4 class="card-title text-nowrap mb-2 text-success text-center">{{ $wismaAvailable }}
                                </h4>
                            </div>
                            <div class="bottom">
                                <span class="d-block mb-1">Antrian</span>
                                <h4 class="card-title text-nowrap mb-2 text-warning text-center">{{ $wismaQueue }}
                                </h4>
                            </div>
                        </div>
                        @auth
                            @if(Auth::user()->role == 'admin')
                            <a href="{{ route('wisma-admin') }}" class="btn btn-warning" id="more-btn3">More</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-4 order-0">
            <!-- check if carousel is not empty -->
            @if (count($events) != 0)
            <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ( $events as $key => $e )
                    <div class="carousel-item {{ ($key == 0)? 'active' : '' }}">
                        <div class="card">
                            <div class="d-flex align-items-end row">
                                <div class="col p-5">
                                    <div class="card-body text-center">
                                        <h1 class="text-dark">{{ ucfirst($e->kegiatan) }}</h1>
                                        <h5 class="card-title text-primary">{{ date("d-m-Y", strtotime($e->start)) }} |
                                            {{ date("d-m-Y", strtotime($e->end)) }}</h5>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="text-dark mb-1">{{ $e->instansi }}</h6>
                                            <h6 class="text-dark mb-1">{{ $e->properties->name }}</h6>
                                        </div>
                                        <p class="mb-4 text-start">
                                            <b>Deskripsi: </b>
                                            {{ $e->description }}
                                        </p>
                                    </div>
                                    <a href="{{ route('ruangan.detail') }}" class="btn btn-primary">Lihat
                                        selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#carouselExample" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-primary" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExample" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-primary" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
            @endif
        </div>
        <div class="col-lg-6 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="text-nowrap mb-2">
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-calendar"></i>
                            </span>
                            Kalender
                        </h4>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
@auth
    @if (Auth::user()->role == 'admin')
        @include('components.demo-btn')
    @endif
@endauth

@endsection

@section('script')
<script src="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/driver.js.iife.js') }}"></script>
<script src="{{ asset('/assets/js/init-driver.js') }}"></script>
<script>
    const venue = document.getElementById('venue');

    const getEvents = async () => {
        const response = await fetch('/api/events');
        const data = await response.json();
        return data;
    }

    document.addEventListener('DOMContentLoaded', async function () {
        var calendarEl = document.getElementById('calendar');
        const btnTrig = document.getElementById('btn-trigger');
        const startDate = document.getElementById('start');
        const endDate = document.getElementById('end');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialDate: new Date(),
            events: await getEvents(),
        });

        calendar.render();
    });
</script>
@endsection
