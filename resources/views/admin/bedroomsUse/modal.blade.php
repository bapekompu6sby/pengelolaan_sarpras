<!-- Modal Edit Penghuni (reusable) -->
<div class="modal fade" id="editPenghuniModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-modern">
        <form method="POST" action="{{ route('penghuni.update') }}" class="modal-content">
            @csrf
            @method('PUT')
            <input type="hidden" name="detail_id" id="ep-detail-id">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Edit Nama Penghuni</h5>
                    <small class="text-muted">Isi sesuai kapasitas. Boleh dikosongkan jika belum terisi.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div id="ep-fields" class="vstack gap-2">
                    <!-- input nama akan di-inject via JS -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-modern"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-modern">Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- Modal Konfirmasi Hapus (reusable + ringkasan data) --}}
<div class="modal fade" id="confirmDeleteDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-modern">
        <div class="modal-content">
            <form method="POST" action="#" id="confirmDeleteForm">
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-trash me-1"></i> Hapus Jadwal Kamar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bx bx-error-circle me-2"></i>
                        <div>
                            Data yang dihapus <strong>tidak dapat dikembalikan</strong>. Pastikan informasi di bawah
                            sudah benar.
                        </div>
                    </div>

                    <!-- Ringkasan data yang akan dihapus -->
                    <ul class="list-group list-group-flush mb-2">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Kamar</span>
                            <strong class="js-del-kamar">—</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Properti</span>
                            <strong class="js-del-properti">—</strong>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Tanggal</span>
                            <strong class="js-del-tanggal">—</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Jumlah Penghuni</span>
                            <strong class="js-del-penghuni">—</strong>
                        </li>
                    </ul>

                    <div class="form-text">
                        Menyetujui berarti menghapus <em>jadwal kamar beserta seluruh data penghuni terkait</em>.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-modern">
                        <i class="bx bx-trash"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
