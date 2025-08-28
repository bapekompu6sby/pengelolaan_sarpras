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
    <link href="{{ asset('/assets/css/kegiatan.css') }}" rel="stylesheet">
@endsection

@section('content')
    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">{{ session('success') }}</x-toast>
    @endif
    @if (session('failed'))
        <x-toast bgColor="bg-danger" title="Failed">{{ session('failed') }}</x-toast>
    @endif

    @php
        // ===== Helper: Format rentang tanggal konsisten lokal =====
        function tanggalRangeID($start, $end){
            $s = \Carbon\Carbon::parse($start); $e = \Carbon\Carbon::parse($end);
            if ($s->isSameDay($e)) return $s->translatedFormat('d M Y');
            if ($s->isSameMonth($e) && $s->isSameYear($e)) return $s->translatedFormat('d').'–'.$e->translatedFormat('d M Y');
            return $s->translatedFormat('d M Y').' — '.$e->translatedFormat('d M Y');
        }
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-3"><span class="text-muted fw-light">Dashboard /</span> Kegiatan</h4>

        {{-- ===================== SLIDER DI ATAS TABEL ===================== --}}
        <div class="card mb-4" aria-labelledby="sliderTitle">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 id="sliderTitle" class="mb-0">
                        <span class="badge bg-label-primary me-2"><i class='bx bx-slideshow'></i></span>
                        Kegiatan Hari Ini
                    </h5>
                </div>

                @php $count = $events->count(); @endphp

                @if ($count > 0)
                    <div id="kegiatanSlider" class="kegiatan-slider" role="region" aria-label="Slider kegiatan hari ini" aria-live="polite">
                        <div class="progress" aria-hidden="true"><div class="bar" id="sliderProgress"></div></div>

                        <div class="slides" id="sliderTrack" style="transform: translateX(0%)">
                            @foreach ($events as $idx => $e)
                                @php $rowId = 'row-'.($e->id ?? Str::slug(($e->kegiatan ?? 'kegiatan').'-'.($e->start ?? ''))); @endphp
                                <article class="slide" data-index="{{ $idx }}" tabindex="0" aria-roledescription="slide" aria-label="Slide {{ $idx+1 }} dari {{ $count }}">
                                    <div class="slide-content">
                                        <span class="instansi"><i class='bx bxs-buildings'></i> {{ $e->instansi ?? '-' }}</span>
                                        <div class="kegiatan-title">{{ ucfirst($e->kegiatan) }}</div>
                                        <div class="tanggal">
                                            <i class='bx bx-calendar'></i>
                                            {{ tanggalRangeID($e->start, $e->end) }}
                                        </div>
                                        <div class="meta">
                                            <span><i class='bx bx-map'></i> {{ $e->properties->name ?? '-' }}</span>
                                        </div>
                                        <div class="cta-link">
                                            <a href="#{{ $rowId }}" class="btn btn-sm btn-outline-primary">Lihat di tabel</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <button class="nav prev" type="button" id="btnPrev" aria-label="Slide sebelumnya"><i class='bx bx-chevron-left'></i></button>
                        <button class="nav next" type="button" id="btnNext" aria-label="Slide berikutnya"><i class='bx bx-chevron-right'></i></button>

                        <div class="dots" id="sliderDots" aria-label="Indikator slide">
                            @for ($i = 0; $i < $count; $i++)
                                <button class="dot {{ $i === 0 ? 'active' : '' }}" type="button" data-index="{{ $i }}" aria-label="Ke slide {{ $i+1 }}"></button>
                            @endfor
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-0" role="alert">
                        <i class='bx bx-info-circle'></i> Tidak ada kegiatan terjadwal untuk hari ini.
                    </div>
                @endif
            </div>
        </div>
        {{-- =================== END SLIDER =================== --}}

        <div class="card" id="kegiatan">
            <div class="card-body">
                <div class="card-title">
                    <h4 class="text-nowrap mb-3">
                        <span class="badge bg-label-success me-2"><i class="bx bx-task"></i></span>
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
                                @php $rowId = 'row-'.($e->id ?? Str::slug(($e->kegiatan ?? 'kegiatan').'-'.($e->start ?? ''))); @endphp
                                <tr id="{{ $rowId }}">
                                    <td>{{ $e->instansi }}</td>
                                    <td>
                                        <strong>{{ ucfirst($e->kegiatan) }}</strong><br>
                                        <small class="text-muted">{{ $e->description }}</small>
                                    </td>
                                    <td>{{ tanggalRangeID($e->start, $e->end) }}</td>
                                    <td>{{ $e->name }}</td>
                                    <td class="text-nowrap">{{ $e->phone_number }}</td>
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
    <script src="{{ asset('/assets/js/kegiatan.js') }}"></script>
@endsection
