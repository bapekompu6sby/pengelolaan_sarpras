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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Data master/</span> Ruangan</h4>
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
                    <div class="d-flex justify-content-between">
                        <div class="head">
                            <h5 class="card-header">Sarana & Prasarana</h5>
                        </div>
                        <div class="my-auto pe-4">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalCenter">
                                <span class="tf-icons bx bx-plus-medical bx-sm"></span>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap p-4">
                        <table id="datatable" class="display table table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Properti</th>
                                    <th>Img</th>
                                    <th>Jenis</th>
                                    <th>Kapasitas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($properties as $property)
                                    <tr>
                                        <td>
                                            <i class="fab fa-react fa-lg text-info me-3"></i>
                                            <strong>{{ $property->name }}</strong>
                                        </td>

                                        <td>
                                            @if (!empty($property->image_path))
                                                <img src="{{ asset('uploads/' . $property->image_path) }}"
                                                    alt="{{ $property->name }}" width="50" height="50"
                                                    style="object-fit: cover; border-radius: 4px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>

                                        <td>{{ ucfirst($property->type) }}</td>
                                        <td>{{ $property->capacity }}</td>
                                        <td>
                                            <span class="badge bg-label-success me-1">Kosong</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm me-1"
                                                data-bs-toggle="modal" data-bs-target="#modalCenter{{ $property->id }}">
                                                Edit
                                            </button>

                                            <button type="button" class="btn btn-danger btn-sm me-1" data-bs-toggle="modal"
                                                data-bs-target="#modalDelete{{ $property->id }}">
                                                Delete
                                            </button>

                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalDetail{{ $property->id }}">
                                                Detail
                                            </button>
                                        </td>

                                    </tr>

                                    @include('components.edit-modal', ['property' => $property])
                                    @include('components.confirm-modal', ['property' => $property])

                                    {{-- Modal Detail --}}
                                    <div class="modal fade" id="modalDetail{{ $property->id }}" tabindex="-1"
                                        aria-labelledby="modalDetailLabel{{ $property->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalDetailLabel{{ $property->id }}">Detail
                                                        Properti: {{ $property->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Tipe Room:</strong>
                                                            {{ $property->room_type ?: '-' }}</li>
                                                        <li class="list-group-item"><strong>Luas:</strong>
                                                            {{ $property->area ?: '-' }} m<sup>2</sup></li>
                                                        <li class="list-group-item"><strong>Fasilitas:</strong>
                                                            {{ $property->facilities ?: '-' }}</li>
                                                        <li class="list-group-item"><strong>Harga:</strong>
                                                            {{ $property->price ?: '-' }}</li>
                                                        <li class="list-group-item"><strong>Unit:</strong>
                                                            {{ $property->unit }}</li>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>



                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Modal -->
    <div class="modal fade" id="modalCenter" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Tambah Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <!-- Nama Ruangan -->
                        <div class="mb-3">
                            <label for="nameWithTitle" class="form-label">Nama Ruangan</label>
                            <input type="text" id="nameWithTitle" class="form-control"
                                placeholder="Masukkan nama ruangan" name="name" value="{{ old('name') }}" required />
                        </div>

                        <!-- Jenis Sarana dan Kapasitas -->
                        <div class="row g-2">
                            <div class="col">
                                <label for="typeSelect" class="form-label">Jenis Sarana</label>
                                <select class="form-select" id="typeSelect" name="type" required>
                                    <option disabled selected>Pilih jenis</option>
                                    <option value="kelas">Kelas</option>
                                    <option value="aula">Aula</option>
                                    <option value="asrama">Asrama</option>
                                    <option value="paviliun">Paviliun</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="capacityInput" class="form-label">Kapasitas</label>
                                <input type="number" id="capacityInput" class="form-control"
                                    placeholder="Kapasitas ruangan" min="1" name="capacity" required />
                            </div>
                        </div>

                        <!-- Room Type -->
                        <div class="mt-3">
                            <label for="roomType" class="form-label">Tipe Ruangan</label>
                            <input type="text" id="roomType" class="form-control" name="room_type"
                                placeholder="Contoh: VIP, Standar" />
                        </div>

                        <!-- Luas -->
                        <div class="mt-3">
                            <label for="area" class="form-label">Luas Ruangan (m²)</label>
                            <input type="text" id="area" class="form-control" name="area"
                                placeholder="Contoh: 50" />
                        </div>

                        <!-- Fasilitas -->
                        <div class="mt-3">
                            <label for="facilities" class="form-label">Fasilitas</label>
                            <textarea id="facilities" class="form-control" name="facilities" placeholder="Contoh: AC, Proyektor, Sound System"></textarea>
                        </div>

                        <!-- Harga dan Unit -->
                        <div class="row g-2 mt-3">
                            <div class="col">
                                <label for="price" class="form-label">Harga</label>
                                <input type="number" id="price" class="form-control" name="price"
                                    placeholder="Masukkan harga" />
                            </div>
                            <div class="col">
                                <label for="unit" class="form-label">Unit</label>
                                <input type="number" id="unit" class="form-control" name="unit"
                                    placeholder="Jumlah unit" />
                            </div>
                        </div>

                        <!-- Upload Gambar -->
                        <div class="mt-3">
                            <label for="img" class="form-label">Upload Gambar</label>
                            <input type="file" id="img" class="form-control" name="img"
                                accept="image/*" />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/js/datatables.js') }}"></script>
@endsection
