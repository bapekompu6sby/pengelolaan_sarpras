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
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Form peminjaman/</span> {{ $property['name'] }}
        </h4>

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
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

                <div class="card shadow-sm w-100">
                    <div class="card-body">
                        {{-- FORM INPUT --}}
                        <form id="formPinjam">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property['id'] }}">

                            <div class="mb-3">
                                <label class="form-label">Nama Properti</label>
                                <input type="text" value="{{ $property['name'] }}" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Peminjam</label>
                                <input type="text" value="{{ $user['name'] }}" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Instansi</label>
                                <input type="text" name="instansi" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Affiliation</label>
                                <input type="text" name="affiliation" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone_number" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" value="{{ $user['email'] }}" class="form-control" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Unit yang Dipesan</label>
                                <input type="number" name="orderer_unit" min="1" max="{{ $property['unit'] }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start" min="{{ date('Y-m-d') }}" class="form-control"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="end" min="{{ date('Y-m-d') }}" class="form-control"
                                    required>
                            </div>

                            {{-- <div class="mb-3">
                                <label class="form-label">Warna Kode</label>
                                <input type="color" name="color" value="#0d6efd" class="form-control form-control-color"
                                    title="Pilih warna">
                            </div> --}}

                            <div class="text-end">
                                <button type="button" id="btnShowModal" class="btn btn-primary">Pesan Sekarang</button>
                            </div>
                        </form>

                        {{-- MODAL KONFIRMASI --}}
                        <div class="modal fade" id="modalDetailPinjam" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Peminjaman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('transactions.pinjam.store') }}" method="POST">
                                        @csrf

                                        {{-- Hidden input untuk semua data --}}
                                        <input type="hidden" name="property_id" value="{{ $property['id'] }}">
                                        <input type="hidden" name="instansi" id="hidden_instansi">
                                        <input type="hidden" name="affiliation" id="hidden_affiliation">
                                        <input type="hidden" name="phone_number" id="hidden_phone_number">
                                        <input type="hidden" name="orderer_unit" id="hidden_orderer_unit">
                                        <input type="hidden" name="start" id="hidden_start">
                                        <input type="hidden" name="end" id="hidden_end">

                                        {{-- <input type="hidden" name="color" id="hidden_color"> --}}

                                        <div class="modal-body">
                                            <p>Periksa kembali data peminjaman Anda sebelum mengajukan.</p>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Nama Properti:</strong>
                                                    <p>{{ $property['name'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Nama Peminjam:</strong>
                                                    <p>{{ $user['name'] }}</p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Instansi:</strong>
                                                    <p id="text_instansi"></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Affiliation:</strong>
                                                    <p id="text_affiliation"></p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Nomor Telepon:</strong>
                                                    <p id="text_phone_number"></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Unit yang Dipesan:</strong>
                                                    <p id="text_orderer_unit"></p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Tanggal Mulai:</strong>
                                                    <p id="text_start"></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Tanggal Selesai:</strong>
                                                    <p id="text_end"></p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Harga per Hari:</strong>
                                                    <p id="text_price">{{ $property['price'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Total Harga:</strong>
                                                    <p id="total_price"></p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="total_harga" id="hidden_total_price">
                                            {{-- <div class="mb-3">
                                                <strong>Warna Kode:</strong>
                                                <p><span id="text_color"
                                                        style="display:inline-block;width:20px;height:20px;border:1px solid #000;"></span>
                                                </p>
                                            </div> --}}
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')

    <script>
        document.getElementById('btnShowModal').addEventListener('click', function() {
            let instansi = document.querySelector('input[name="instansi"]').value;
            let affiliation = document.querySelector('input[name="affiliation"]').value;
            let phone_number = document.querySelector('input[name="phone_number"]').value;
            let orderer_unit = parseInt(document.querySelector('input[name="orderer_unit"]').value);
            let start = document.querySelector('input[name="start"]').value;
            let end = document.querySelector('input[name="end"]').value;

            // let color = document.querySelector('input[name="color"]').value;

            // Tampilkan di modal
            document.getElementById('text_instansi').textContent = instansi;
            document.getElementById('text_affiliation').textContent = affiliation;
            document.getElementById('text_phone_number').textContent = phone_number;
            document.getElementById('text_orderer_unit').textContent = orderer_unit;
            document.getElementById('text_start').textContent = start;
            document.getElementById('text_end').textContent = end;
            // document.getElementById('text_color').style.backgroundColor = color;

            // Set hidden input
            document.getElementById('hidden_instansi').value = instansi;
            document.getElementById('hidden_affiliation').value = affiliation;
            document.getElementById('hidden_phone_number').value = phone_number;
            document.getElementById('hidden_orderer_unit').value = orderer_unit;
            document.getElementById('hidden_start').value = start;
            document.getElementById('hidden_end').value = end;
            // document.getElementById('hidden_color').value = color;

            // Hitung total harga
            let pricePerDay = parseInt(document.getElementById("text_price").innerText);
            let startDate = new Date(start);
            let endDate = new Date(end);
            let diffDays = (endDate - startDate) / (1000 * 60 * 60 * 24) + 1;

            let total = diffDays * pricePerDay * orderer_unit;
            document.getElementById("total_price").innerText = total.toLocaleString("id-ID");
            
            // Tambahkan ini supaya value ikut terkirim
            document.getElementById("hidden_total_price").value = total;

            // Tampilkan modal
            new bootstrap.Modal(document.getElementById('modalDetailPinjam')).show();
        });
    </script>
@endsection
