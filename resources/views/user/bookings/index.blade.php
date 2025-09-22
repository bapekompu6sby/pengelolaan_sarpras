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

        /* Kartu properti */
        .property-card {
            border-radius: 16px;
            overflow: hidden;
            transition: transform .22s ease, box-shadow .22s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
            will-change: transform;
            border: 1px solid rgba(0, 0, 0, .06);
        }

        .property-card:hover {
            transform: scale(1.015);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .12);
        }

        /* 📱 MOBILE (default) - gambar 4:3, rounded atas, tanpa shadow */
        .img-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 3;
            border-radius: 16px 16px 0 0;
            overflow: hidden;
            background: #f6f8fc;
            box-shadow: none;
            border-bottom: none;
        }

        .img-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .22s ease;
        }

        /* Bar biru: MOBILE di ATAS */
        .img-frame::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 6px;
            background: var(--pupr-blue, #003A70);
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .06);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            transition: height .22s ease, width .22s ease, opacity .22s ease;
        }

        /* 💻 DESKTOP/TABLET (≥768px) - 1:1, rounded kiri saja, bar di kiri */
        @media (min-width: 768px) {
            .img-frame {
                aspect-ratio: 1 / 1;
                border-radius: 16px 0 0 16px;
                /* ⬅️ kiri atas & kiri bawah bulat, kanan rata */
                box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
            }

            .img-frame::before {
                top: 0;
                bottom: 0;
                left: 0;
                right: auto;
                height: auto;
                width: 6px;
                /* bar di kiri */
                box-shadow: inset -1px 0 0 rgba(0, 0, 0, .06);
                border-top-left-radius: 16px;
                border-bottom-left-radius: 16px;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }
        }


        /* Hover: bar menghilang + foto zoom */
        .property-card:hover .img-frame::before {
            height: 0;
            opacity: 0;
        }

        /* mobile */
        @media (min-width:768px) {
            .property-card:hover .img-frame::before {
                width: 0;
                opacity: 0;
            }

            /* desktop */
        }

        .property-card:hover .img-frame img {
            transform: scale(1.05);
        }

        /* Reduce motion */
        @media (prefers-reduced-motion:reduce) {

            .property-card,
            .img-frame::before,
            .img-frame img {
                transition: none;
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

                                    <div class="col-md-4 d-flex justify-content-center align-items-center">
                                        <div class="img-frame">
                                            <img src="{{ $property->image_path ? asset('uploads/' . $property->image_path) : 'https://placehold.co/400?text=No+Image' }}"
                                                alt="{{ $property->name ?? 'No image' }}">
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
                                                <div class="d-flex info-row text-secondary ">
                                                    <i class="bx bxs-building fs-4  me-2 icon-brand info-icon"></i>
                                                    <span><strong class="text-dark">Unit:</strong>
                                                        {{ $property->unit }}</span>
                                                </div>

                                            </div>

                                            @auth
                                                @if (auth()->user()->role != 'supervisor')
                                                    <button class="btn btn-primary btn-pesan mt-3"
                                                        data-property-id="{{ $property->id }}" data-bs-toggle="modal"
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
            let venueId = document.getElementById('venue').value;
            let startDate = document.getElementById('start').value;
            let endDate = document.getElementById('end').value;
            let unit = document.getElementById('ordered_unit').value;
            let bookBtn = document.getElementById('createTransactionBtn');

            if (!venueId || !startDate || !endDate) {
                alert('Pilih tanggal, ruangan, dan jumlah terlebih dahulu.');
                return;
            }

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
                        ordered_unit: unit
                    })
                })
                .then(res => res.json())
                .then(data => {
                    let resultDiv = document.getElementById('availabilityResult');
                    if (data.available) {
                        resultDiv.innerHTML =
                            `<span class="text-success">✅ ${data.avail_count} Ruangan Tersedia!</span>`;
                        bookBtn.disabled = false;
                    } else {
                        resultDiv.innerHTML =
                            `<span class="text-danger"> ${data.avail_count} Ruangan Tersedia !</span>`;
                        bookBtn.disabled = true;
                    }
                })
                .catch(err => {
                    console.log(err);
                    console.error(err);
                    alert('Error checking availability.');
                });
        });
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

                        modal.querySelector('#venue_name').value = data.property.name;
                        modal.querySelector('#venue').value = data.property.id;


                        modal.querySelector('#name').value = data.user.name || '';
                        modal.querySelector('#email').value = data.user.email || '';
                        modal.querySelector('#phone_number').value = data.user.phone_number || '';

                        // Reset input tanggal dan unit (opsional)
                        modal.querySelector('#start').value = '';
                        modal.querySelector('#end').value = '';
                        modal.querySelector('#ordered_unit').value = 1;

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
