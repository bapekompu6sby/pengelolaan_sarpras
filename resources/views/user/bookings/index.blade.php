@extends('layout.user_layout')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/datatables/datatables.min.css') }}" rel="stylesheet">
    <style>
        /* ikon & info */
        .icon-brand {
            color: #003A70 !important;
        }

        .info-row {
            align-items: center;
        }

        .info-row.top {
            align-items: flex-start;
        }

        .info-icon {
            flex-shrink: 0;
        }

        /* Kartu */
        .property-card {
            border-radius: 16px;
            overflow: hidden;
            /* cegah elemen di dalam keluar (garis biru gak tembus ke sidebar) */
            transition: transform .22s ease, box-shadow .22s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
            border: 1px solid rgba(0, 0, 0, .06);
        }

        .property-card:hover {
            transform: scale(1.015);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .12);
        }

        /* Frame gambar (hanya frame ini yang ngatur sudut & strip biru) */
        /* Frame gambar tetap kotak & yang motong sudut kiri */
        .img-frame {
            position: relative;
            isolation: isolate;
            width: 100%;
            aspect-ratio: 4 / 3;
            /* mobile */
            overflow: hidden;
            background: #f6f8fc;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
        }

        @media (min-width:768px) {
            .img-frame {
                aspect-ratio: 1 / 1;
                border-radius: 16px 0 0 16px;
            }
        }

        /* PENTING: semua parent carousel harus punya height:100% */
        .img-frame .carousel,
        .img-frame .carousel-inner,
        .img-frame .carousel-item {
            height: 100%;
        }

        /* Paksa gambar selalu penuh (tanpa space) */
        .img-frame .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* penuh, crop jika perlu */
            display: block;
        }

        /* Hilangkan radius di dalam carousel biar sisi kanan tetap rata */
        .img-frame .carousel,
        .img-frame .carousel-inner,
        .img-frame .carousel-item,
        .img-frame .carousel-item img {
            border-radius: 0 !important;
        }

        /* Strip biru: mobile di atas, desktop di kiri */
        .img-frame::before {
            content: "";
            position: absolute;
            z-index: 1;
            left: 0;
            right: 0;
            top: 0;
            height: 10px;
            background: var(--pupr-blue, #003A70);
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .06);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            transition: height .2s ease, width .2s ease, opacity .2s ease;
            pointer-events: none;
        }

        @media (min-width:768px) {
            .img-frame::before {
                top: 0;
                bottom: 0;
                left: 0;
                right: auto;
                width: 10px;
                height: auto;
                border-radius: 16px 0 0 16px;
                box-shadow: inset -1px 0 0 rgba(0, 0, 0, .06);
            }
        }

        /* Layering aman: strip di atas gambar, controls/indicators di atas strip */
        .img-frame .carousel {
            position: relative;
            z-index: 0;
        }

        .img-frame .carousel-indicators,
        .img-frame .carousel-control-prev,
        .img-frame .carousel-control-next {
            z-index: 2;
        }

        /* Hover: hilangkan strip biru */
        .property-card:hover .img-frame::before {
            height: 0;
            opacity: 0;
        }

        @media (min-width:768px) {
            .property-card:hover .img-frame::before {
                width: 0;
                opacity: 0;
            }
        }
    </style>
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

    <div class="p-3">

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h5 class="text-danger">Masukkan data dengan benar</h5>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="card">
                    <div class="table-responsive text-nowrap p-4">
                        <h1 class="h3 fw-bold text-dark mb-4">Peminjaman Ruangan</h1>
                        @foreach ($properties as $property)
                            <div class="card mb-4 shadow-sm property-card">

                                <div class="row g-0">

                                    @php
                                        // Kumpulkan semua slide: cover + galeri
                                        $slides = [];
                                        if (!empty($property->image_path)) {
                                            $slides[] = asset(
                                                'storage/uploads/properties/covers/' . $property->image_path,
                                            );
                                        }
                                        foreach ($property->images as $img) {
                                            $slides[] = asset('storage/uploads/properties/gallery/' . $img->image_path);
                                        }
                                        // Fallback placeholder kalau kosong
                                        if (empty($slides)) {
                                            $slides[] = 'https://placehold.co/800x450?text=No+Image';
                                        }

                                        $carouselId = 'propCarousel-' . $property->id;
                                    @endphp

                                    <div class="col-md-4 d-flex justify-content-center align-items-center">
                                        <div class="w-100">
                                            <div class="img-frame"> {{-- ⬅️ Tambah wrapper --}}
                                                <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel"
                                                    data-bs-interval="3000" data-bs-pause="hover" data-bs-touch="true">

                                                    @if (count($slides) > 1)
                                                        <div class="carousel-indicators">
                                                            @foreach ($slides as $i => $src)
                                                                <button type="button" data-bs-target="#{{ $carouselId }}"
                                                                    data-bs-slide-to="{{ $i }}"
                                                                    @class(['active' => $i === 0])
                                                                    aria-current="{{ $i === 0 ? 'true' : 'false' }}"
                                                                    aria-label="Slide {{ $i + 1 }}"></button>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <div class="carousel-inner">
                                                        @foreach ($slides as $i => $src)
                                                            <div
                                                                class="carousel-item @if ($i === 0) active @endif">
                                                                <img src="{{ $src }}" class="d-block w-100"
                                                                    alt="{{ $property->name ?? 'Property image' }}"
                                                                    @if ($i > 0) loading="lazy" @endif>

                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if (count($slides) > 1)
                                                        <button class="carousel-control-prev" type="button"
                                                            data-bs-target="#{{ $carouselId }}" data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="visually-hidden">Previous</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button"
                                                            data-bs-target="#{{ $carouselId }}" data-bs-slide="next">
                                                            <span class="carousel-control-next-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="visually-hidden">Next</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Konten -->
                                    <div class="col-md-8">
                                        <div class="card-body d-flex flex-column justify-content-between"
                                            style="height: 100%;">
                                            <h2 class="card-title text-wrap text-dark text-break mb-4">
                                                {{ $property->name }}
                                            </h2>

                                            <div class="d-flex flex-column gap-2">

                                                {{-- Tipe --}}
                                                <div class="d-flex info-row text-secondary">
                                                    <i class="bx bx-category fs-4 me-2 icon-brand info-icon"></i>
                                                    <span><strong class="text-dark">Tipe:</strong>
                                                        {{ strtoupper($property->room_type) }}</span>
                                                </div>

                                                {{-- Kapasitas --}}
                                                <div class="d-flex info-row text-secondary">
                                                    <i class="bx bxs-group fs-4 me-2 icon-brand info-icon"></i>
                                                    <span><strong class="text-dark">Kapasitas:</strong>
                                                        ±{{ $property->capacity }}
                                                        orang</span>
                                                </div>

                                                {{-- Luas --}}
                                                <div class="d-flex info-row text-secondary">
                                                    <i class="bx bx-ruler fs-4 me-2 icon-brand info-icon"></i>
                                                    <span><strong class="text-dark">Luas:</strong> {{ $property->area }}
                                                        m<sup>2</sup></span>
                                                </div>

                                                {{-- Fasilitas (top-align karena bisa panjang) --}}
                                                <div class="d-flex info-row top text-secondary">
                                                    <i class="bx bx-list-check fs-4 me-2 icon-brand info-icon mt-1"></i>
                                                    <span class="text-wrap text-break">
                                                        <strong class="text-dark">Fasilitas:</strong>
                                                        {{ $property->facilities }}
                                                    </span>
                                                </div>

                                                {{-- Harga --}}
                                                <div class="d-flex info-row text-secondary">
                                                    <i class="bx bx-money fs-4 me-2 icon-brand info-icon"></i>
                                                    <span><strong class="text-dark">Harga:</strong> Rp
                                                        {{ number_format($property->price, 0, ',', '.') }} / hari</span>
                                                </div>

                                                {{-- Unit / Gedung --}}
                                                @if ($property->type == 'paviliun')
                                                    <div class="d-flex info-row text-secondary">
                                                        <i class="bx bxs-building fs-4 me-2 icon-brand info-icon"></i>
                                                        <span><strong class="text-dark">Jumlah Kamar:</strong>
                                                            {{ $property->unit }}</span>
                                                    </div>
                                                @else
                                                    <div class="d-flex info-row text-secondary ">
                                                        <i class="bx bxs-building fs-4  me-2 icon-brand info-icon"></i>
                                                        <span><strong class="text-dark">Unit:</strong>
                                                            {{ $property->unit }}</span>
                                                    </div>
                                                @endif

                                            </div>

                                            @auth
                                                @if (auth()->user()->role != 'supervisor')
                                                    <button class="btn btn-primary btn-pesan mt-3"
                                                        data-property-id="{{ $property->id }}"
                                                        data-type="{{ $property->type }}" data-bs-toggle="modal"
                                                        data-bs-target="#addEvent">
                                                        Pesan Sekarang
                                                    </button>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

            </div>
        </div>
    </div>


    @include('user.bookings.modal')


