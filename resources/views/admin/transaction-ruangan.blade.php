@extends('layout.index')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.nav')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
    <style>
        /* Atur toolbar supaya lebih langsing & rapi */
        .fc-toolbar {
            flex-wrap: wrap;
            gap: 6px;
        }

        .fc-toolbar-chunk {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Mobile view */
        @media (max-width: 576px) {
            .fc-toolbar-chunk:first-child {
                justify-content: flex-start;
                width: 100%;
                /* biar tombol ngambil full baris */
            }

            .fc-toolbar-title {
                font-size: 1rem;
            }
        }

        /* Tombol biar nggak melar aneh */
        .fc-addEventButton-button,
        .fc-listEventButton-button {
            flex: none;
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

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Peminjaman/</span> Tambah peminjaman</h4> --}}

        @if ($errors->any())
            <div>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <h5 class="text-danger">Masukkan data dengan benar</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close  btn-lg" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col">
                <div class="card calendar-container">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal add event -->
        <div class="modal fade" id="addEvent" tabindex="-1" role="dialog" aria-labelledby="addEventLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('transactions.ruangan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addEventLabel">Add Event</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Pemesan</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Nomor HP/WA</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="office" class="form-label">Instansi</label>
                                <input type="text" class="form-control" id="office" name="office" required>
                            </div>
                            <div class="col mb-0">
                                <label for="affiliation" class="form-label">Afiliasi</label>
                                <select class="form-select" id="affiliation" aria-label="Default select example"
                                    name="affiliation">
                                    <option value="" selected disabled>Open this select menu</option>
                                    <option value="internal_pu">Internal PU</option>
                                    <option value="external_pu">External PU</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="request_letter" class="form-label">Surat Peminjaman</label>
                                <input type="file" class="form-control" id="request_letter" name="request_letter"
                                    accept=".pdf,image/*">
                            </div>
                            <div class="mb-3">
                                <label for="event" class="form-label">Kegiatan</label>
                                <input type="text" class="form-control" id="event" name="event" required>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="start" class="form-label">Mulai</label>
                                    <input type="date" class="form-control" id="start" name="start" required>
                                </div>
                                <div class="col mb-3">
                                    <label for="end" class="form-label">Selesai</label>
                                    <input type="date" class="form-control" id="end" name="end" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="venue" class="form-label">Ruangan</label>
                                <select class="form-select" id="venue" name="venue" required>
                                    <option selected disabled>Pilih Ruangan</option>
                                    @foreach ($properties as $venue)
                                        <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="ordered_unit" class="form-label">Jumlah Ruangan</label>
                                <input type="number" class="form-control" id="ordered_unit" name="ordered_unit"
                                    value="1">
                                <span class="mt-1 text-sm text-danger">Untuk tipe Aula dan Kelas, hanya ada 1
                                    Ruangan</span>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <button type="button" id="checkAvailabilityBtn" class="btn btn-success">Cek Ketersediaan
                                Ruangan</button>

                            <div id="availabilityResult" class="mt-2"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="createTransactionBtn" disabled>Save
                                changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- / Modal add event -->

        <!-- Modal trigger -->
        <button id="btn-trigger" type="button" class="btn btn-primary invisible" data-bs-toggle="modal"
            data-bs-target="#addEvent">
            Add Event
        </button>


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
                        console.log(err)
                        console.error(err);
                        alert('Error checking availability.');
                    });
            });
        </script>
    @endsection
