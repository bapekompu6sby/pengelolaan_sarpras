{{-- ADD KAMAR --}}
<div class="modal fade" id="addKamarModal" tabindex="-1" aria-labelledby="addKamarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable ">
        <div class="modal-content">
            <form id="addKamarForm" method="POST" action="{{ route('kamar.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="addKamarModalLabel">
                        <i class="bx bx-bed me-1"></i> Tambah Kamar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="properties_id" name="properties_id">

                    <div class="mb-3">
                        <label for="nama_kamar" class="form-label">Nama Kamar <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kamar" name="nama_kamar"
                            placeholder="cth: 201 / A-1" required autofocus>
                        <div class="form-text">Gunakan format konsisten agar mudah dicari.</div>
                    </div>
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="kapasitas" class="form-label">Kapasitas <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                                    min="1" value="1" required>
                                <span class="input-group-text">org</span>
                            </div>
                            <div class="form-text">Minimal 1 orang.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="lantai" class="form-label">Lantai <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-layer"></i></span>
                                <input type="number" class="form-control" id="lantai" name="lantai" min="1"
                                    required>
                            </div>
                            <div class="form-text">Masukkan angka lantai (1, 2, 3, …).</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Tambah Kamar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT KAMAR --}}
<div class="modal fade" id="editKamarModal" tabindex="-1" aria-labelledby="editKamarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editKamarForm" method="POST" action="">
                @csrf {{-- sesuai JS kamu, pakai POST custom --}}

                <div class="modal-header">
                    <h5 class="modal-title" id="editKamarModalLabel">
                        <i class="bx bx-edit-alt me-1"></i> Edit Kamar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="kamar_id" name="kamar_id">

                    <div class="mb-3">
                        <label for="nama_kamar_edit" class="form-label">Nama Kamar <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kamar_edit" name="nama_kamar" required>
                        <div class="form-text">Perbarui nama jika diperlukan.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kapasitas_edit" class="form-label">Kapasitas <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="kapasitas_edit" name="kapasitas"
                                    min="1" required>
                                <span class="input-group-text">org</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="lantai_edit" class="form-label">Lantai <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-layer"></i></span>
                                <input type="number" class="form-control" id="lantai_edit" name="lantai"
                                    min="1" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Update Kamar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELETE KAMAR --}}
<div class="modal fade" id="modalDeleteRoom{{ $k->id }}" data-bs-backdrop="static" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('kamar.destroy', $k->id) }}" method="POST">
                @csrf @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-trash me-1"></i> Hapus Kamar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bx bx-error me-2"></i>
                        <div>Data yang dihapus tidak dapat dikembalikan.</div>
                    </div>

                    <ul class="list-group list-group-flush mb-2">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Kamar</span><strong>{{ $k->nama_kamar }}</strong>
                        </li>
                        @isset($p)
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Properti</span><strong>{{ $p->name }}</strong>
                            </li>
                        @endisset
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Lantai</span><strong>{{ (int) $k->lantai }}</strong>
                        </li>
                    </ul>

                    <div class="form-text">Pastikan data di atas sudah benar sebelum menghapus.</div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