@endsection

@section('script')
    <script src="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.js') }}"></script>
    <script>
        function fillSel(id, from, to, step = 1) {
            const s = document.getElementById(id);
            for (let v = from; v <= to; v += step) {
                const opt = document.createElement('option');
                opt.value = opt.textContent = String(v).padStart(2, '0');
                s.appendChild(opt);
            }
        }
        fillSel('h_start', 0, 23);
        fillSel('h_end', 0, 23);
        fillSel('m_start', 0, 59, 1);
        fillSel('m_end', 0, 59, 1);

        function syncHidden() {
            jam_start.value = `${h_start.value}:${m_start.value}`;
            jam_end.value = `${h_end.value}:${m_end.value}`;
        }
        ['h_start', 'm_start', 'h_end', 'm_end'].forEach(id => {
            document.getElementById(id).addEventListener('change', syncHidden);
        });
        syncHidden();
    </script>
    <script>
        const getEvents = async () => {
            const response = await fetch('/api/events');
            const data = await response.json();
            return data;
        }

        document.addEventListener('DOMContentLoaded', async function() {
            var calendarEl = document.getElementById('calendar');
            const btnTrig = document.getElementById('btn-trigger');
            const startDate = document.getElementById('start');
            const endDate = document.getElementById('end');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialDate: new Date(),
                customButtons: {
                    addEventButton: {
                        text: 'Tambah',
                        click: function() {
                            btnTrig.click();
                        }
                    },
                    listEventButton: {
                        text: 'List Kegiatan',
                        click: function() {
                            window.location.href = '/transactions/ruangan/list';
                        }
                    }
                },
                headerToolbar: {
                    left: 'addEventButton listEventButton',
                    center: 'title',
                },
                selectable: true,
                eventClick: function(arg) {
                    console.log(arg.event.title);
                },
                businessHours: true,
                dayMaxEvents: true, // allow "more" link when too many events
                events: await getEvents(),
            });

            calendar.render();
        });

        document.getElementById('checkAvailabilityBtn').addEventListener('click', function() {
            const venueId = document.getElementById('venue').value;
            const startDate = document.getElementById('start').value;
            const endDate = document.getElementById('end').value;
            const unit = document.getElementById('ordered_unit').value;
            const bookBtn = document.getElementById('createTransactionBtn');
            const propertyType = document.getElementById('property_type')?.value || null;
            const jamStart = document.getElementById('jam_start')?.value || null;
            const jamEnd = document.getElementById('jam_end')?.value || null;
            const resultDiv = document.getElementById('availabilityResult');

            // 🧱 Validasi dasar
            if (!venueId || !startDate || !endDate) {
                alert('Pilih tanggal, ruangan, dan jumlah terlebih dahulu.');
                return;
            }

            // ⏰ Tambahan validasi khusus fasilitas
            // if (propertyType === 'fasilitas') {
            //     if (!jamStart || !jamEnd) {
            //         alert('Untuk tipe fasilitas, jam mulai dan jam selesai wajib diisi!');
            //         return;
            //     }
            //     if (jamEnd <= jamStart) {
            //         alert('Jam selesai harus lebih besar dari jam mulai!');
            //         return;
            //     }
            // }

            if (propertyType === 'fasilitas') {
                if (!jamStart || !jamEnd) {
                    alert('Untuk tipe fasilitas, jam mulai dan jam selesai wajib diisi!');
                    return;
                }

                // Ubah jadi menit supaya bisa dibanding lebih akurat
                const [startH, startM] = jamStart.split(':').map(Number);
                const [endH, endM] = jamEnd.split(':').map(Number);
                const startTotal = startH * 60 + startM;
                const endTotal = endH * 60 + endM;

                // Kalau jam selesai lebih kecil, anggap lewat tengah malam
                const durasi = endTotal >= startTotal ?
                    endTotal - startTotal :
                    (24 * 60 - startTotal) + endTotal;

                if (durasi <= 0) {
                    alert('Jam selesai harus lebih besar dari jam mulai!');
                    return;
                }


            }


            // 🚀 Kirim data ke backend
            fetch("{{ route('properties.check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        venue_id: venueId,
                        start_date: startDate,
                        end_date: endDate,
                        ordered_unit: unit,
                        jam_start: jamStart,
                        jam_end: jamEnd,
                        property_type: propertyType
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log('Response:', data);
                    if (data.available) {
                        resultDiv.innerHTML = `
                <span class="text-success">
                    ✅ ${data.avail_count} ${propertyType === 'fasilitas' ? 'slot waktu' : 'ruangan'} tersedia!
                </span>`;
                        bookBtn.disabled = false;
                    } else {
                        resultDiv.innerHTML = `
                <span class="text-danger">
                    ⛔ Tidak tersedia${propertyType === 'fasilitas' ? ' pada jam tersebut' : ' di tanggal ini'}.
                    (${data.avail_count} unit tersisa)
                </span>`;
                        bookBtn.disabled = true;
                    }
                })
                .catch(err => {
                    console.error('Error checking availability:', err);
                    alert('Terjadi kesalahan saat mengecek ketersediaan.');
                });
        });
    </script>
    <script>
        // Kumpulkan semua URL gambar dari response API-mu
        function buildSlidesFromData(data) {
            const slides = [];
            if (data?.property?.image_path) {
                slides.push(`/storage/uploads/properties/covers/${data.property.image_path}`);
            }
            if (Array.isArray(data?.property?.images)) {
                data.property.images.forEach(img => {
                    if (img?.image_path) slides.push(`/storage/uploads/properties/gallery/${img.image_path}`);
                });
            }
            return slides.length ? slides : ['https://placehold.co/800x600?text=No+Image'];
        }

        // Render galeri + indikator + thumbnails
        function renderModalGallery(slides) {
            const inner = document.getElementById('modalGalleryInner');
            const indc = document.getElementById('modalGalleryIndicators');
            const thumbs = document.getElementById('modalGalleryThumbs');
            const gallery = document.getElementById('modalGallery');

            inner.innerHTML = '';
            indc.innerHTML = '';
            thumbs.innerHTML = '';

            slides.forEach((src, i) => {
                inner.insertAdjacentHTML('beforeend',
                    `<div class="carousel-item ${i===0?'active':''}">
           <img src="${src}" alt="slide-${i+1}">
         </div>`
                );
                indc.insertAdjacentHTML('beforeend',
                    `<button type="button" data-bs-target="#modalGallery" data-bs-slide-to="${i}"
                 class="${i===0?'active':''}" ${i===0?'aria-current="true"':''}
                 aria-label="Slide ${i+1}"></button>`
                );
                const th = document.createElement('img');
                th.src = src;
                th.alt = `thumb-${i+1}`;
                if (i === 0) th.classList.add('active');
                th.onclick = () => bootstrap.Carousel.getOrCreateInstance(gallery).to(i);
                thumbs.appendChild(th);
            });

            gallery.addEventListener('slid.bs.carousel', (e) => {
                const idx = e.to;
                thumbs.querySelectorAll('img').forEach((img, j) => img.classList.toggle('active', j === idx));
            }, {
                once: false
            });
        }

        // Isi ringkasan kecil di sisi kiri
        function renderModalSummary(data) {
            const p = data?.property ?? {};
            document.getElementById('tpSumName').textContent = p.name ?? '—';
            document.getElementById('tpSumType').textContent = (p.type ?? '—').toUpperCase();
            const cap = p.capacity ? `± ${Number(p.capacity).toLocaleString('id-ID')} orang` : '—';
            document.getElementById('tpSumCapacity').textContent = `Kapasitas ${cap}`;

            document.getElementById('tpSumArea').textContent = `Luas ${p.area ?? '—'} m²`;
            document.getElementById('tpSumFacilities').textContent = p.facilities ?? '—';
            const price = (parseInt(p.price) || 0).toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
            document.getElementById('tpSumPrice').textContent = price;
        }
    </script>

    <script>
        // Fungsi hitung total harga
        function calculateTotal(pricePerDay) {
            const afiliasi = document.getElementById('affiliation').value;
            const modal = document.getElementById('addEvent');
            const start = new Date(modal.querySelector('#start').value);
            const end = new Date(modal.querySelector('#end').value);
            const unit = parseInt(modal.querySelector('#ordered_unit').value) || 1;

            if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start || afiliasi === 'internal_pu') {
                document.getElementById('total_price').innerText = 'Rp 0';
                return;
            }

            // Hitung selisih hari + 1 (termasuk hari pertama)
            const diffTime = end - start;
            const diffDays = diffTime / (1000 * 60 * 60 * 24) + 1;

            const total = diffDays * pricePerDay * unit;

            // Format ke rupiah
            document.getElementById('total_price').innerText = total.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            // Update input hidden untuk dikirim ke server
            document.getElementById('total_harga_input').value = total;

        }

        // Pasang event listener pada input yang akan mempengaruhi harga
        document.getElementById('start').addEventListener('change', () => {
            if (window.currentPrice) calculateTotal(window.currentPrice);
        });
        document.getElementById('end').addEventListener('change', () => {
            if (window.currentPrice) calculateTotal(window.currentPrice);
        });
        document.getElementById('ordered_unit').addEventListener('input', () => {
            if (window.currentPrice) calculateTotal(window.currentPrice);
        });
        document.getElementById('affiliation').addEventListener('change', () => {
            if (window.currentPrice) calculateTotal(window.currentPrice);
        });


        // Saat fetch data properti selesai dan modal terbuka, set harga per hari dan hitung total awal
        document.querySelectorAll('.btn-pesan').forEach(button => {
            button.addEventListener('click', function() {
                const propertyId = this.getAttribute('data-property-id');

                fetch(`/api/properties/${propertyId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);

                        // Simpan harga per hari ke global variable
                        window.currentPrice = parseInt(data.property.price) || 0;

                        const modal = document.getElementById('addEvent');
                        const venueSelect = modal.querySelector('#venue');
                        venueSelect.value = data.property.id;

                        const modalTitle = modal.querySelector('.modal-title');
                        modalTitle.textContent = 'Pesan Ruangan: ' + data.property.name;

                        // Set data properti
                        modal.querySelector('#venue_name').value = data.property.name;
                        modal.querySelector('#venue').value = data.property.id;

                        // 🧠 Simpan tipe properti ke hidden input (biar dikirim ke server)
                        let typeInput = modal.querySelector('#property_type');
                        if (!typeInput) {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.id = 'property_type';
                            hidden.name = 'property_type';
                            modal.querySelector('form').appendChild(hidden);
                            typeInput = hidden;
                        }
                        typeInput.value = data.property.type;

                        // ✅ (TIDAK ADA LAGI IF FASILITAS)
                        // Field jam sudah di-render dari HTML langsung — hanya perlu toggle tampilannya kalau mau

                        const jamContainer = modal.querySelector('#jamFieldsContainer');
                        if (jamContainer) {
                            // Kalau kamu mau otomatis sembunyikan kalau bukan fasilitas (opsional)
                            jamContainer.classList.toggle('d-none', data.property.type !== 'fasilitas');
                        }

                        // Isi data user
                        modal.querySelector('#name').value = data.user.name || '';
                        modal.querySelector('#email').value = data.user.email || '';
                        modal.querySelector('#phone_number').value = data.user.phone_number || '';

                        // Reset input tanggal & unit
                        modal.querySelector('#start').value = '';
                        modal.querySelector('#end').value = '';
                        modal.querySelector('#ordered_unit').value = 1;

                        // Render gambar dan ringkasan
                        const slides = buildSlidesFromData(data);
                        renderModalGallery(slides);
                        renderModalSummary(data);

                        // Reset total harga
                        document.getElementById('total_price').innerText = 'Rp 0';
                    })
                    .catch(error => {
                        console.error('Error fetching property/user data:', error);
                    });
            });
        });
    </script>
@endsection
