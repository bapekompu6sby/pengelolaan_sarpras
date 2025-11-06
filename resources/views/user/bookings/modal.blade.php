<!-- ===== Modal add event (Tokopedia-like) ===== -->
<style>
    /* ====== Styling khusus modal galeri ====== */
    .tp-modal .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, .06);
    }

    .tp-modal .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, .06);
    }

    .tp-gallery,
    .tp-gallery .carousel,
    .tp-gallery .carousel-inner,
    .tp-gallery .carousel-item {
        height: 100%;
    }

    .tp-gallery .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 12px;
        background: #f6f8fc;
    }

    /* deretan thumbnail melebar penuh dan turun ke baris baru */
    .tp-thumbs {
        display: flex;
        flex-wrap: wrap;
        /* <-- kunci */
        gap: 8px;
        overflow: visible;
        /* jangan scroll */
        padding: 0;
    }

    /* ukuran thumb bisa kamu sesuaikan */
    .tp-thumbs img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid transparent;
        cursor: pointer;
        flex: 0 0 auto;
        background: #f0f2f6;
    }


    .tp-thumbs img.active {
        border-color: var(--bs-primary);
    }

    .tp-summary .badge {
        font-weight: 600;
    }

    .tp-price {
        font-weight: 700;
        font-size: 1.15rem;
    }

    @media (max-width: 991.98px) {
        .tp-gallery {
            min-height: 280px;
        }
    }

    @media (min-width: 992px) {
        .tp-gallery {
            min-height: 360px;
        }
    }
</style>


<div class="modal fade" id="addEvent" tabindex="-1" aria-labelledby="addEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content tp-modal">
            <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="addEventLabel">Pesan Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4 align-items-stretch">
                        <!-- ===== KIRI: GALERI + THUMBS + RINGKASAN SINGKAT ===== -->
                        <div class="col-12 col-lg-5 d-flex flex-column">
                            <!-- GALERI -->
                            <div class="tp-gallery card border-0 flex-grow-1">
                                <div id="modalGallery" class="carousel slide" data-bs-ride="carousel"
                                    data-bs-interval="4000" data-bs-touch="true">
                                    <div class="carousel-inner" id="modalGalleryInner">
                                        <!-- Placeholder awal; akan diganti via JS -->
                                        <div class="carousel-item active">
                                            <img src="https://placehold.co/800x600?text=Loading+Image"
                                                alt="placeholder">
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#modalGallery"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Prev</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#modalGallery"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                    <div class="carousel-indicators" id="modalGalleryIndicators">
                                        <button type="button" data-bs-target="#modalGallery" data-bs-slide-to="0"
                                            class="active" aria-current="true" aria-label="Slide 1"></button>
                                    </div>
                                </div>

                                <!-- THUMBS -->
                                <div class="tp-thumbs d-flex gap-2 mt-2" id="modalGalleryThumbs">
                                    <img src="https://placehold.co/120x120?text=Thumb" alt="thumb" class="active">
                                </div>
                            </div>

                            <!-- RINGKASAN SINGKAT (opsional, di bawah galeri) -->
                            <div class="tp-summary card border-0 mt-3">
                                <div class="card-body">
                                    <h6 class="mb-2" id="tpSumName">—</h6>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-label-primary" id="tpSumType">Tipe —</span>
                                        <span class="badge bg-label-secondary" id="tpSumCapacity">Kapasitas —</span>
                                        <span class="badge bg-label-info" id="tpSumArea">Luas — m²</span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <strong>Fasilitas:</strong> <span id="tpSumFacilities">—</span>
                                    </div>
                                    <div class="tp-price"><span id="tpSumPrice">Rp 0</span> <small
                                            class="text-muted fw-normal">/ hari</small></div>
                                </div>
                            </div>
                        </div>

                        <!-- ===== KANAN: FORM (ID/NAME TIDAK DIUBAH) ===== -->
                        <div class="col-12 col-lg-7">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Pemesan</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Nomor HP/WA</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="office" class="form-label">Instansi</label>
                                    <input type="text" class="form-control" id="office" name="office"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="affiliation" class="form-label">Afiliasi</label>
                                    <select class="form-select" id="affiliation" name="affiliation">
                                        <option value="" selected disabled>Pilih afiliasi</option>
                                        <option value="internal_pu">Internal PU</option>
                                        <option value="external_pu">External PU</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="event" class="form-label">Kegiatan</label>
                                    <input type="text" class="form-control" id="event" name="event"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start" class="form-label">Mulai</label>
                                    <input type="date" class="form-control" id="start" name="start"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end" class="form-label">Selesai</label>
                                    <input type="date" class="form-control" id="end" name="end"
                                        required>
                                </div>
                            </div>

                            <!-- Kolom Jam -->

                            {{-- <div id="jamFieldsContainer" class="d-none">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="jam_start" class="form-label">Jam Mulai</label>
                                        <input type="text" class="form-control" id="jam_start" name="jam_start"
                                            placeholder="HH:MM" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="jam_end" class="form-label">Jam Selesai</label>
                                        <input type="text" class="form-control" id="jam_end" name="jam_end"
                                            placeholder="HH:MM" required>
                                    </div>
                                </div>
                            </div> --}}
                            <!-- Input -->
                            <div id="jamFieldsContainer" class="d-none">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label">Jam Mulai</label>
                                        <div class="d-flex gap-2">
                                            <select id="h_start" class="form-select" required></select> :
                                            <select id="m_start" class="form-select" required></select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Jam Selesai</label>
                                        <div class="d-flex gap-2">
                                            <select id="h_end" class="form-select" required></select> :
                                            <select id="m_end" class="form-select" required></select>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="jam_start" name="jam_start">
                                <input type="hidden" id="jam_end" name="jam_end">
                            </div>

                            <input type="hidden" id="property_type" name="property_type">
                            <div id="jamFieldsContainer"></div>
                            <div class="mb-3">
                                <label for="venue_name" class="form-label">Ruangan</label>
                                <input type="text" class="form-control" id="venue_name" readonly>
                                <input type="hidden" id="venue" name="venue">
                            </div>

                            <div class="mb-3">
                                <label for="request_letter" class="form-label">Surat Peminjaman</label>
                                <input type="file" class="form-control" id="request_letter" name="request_letter"
                                    accept=".pdf,image/*">
                                <small class="form-text text-muted">Jika belum ada, boleh dikosongi terlebih
                                    dahulu</small>
                            </div>



                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ordered_unit" class="form-label">Jumlah Unit</label>
                                    <input type="number" class="form-control" id="ordered_unit" name="ordered_unit"
                                        min="1" value="1" required>
                                    <small class="text-danger d-block mt-1">Untuk tipe Aula dan Kelas, hanya ada 1
                                        Ruangan</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Harga</label>
                                    <p id="total_price" class="mb-1 fw-bold fs-5">Rp 0</p>
                                    <input type="hidden" id="total_harga_input" name="total_harga" value="0">
                                    <button type="button" id="checkAvailabilityBtn"
                                        class="btn btn-success btn-sm mt-2">Cek Ketersediaan Ruangan</button>
                                    <div id="availabilityResult" class="mt-2 small"></div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /row -->
                </div> <!-- /modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="createTransactionBtn" disabled>Pesan
                        Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
