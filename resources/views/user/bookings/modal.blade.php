<!-- Modal add event -->
<div class="modal fade" id="addEvent" tabindex="-1" role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
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
                        <label for="venue_name" class="form-label">Ruangan</label>
                        <!-- Ditampilkan ke user -->
                        <input type="text" class="form-control" id="venue_name" readonly>
                        <input type="hidden" id="venue" name="venue">

                    </div>


                    {{-- <div class="mb-3">
                            <label for="payment_receipt" class="form-label">Bukti Pembayaran</label>
                            <input type="file" class="form-control" id="payment_receipt" name="payment_receipt"
                                accept=".pdf,image/*">
                        </div> --}}

                    <div class="mb-3">
                        <label for="request_letter" class="form-label">Surat Peminjaman</label>
                        <input type="file" class="form-control" id="request_letter" name="request_letter"
                            accept=".pdf,image/*">
                        <small class="form-text text-muted">Jika belum ada, boleh dikosongi terlebih dahulu</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="ordered_unit" class="form-label">Jumlah Unit</label>
                        <input type="number" class="form-control" id="ordered_unit" name="ordered_unit"
                            min="1" value="1" required>
                        <span class="mt-1 text-sm text-danger">Untuk tipe Aula dan Kelas, hanya ada 1
                            Ruangan</span>
                    </div>
                    <div class="mb-3">
                        <label for="total_harga" class="form-label">Total Harga</label>
                        <p id="total_price" style="font-weight: bold; font-size: 1.25rem;">Rp 0</p>
                        <input type="hidden" id="total_harga_input" name="total_harga" value="0">
                    </div>



                    <button type="button" id="checkAvailabilityBtn" class="btn btn-success">Cek Ketersediaan
                        Ruangan</button>

                    <div id="availabilityResult" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="createTransactionBtn" disabled>Pesan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
