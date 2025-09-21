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

                        {{-- Upload Gambar --}}
                        <div class="col-12">
                            <label for="create_img" class="form-label">Gambar Ruangan (opsional)</label>
                            <input type="file" id="create_img" name="img" class="form-control"
                                accept="image/*">
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
                        <div class="col-12">
                            <label for="edit_img_{{ $property->id }}" class="form-label">Gambar Ruangan</label>
                            <input type="file" id="edit_img_{{ $property->id }}" name="img"
                                class="form-control" accept="image/*">

                            @if ($property->image_path)
                                <div class="mt-3">
                                    <img src="{{ asset('uploads/' . $property->image_path) }}" alt="Gambar Ruangan"
                                        class="img-thumbnail rounded shadow-sm"
                                        style="max-height: 300px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

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


{{-- =========================================================
  MODAL: DETAIL PROPERTY (per property)
  - Teks terpotong rapi, gambar responsif
  ========================================================== --}}
<div class="modal fade" id="modalDetail{{ $property->id }}" tabindex="-1" aria-hidden="true"
    aria-labelledby="modalDetailTitle{{ $property->id }}">
    <div class="modal-dialog modal-dialog-centered modal-modern">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailTitle{{ $property->id }}">
                    Detail Properti: {{ $property->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                {{-- Gambar --}}
                @if (!empty($property->image_path))
                    <div class="d-flex justify-content-center mb-3">
                        <img src="{{ asset('uploads/' . $property->image_path) }}" alt="{{ $property->name }}"
                            class="img-fluid rounded shadow-sm" style="max-height: 300px; object-fit: cover;">
                    </div>
                @else
                    <span class="text-muted">No Image</span>
                @endif

                <div class="row g-3">
                    <div class="col-6">
                        <strong>Tipe Ruangan:</strong><br>
                        <span class="text-break">{{ $property->room_type ?: '-' }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Luas:</strong><br>
                        {{ $property->area ?: '-' }} m²
                    </div>
                    <div class="col-12">
                        <strong>Fasilitas:</strong><br>
                        <div class="text-break" style="max-height: 6rem; overflow-y: auto;">
                            {{ $property->facilities ?: '-' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <strong>Harga:</strong><br>
                        {{ $property->price ? 'Rp ' . number_format($property->price, 0, ',', '.') : '-' }}
                    </div>
                    <div class="col-6">
                        <strong>Unit:</strong><br>
                        <span class="text-break">{{ $property->unit }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Jenis Ruangan:</strong><br>
                        <span class="badge bg-brand">{{ ucfirst($property->type) }}</span>
                    </div>
                    <div class="col-6">
                        <strong>Kapasitas:</strong><br>
                        <span class="badge bg-success">{{ $property->capacity }} orang</span>
                    </div>
                    <div class="col-6">
                        <strong>Dibuat pada:</strong><br>
                        {{ optional($property->created_at)->format('d M Y H:i') ?: '-' }}
                    </div>
                    <div class="col-6">
                        <strong>Diperbarui:</strong><br>
                        {{ optional($property->updated_at)->format('d M Y H:i') ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
