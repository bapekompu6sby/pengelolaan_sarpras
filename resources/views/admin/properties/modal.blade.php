{{-- =========================================================
  MODAL: CREATE PROPERTY (Tambah Ruangan)
  - Konsisten: modal-modern + form-floating + btn-modern
  - Aksesibilitas: label ↔ id, aria-label
  ========================================================== --}}
<div class="modal fade" id="modalCreate" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
    aria-labelledby="modalCreateTitle">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-modern">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalCreateTitle">Tambah Ruangan</h5>
                    <small class="text-muted">Isi data ruangan dengan benar sebelum menyimpan.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Nama Ruangan --}}
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" id="create_name" name="name" class="form-control"
                                    placeholder="Nama Ruangan" value="{{ old('name') }}" required>
                                <label for="create_name">Nama Ruangan</label>
                            </div>
                        </div>

                        {{-- Jenis Sarana --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="create_type" name="type" class="form-select" required>
                                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>Pilih jenis
                                    </option>
                                    <option value="kelas" @selected(old('type') === 'kelas')>Kelas</option>
                                    <option value="aula" @selected(old('type') === 'aula')>Aula</option>
                                    <option value="asrama" @selected(old('type') === 'asrama')>Asrama</option>
                                    <option value="paviliun" @selected(old('type') === 'paviliun')>Paviliun</option>
                                    <option value="fasilitas" @selected(old('type') === 'fasilitas')>Fasilitas</option>
                                </select>
                                <label for="create_type">Jenis Sarana</label>
                            </div>
                        </div>

                        {{-- Kapasitas --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="create_capacity" name="capacity" class="form-control"
                                    placeholder="Kapasitas" value="{{ old('capacity') }}" min="1" required>
                                <label for="create_capacity">Kapasitas</label>
                            </div>
                        </div>

                        {{-- Tipe Ruangan (opsional) --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="create_room_type" name="room_type" class="form-control"
                                    placeholder="VIP / Standar" value="{{ old('room_type') }}">
                                <label for="create_room_type">Tipe Ruangan (opsional)</label>
                            </div>
                        </div>

                        {{-- Luas (m²) --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="create_area" name="area" class="form-control"
                                    placeholder="Luas" value="{{ old('area') }}" min="0" step="1">
                                <label for="create_area">Luas Ruangan (m²)</label>
                            </div>
                        </div>

                        {{-- Fasilitas --}}
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea id="create_facilities" name="facilities" class="form-control" placeholder="Fasilitas" style="height: 100px">{{ old('facilities') }}</textarea>
                                <label for="create_facilities">Fasilitas (pisahkan dengan koma)</label>
                            </div>
                        </div>

                        {{-- Harga --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="create_price" name="price" class="form-control"
                                    placeholder="Harga" value="{{ old('price') }}" min="0">
                                <label for="create_price">Harga</label>
                            </div>
                        </div>

                        {{-- Unit --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="create_unit" name="unit" class="form-control"
                                    placeholder="Unit" value="{{ old('unit') }}" min="0">
                                <label for="create_unit">Unit</label>
                            </div>
                        </div>

                        {{-- Upload Gambar (cover) --}}
                        <div class="col-12">
                            <label for="create_img" class="form-label">Gambar Cover (opsional)</label>
                            <input type="file" id="create_img" name="img" class="form-control"
                                accept="image/*">
                            <small class="text-muted">Disimpan sebagai cover utama.</small>
                        </div>

                        {{-- Galeri (+ bisa banyak) --}}
                        <div class="col-12">
                            <label for="create_gallery" class="form-label">Galeri Foto (opsional, bisa pilih
                                banyak)</label>
                            <input type="file" id="create_gallery" name="gallery[]" class="form-control"
                                accept="image/*" multiple>
                            <small class="text-muted">JPG/PNG, max 20 MB per file.</small>
                        </div>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-modern">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================
  MODAL: EDIT PROPERTY (per property)
  - id unik per properti: modalEdit{{ $property->id }}
  - form-floating konsisten
  ========================================================== --}}
<div class="modal fade" id="modalEdit{{ $property->id }}" data-bs-backdrop="static" tabindex="-1"
    aria-hidden="true" aria-labelledby="modalEditTitle{{ $property->id }}">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-modern">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalEditTitle{{ $property->id }}">Edit Ruangan</h5>
                    <small class="text-muted">Perbarui informasi ruangan berikut.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <form action="{{ route('properties.update', $property->id) }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Nama Ruangan --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="edit_name_{{ $property->id }}" name="name"
                                    class="form-control" placeholder="Nama Ruangan"
                                    value="{{ old('name', $property->name) }}" maxlength="32" required>
                                <label for="edit_name_{{ $property->id }}">Nama Ruangan</label>
                            </div>
                        </div>

                        {{-- Jenis Sarana --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="edit_type_{{ $property->id }}" name="type" class="form-select"
                                    required>
                                    <option value="aula" @selected(old('type', $property->type) === 'aula')>Aula</option>
                                    <option value="kelas" @selected(old('type', $property->type) === 'kelas')>Kelas</option>
                                    <option value="asrama" @selected(old('type', $property->type) === 'asrama')>Asrama</option>
                                    <option value="paviliun" @selected(old('type', $property->type) === 'paviliun')>Paviliun</option>
                                    <option value="fasilitas" @selected(old('type', $property->type) === 'fasilitas')>Fasilitas</option>
                                </select>
                                <label for="edit_type_{{ $property->id }}">Jenis Sarana</label>
                            </div>
                        </div>

                        {{-- Kapasitas --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="edit_capacity_{{ $property->id }}" name="capacity"
                                    class="form-control" placeholder="Kapasitas"
                                    value="{{ old('capacity', $property->capacity) }}" min="1" max="1000"
                                    required>
                                <label for="edit_capacity_{{ $property->id }}">Kapasitas</label>
                            </div>
                        </div>

                        {{-- Tipe Ruangan --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="edit_room_type_{{ $property->id }}" name="room_type"
                                    class="form-control" placeholder="Tipe Ruangan"
                                    value="{{ old('room_type', $property->room_type) }}" maxlength="50">
                                <label for="edit_room_type_{{ $property->id }}">Tipe Ruangan</label>
                            </div>
                        </div>

                        {{-- Luas (m²) --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="edit_area_{{ $property->id }}" name="area"
                                    class="form-control" placeholder="Luas"
                                    value="{{ old('area', $property->area) }}" min="0" step="1">
                                <label for="edit_area_{{ $property->id }}">Luas (m²)</label>
                            </div>
                        </div>

                        {{-- Harga --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="edit_price_{{ $property->id }}" name="price"
                                    class="form-control" placeholder="Harga"
                                    value="{{ old('price', $property->price) }}" min="0" step="1">
                                <label for="edit_price_{{ $property->id }}">Harga</label>
                            </div>
                        </div>

                        {{-- Unit --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" id="edit_unit_{{ $property->id }}" name="unit"
                                    class="form-control" placeholder="Unit"
                                    value="{{ old('unit', $property->unit) }}" min="0">
                                <label for="edit_unit_{{ $property->id }}">Unit</label>
                            </div>
                        </div>

                        {{-- Fasilitas --}}
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea id="edit_facilities_{{ $property->id }}" name="facilities" class="form-control" placeholder="Fasilitas"
                                    style="height: 120px">{{ old('facilities', $property->facilities) }}</textarea>
                                <label for="edit_facilities_{{ $property->id }}">Fasilitas</label>
                            </div>
                        </div>

                        {{-- Upload Gambar --}}
                        {{-- ===================== Gambar & Galeri (Cover + Gallery) ===================== --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="mb-3">Gambar & Galeri (Usahakan 1:1)</h6>

                                    <div class="row g-4 align-items-start">
                                        {{-- ================== KOLOM COVER ================== --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Cover</label>

                                            {{-- Preview cover lama --}}
                                            @if ($property->image_path)
                                                <div class="mb-2">
                                                    <img id="coverPreview_{{ $property->id }}"
                                                        src="{{ asset('storage/uploads/properties/covers/' . $property->image_path) }}"
                                                        class="img-fluid rounded border"
                                                        style="max-height:220px;object-fit:cover;width:100%"
                                                        alt="Cover">
                                                </div>
                                                {{-- Hapus cover (opsional) --}}
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="remove_cover_{{ $property->id }}" name="remove_cover"
                                                        value="1">
                                                    <label class="form-check-label"
                                                        for="remove_cover_{{ $property->id }}">
                                                        Hapus cover (tanpa ganti)
                                                    </label>
                                                </div>
                                            @else
                                                <div class="mb-2">
                                                    <img id="coverPreview_{{ $property->id }}"
                                                        src="https://via.placeholder.com/600x360?text=No+Cover"
                                                        class="img-fluid rounded border"
                                                        style="max-height:220px;object-fit:cover;width:100%"
                                                        alt="Cover">
                                                </div>
                                            @endif

                                            {{-- Upload cover baru --}}
                                            <input type="file" id="edit_img_{{ $property->id }}" name="img"
                                                class="form-control" accept="image/*"
                                                onchange="previewCover_{{ $property->id }}(event)">
                                            <small class="text-muted d-block mt-1">JPG/PNG, maks 20MB.</small>
                                        </div>

                                        {{-- ================== KOLOM GALERI ================== --}}
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Galeri</label>

                                            {{-- Upload banyak (+) --}}
                                            <input type="file" id="edit_gallery_{{ $property->id }}"
                                                name="gallery[]" class="form-control mb-2" accept="image/*" multiple>
                                            <small class="text-muted d-block mb-3">Pilih beberapa file sekaligus.
                                                JPG/PNG, maks 20MB/berkas.</small>

                                            {{-- Daftar galeri yang sudah ada + checkbox hapus (−) --}}
                                            @if ($property->images->count())
                                                <div class="d-flex flex-wrap gap-3">
                                                    @foreach ($property->images as $img)
                                                        <div class="border rounded p-2" style="width:150px">
                                                            <img src="{{ asset('storage/uploads/properties/gallery/' . $img->image_path) }}"
                                                                class="img-fluid rounded mb-2"
                                                                style="height:90px;object-fit:cover;width:100%"
                                                                alt="Gallery">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="remove_gallery_{{ $img->id }}"
                                                                    name="remove_gallery[]"
                                                                    value="{{ $img->id }}">
                                                                <label class="form-check-label small"
                                                                    for="remove_gallery_{{ $img->id }}">
                                                                    Hapus (−)
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted">Belum ada foto galeri.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======= JS mini: preview cover baru ======= --}}
                        <script>
                            function previewCover_{{ $property->id }}(e) {
                                const file = e.target.files?.[0];
                                if (!file) return;
                                const img = document.getElementById('coverPreview_{{ $property->id }}');
                                img.src = URL.createObjectURL(file);
                            }
                        </script>


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================
  MODAL: DELETE PROPERTY (per property)
  - Tetap pakai modal konfirmasi kamu
  ========================================================== --}}
{{-- {-- DELETE PROPERTI --} --}}
<div class="modal fade" id="modalDelete{{ $property->id }}" data-bs-backdrop="static" tabindex="-1"
    aria-hidden="true" aria-labelledby="modalDeleteTitle{{ $property->id }}">
    <div class="modal-dialog modal-dialog-centered modal-modern">
        <div class="modal-content">
            <form action="{{ route('properties.destroy', $property->id) }}" method="POST">
                @csrf @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeleteTitle{{ $property->id }}">
                        <i class="bx bx-trash me-1"></i> Hapus Properti
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    {{-- Peringatan --}}
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bx bx-error me-2"></i>
                        <div>
                            Tindakan ini bersifat permanen. Data yang dihapus tidak dapat dikembalikan.
                        </div>
                    </div>

                    {{-- Ringkasan data yang akan dihapus --}}
                    <ul class="list-group list-group-flush mb-2">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Nama Properti</span>
                            <strong class="text-break">{{ $property->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Jenis</span>
                            <strong>{{ ucfirst($property->type) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Kapasitas</span>
                            <strong>{{ (int) $property->capacity }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Unit</span>
                            <strong>{{ (int) $property->unit }}</strong>
                        </li>

                        {{-- Jika di controller: PropertiesController@index -> ->withCount(['kamars','transactions']) --}}
                        @if (isset($property->kamars_count))
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Jumlah Kamar Terkait</span>
                                <strong>{{ $property->kamars_count }}</strong>
                            </li>
                        @endif
                        @if (isset($property->transactions_count))
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Jumlah Transaksi Terkait</span>
                                <strong>{{ $property->transactions_count }}</strong>
                            </li>
                        @endif
                    </ul>

                    <div class="form-text">
                        Pastikan data sudah benar sebelum menghapus.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-modern">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ===========================
     modalDetail (ala Tokopedia)
   =========================== --}}
<style>
    /* Header/footer ringan */
    .tpd .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, .06)
    }

    .tpd .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, .06)
    }

    /* Kolom kiri: galeri 1:1 always */
    .tpd-gallery {
        position: relative;
        width: 100%;
        aspect-ratio: 1/1;
        border-radius: 12px;
        overflow: hidden;
        background: #f6f8fc;
    }

    .tpd-gallery .carousel,
    .tpd-gallery .carousel-inner,
    .tpd-gallery .carousel-item {
        position: absolute;
        inset: 0;
        height: 100%
    }

    .tpd-gallery .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 0;
        background: #f6f8fc;
    }

    .tpd-gallery .carousel-indicators,
    .tpd-gallery .carousel-control-prev,
    .tpd-gallery .carousel-control-next {
        z-index: 2
    }

    /* Thumbnails */
    .tpd-thumbs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px
    }

    .tpd-thumbs img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid transparent;
        background: #eef1f6;
        cursor: pointer;
    }

    .tpd-thumbs img.active {
        border-color: var(--bs-primary)
    }

    /* Ringkasan kanan */
    .tpd-title {
        font-weight: 700;
        line-height: 1.25
    }

    .tpd-price {
        font-weight: 800;
        font-size: 1.25rem
    }

    .tpd-meta .badge {
        font-weight: 600
    }

    .tpd-fac {
        max-height: 7.5rem;
        overflow: auto
    }

    /* Chip fasilitas (opsional, simple) */
    .tpd-chip {
        display: inline-block;
        padding: .25rem .5rem;
        border-radius: 999px;
        background: #f1f3f5;
        font-size: .825rem;
        margin: .125rem .25rem .25rem 0
    }

    @media (min-width:992px) {
        .tpd-gallery {
            min-height: 360px
        }
    }

    @media (max-width:991.98px) {
        .tpd-gallery {
            min-height: 280px
        }
    }
</style>

@php
    // Kumpulkan slide: cover + galeri
    $__slides = [];
    if (!empty($property->image_path)) {
        $__slides[] = asset('storage/uploads/properties/covers/' . $property->image_path);
    }
    if (method_exists($property, 'images')) {
        foreach ($property->images ?? [] as $img) {
            if (!empty($img->image_path)) {
                $__slides[] = asset('storage/uploads/properties/gallery/' . $img->image_path);
            }
        }
    }
    if (empty($__slides)) {
        $__slides[] = 'https://placehold.co/800x800?text=No+Image';
    }
    $__carouselId = 'detailCarousel-' . $property->id;
    $__thumbsId = 'detailThumbs-' . $property->id;
@endphp

<div class="modal fade" id="modalDetail{{ $property->id }}" tabindex="-1" aria-hidden="true"
    aria-labelledby="modalDetailTitle{{ $property->id }}">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content tpd">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailTitle{{ $property->id }}">
                    {{ $property->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4 align-items-stretch">
                    {{-- KIRI: GALERI + THUMBS --}}
                    <div class="col-12 col-lg-5 d-flex flex-column">
                        <div class="tpd-gallery card border-0 flex-grow-1">
                            <div id="{{ $__carouselId }}" class="carousel slide" data-bs-ride="carousel"
                                data-bs-interval="4000" data-bs-touch="true">
                                <div class="carousel-inner">
                                    @foreach ($__slides as $i => $src)
                                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                            <img src="{{ $src }}" alt="slide-{{ $i + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                                @if (count($__slides) > 1)
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#{{ $__carouselId }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Prev</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#{{ $__carouselId }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                    <div class="carousel-indicators">
                                        @foreach ($__slides as $i => $src)
                                            <button type="button" data-bs-target="#{{ $__carouselId }}"
                                                data-bs-slide-to="{{ $i }}"
                                                class="{{ $i === 0 ? 'active' : '' }}"
                                                aria-label="Slide {{ $i + 1 }}"
                                                @if ($i === 0) aria-current="true" @endif></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Thumbnails --}}
                            @if (count($__slides) > 1)
                                <div class="tpd-thumbs mt-2" id="{{ $__thumbsId }}">
                                    @foreach ($__slides as $i => $src)
                                        <img src="{{ $src }}" alt="thumb-{{ $i + 1 }}"
                                            class="{{ $i === 0 ? 'active' : '' }}" data-to="{{ $i }}">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- KANAN: RINGKASAN + DETAIL --}}
                    <div class="col-12 col-lg-7">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-3">
                                <div class="tpd-title h4 mb-2">{{ $property->name }}</div>

                                <div class="tpd-meta d-flex flex-wrap gap-2">
                                    <span class="badge bg-label-primary">{{ ucfirst($property->type ?? '-') }}</span>
                                    <span class="badge bg-label-secondary">Kapasitas ±
                                        {{ $property->capacity ?? '-' }}</span>
                                    <span class="badge bg-label-info">Luas {{ $property->area ?? '-' }} m²</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small mb-1"><strong>Fasilitas</strong></div>
                                @php
                                    $facStr = trim((string) $property->facilities);
                                    $facArr = $facStr !== '' ? preg_split('/\s*,\s*/', $facStr) : [];
                                @endphp
                                @if (!empty($facArr))
                                    <div class="tpd-fac">
                                        @foreach ($facArr as $f)
                                            @if ($f !== '')
                                                <span class="tpd-chip">{{ $f }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="tpd-price">
                                    {{ $property->price ? 'Rp ' . number_format($property->price, 0, ',', '.') : 'Rp 0' }}
                                    <small class="text-muted fw-normal">/ hari</small>
                                </div>
                                
                            </div>

                            <div class="row g-3 small text-muted">
                                {{-- unit --}}
                                <div class="col-6">
                                    <div class="fw-semibold">Unit</div>
                                    <div>{{ $property->unit ?: '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-semibold">Tipe Ruangan</div>
                                    <div>{{ $property->room_type ?: '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-semibold">Dibuat</div>
                                    <div>{{ optional($property->created_at)->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-semibold">Status</div>
                                    <div>{{ ucfirst($property->status ?? '-') }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-semibold">Diperbarui</div>
                                    <div>{{ optional($property->updated_at)->format('d M Y H:i') ?: '-' }}</div>
                                </div>
                            </div>

                            {{-- Aksi (opsional) --}}
                            <div class="mt-4 d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Tutup</button>
                                {{-- Bisa tambahkan tombol "Edit" / "Pesan" lain jika perlu --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- /modal-body --}}
        </div>
    </div>
</div>

{{-- JS kecil: sinkronkan thumbnail & carousel --}}
<script>
    (function() {
        const cid = "{{ $__carouselId }}";
        const tid = "{{ $__thumbsId }}";
        const gal = document.getElementById(cid);
        const ths = document.getElementById(tid);

        if (gal && ths) {
            const inst = bootstrap.Carousel.getOrCreateInstance(gal);
            // klik thumbnail -> pindah slide
            ths.querySelectorAll('img[data-to]').forEach((img, i) => {
                img.addEventListener('click', () => inst.to(i));
            });
            // update kelas active di thumb saat slide berganti
            gal.addEventListener('slid.bs.carousel', (e) => {
                const idx = e.to;
                ths.querySelectorAll('img').forEach((im, j) => im.classList.toggle('active', j === idx));
            });
        }
    })();
</script>
