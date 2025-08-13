@extends('layout.index')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.nav')
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

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Ruangan</h4>
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
                        @foreach ($properties as $property)
                            <div
                                style="display: flex; width: 100%; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: Arial, sans-serif; gap: 20px; margin-bottom: 20px;">
                                <div
                                    style="flex-shrink: 0; width: 250px; height: 250px; overflow: hidden; border-radius: 8px;">
                                    <img src="{{ asset('uploads/' . $property->image_path) }}" alt="{{ $property->name }}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" />
                                </div>


                                <div style="flex-grow: 1; color: #333;">
                                    <h3 style="margin-top: 0; margin-bottom: 12px;">{{ $property->name }}</h3>

                                    <p style="margin: 4px 0;">
                                        <strong>Tipe:</strong> {{ strtoupper($property->room_type) }}
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Kapasitas:</strong> {{ $property->capacity }} orang
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Luas:</strong> {{ $property->area }} m<sup>2</sup>
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Fasilitas:</strong> {{ $property->facilities }}
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Harga:</strong> Rp {{ number_format($property->price, 0, ',', '.') }} / Per
                                        hari Per Ruangan
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Unit:</strong> {{ $property->unit }}
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Status:</strong> <span
                                            style="color: #028800; font-weight: bold;">tersedia</span>
                                    </p>

                                    <button class="btn btn-primary btn-pesan" data-property-id="{{ $property->id }}"
                                        data-bs-toggle="modal" data-bs-target="#addEvent">
                                        Pesan Sekarang
                                    </button>




                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal add event -->
    <div class="modal fade" id="addEvent" tabindex="-1" role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('transactions.ruangan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventLabel">Add Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Pemesan</label>
                            <input type="text" class="form-control" id="name" name="name" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor HP/WA</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" required readonly>
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
                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="ordered_unit" class="form-label">Jumlah Unit</label>
                            <input type="number" class="form-control" id="ordered_unit" name="ordered_unit"
                                min="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="total_harga" class="form-label">Total Harga</label>
                            <p id="total_price" style="font-weight: bold; font-size: 1.25rem;">Rp 0</p>
                            <input type="hidden" id="total_harga_input" name="total_harga" value="0">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Content -->


@endsection

@section('script')
    <script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/js/datatables.js') }}"></script>

    <script>
        // Fungsi hitung total harga
        function calculateTotal(pricePerDay) {
            const modal = document.getElementById('addEvent');
            const start = new Date(modal.querySelector('#start').value);
            const end = new Date(modal.querySelector('#end').value);
            const unit = parseInt(modal.querySelector('#ordered_unit').value) || 1;

            if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) {
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
                        // Simpan harga per hari ke global variable
                        window.currentPrice = parseInt(data.property.price) || 0;

                        const modal = document.getElementById('addEvent');
                        const venueSelect = modal.querySelector('#venue');
                        venueSelect.value = data.property.id;

                        const modalTitle = modal.querySelector('.modal-title');
                        modalTitle.textContent = 'Pesan Ruangan: ' + data.property.name;

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
