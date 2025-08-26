@extends('layout.index')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.nav')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">List Penginapan & Kamar</h4>

        @foreach ($properties as $p)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('uploads/' . $p['image_path']) }}" alt="{{ $p['name'] }}"
                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                        <h5 class="mb-0">{{ $p['name'] }} ({{ ucfirst($p['type']) }})</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">Unit: {{ $p['unit'] }}</span>
                        <button class="btn btn-success btn-sm"
                            onclick="openAddKamarModal({{ $p['id'] }}, '{{ $p['name'] }}')">
                            + Tambah Kamar
                        </button>


                    </div>
                </div>



                <div class="card-body">
                    <div class="row">
                        @foreach ($p['kamar'] as $k)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $k['nama_kamar'] }}</h6>
                                        
                                        <button class="btn btn-sm btn-warning btn-edit-kamar" data-id="{{ $k['id'] }}"
                                            data-nama="{{ $k['nama_kamar'] }}" data-status="{{ $k['status'] }}"
                                            data-penghuni="{{ $k['nama_penghuni'] ?? '' }}" data-bs-toggle="modal"
                                            data-bs-target="#editKamarModal">
                                            Edit
                                        </button>
                                        <form action="{{ route('kamar.destroy', $k['id']) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus kamar ini?')">
                                                Remove
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <!-- Modal Tambah Kamar -->
    <div class="modal fade" id="addKamarModal" tabindex="-1" aria-labelledby="addKamarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"> <!-- form di dalam modal-content -->
                <form id="addKamarForm" method="POST" action="{{ route('kamar.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addKamarModalLabel">Tambah Kamar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="penginapan_id" name="penginapan_id">
                        <div class="mb-3">
                            <label for="nama_kamar" class="form-label">Nama Kamar</label>
                            <input type="text" class="form-control" id="nama_kamar" name="nama_kamar" required>
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="kapasitas" name="kapasitas" value="1"
                                min="1" required>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah Kamar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Kamar -->
    <div class="modal fade" id="editKamarModal" tabindex="-1" aria-labelledby="editKamarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editKamarForm" method="POST" action="">
                    @csrf
                    @method('POST')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editKamarModalLabel">Edit Kamar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="kamar_id" name="kamar_id">
                            <div class="mb-3">
                                <label for="nama_kamar" class="form-label">Nama Kamar</label>
                                <input type="text" class="form-control" id="nama_kamar" name="nama_kamar">
                            </div>
                            
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Kamar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Script untuk buka modal, set penginapan_id, dan reset form -->
    <script>
        function openAddKamarModal(penginapanId, penginapanName) {
            const modalLabel = document.getElementById('addKamarModalLabel');
            const form = document.getElementById('addKamarForm');
            const penginapanInput = document.getElementById('penginapan_id');

            // Set judul modal
            modalLabel.textContent = `Tambah Kamar di ${penginapanName}`;

            // Set hidden input
            penginapanInput.value = penginapanId;

            // Reset semua input form
            form.reset();

            // Tampilkan modal
            const addModal = new bootstrap.Modal(document.getElementById('addKamarModal'));
            addModal.show();
        }
    </script>
    <script>
        // Ambil modal element
        const editKamarModal = document.getElementById('editKamarModal')

       
        editKamarModal.addEventListener('show.bs.modal', function(event) {
            
            const button = event.relatedTarget

            // Ambil data dari tombol
            const id = button.getAttribute('data-id')
            const nama = button.getAttribute('data-nama')
            const status = button.getAttribute('data-status')
            const penghuni = button.getAttribute('data-penghuni')

            // Isi form modal
            const modal = this
            modal.querySelector('#kamar_id').value = id
            modal.querySelector('#nama_kamar').value = nama
            modal.querySelector('#status').value = status
            modal.querySelector('#nama_penghuni').value = penghuni

            // Update action form supaya submit ke route yang benar
            modal.querySelector('#editKamarForm').action = `/kamar/edit/${id}` // pastikan route sesuai
        })
    </script>
@endsection
