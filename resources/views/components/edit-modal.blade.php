@if (isset($property))
    <div class="modal fade" id="modalCenter{{ $property->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- modal-lg biar lebih lega --}}
            <div class="modal-content shadow-lg border-0 rounded-3">
                {{-- Header --}}
                <div class="modal-header  text-white">
                    <h5 class="modal-title fw-bold">
                        Edit Ruangan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Form --}}
                <form action="{{ route('properties.update', $property->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PATCH')
                    @csrf

                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Nama Ruangan --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nama Ruangan</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $property->name) }}" required maxlength="32">
                            </div>

                            {{-- Jenis Sarana --}}
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">Jenis Sarana</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="aula" {{ $property->type == 'aula' ? 'selected' : '' }}>Aula
                                    </option>
                                    <option value="kelas" {{ $property->type == 'kelas' ? 'selected' : '' }}>Kelas
                                    </option>
                                    <option value="asrama" {{ $property->type == 'asrama' ? 'selected' : '' }}>Asrama
                                    </option>
                                    <option value="pavilion" {{ $property->type == 'pavilion' ? 'selected' : '' }}>
                                        Pavilion
                                    </option>
                                </select>
                            </div>

                            {{-- Kapasitas --}}
                            <div class="col-md-6">
                                <label for="capacity" class="form-label fw-semibold">Kapasitas</label>
                                <input type="number" class="form-control" id="capacity" name="capacity"
                                    value="{{ old('capacity', $property->capacity) }}" min="1" max="1000"
                                    required>
                            </div>

                            {{-- Tipe Ruangan --}}
                            <div class="col-md-6">
                                <label for="room_type" class="form-label fw-semibold">Tipe Ruangan</label>
                                <input type="text" class="form-control" id="room_type" name="room_type"
                                    value="{{ old('room_type', $property->room_type) }}" maxlength="50">
                            </div>

                            {{-- Luas Area --}}
                            <div class="col-md-6">
                                <label for="area" class="form-label fw-semibold">Luas Area (m²)</label>
                                <input type="text" class="form-control" id="area" name="area"
                                    value="{{ old('area', $property->area) }}" maxlength="50">
                            </div>

                            {{-- Harga --}}
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">Harga</label>
                                <input type="number" class="form-control" id="price" name="price"
                                    value="{{ old('price', $property->price) }}" min="0" step="0.01">
                            </div>

                            {{-- Unit --}}
                            <div class="col-md-6">
                                <label for="unit" class="form-label fw-semibold">Unit</label>
                                <input type="number" class="form-control" id="unit" name="unit"
                                    value="{{ old('unit', $property->unit) }}" min="0">
                            </div>

                            {{-- Fasilitas --}}
                            <div class="col-md-12">
                                <label for="facilities" class="form-label fw-semibold">Fasilitas</label>
                                <textarea class="form-control" id="facilities" name="facilities" rows="3">{{ old('facilities', $property->facilities) }}</textarea>
                            </div>

                            {{-- Upload Gambar --}}
                            <div class="col-md-12">
                                <label for="img" class="form-label fw-semibold">Gambar Ruangan</label>
                                <input type="file" class="form-control" id="img" name="img"
                                    accept="image/*">

                                @if ($property->image_path)
                                    <div class="mt-3">
                                        <img src="{{ asset('uploads/' . $property->image_path) }}"
                                            alt="Gambar Ruangan" class="img-thumbnail rounded shadow-sm"
                                            style="max-height: 300px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="modalCenter{{ $property->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('properties.update', $property->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @method('PATCH')
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $property->name) }}" required maxlength="32" />
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Jenis Sarana</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="kelas" {{ $property->type == 'kelas' ? 'selected' : '' }}>Kelas</option>
                                <option value="aula" {{ $property->type == 'aula' ? 'selected' : '' }}>Aula</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="capacity" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="capacity" name="capacity"
                                value="{{ old('capacity', $property->capacity) }}" min="1" max="1000"
                                required />
                        </div>

                        <div class="mb-3">
                            <label for="room_type" class="form-label">Tipe Ruangan</label>
                            <input type="text" class="form-control" id="room_type" name="room_type"
                                value="{{ old('room_type', $property->room_type) }}" maxlength="50" />
                        </div>

                        <div class="mb-3">
                            <label for="area" class="form-label">Luas Area (m²)</label>
                            <input type="text" class="form-control" id="area" name="area"
                                value="{{ old('area', $property->area) }}" maxlength="50" />
                        </div>

                        <div class="mb-3">
                            <label for="facilities" class="form-label">Fasilitas</label>
                            <textarea class="form-control" id="facilities" name="facilities" rows="3">{{ old('facilities', $property->facilities) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price"
                                value="{{ old('price', $property->price) }}" min="0" step="0.01" />
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="number" class="form-control" id="unit" name="unit"
                                value="{{ old('unit', $property->unit) }}" min="0" />
                        </div>

                        <div class="mb-3">
                            <label for="img" class="form-label">Gambar Ruangan</label>
                            <input type="file" class="form-control" id="img" name="img" accept="image/*" />

                            @if ($property->image_path)
                                <div class="mt-2">
                                    <img src="{{ asset('uploads/' . $property->image_path) }}" alt="Gambar Ruangan"
                                        width="100" />
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
@elseif(isset($t))
    <!-- Modal -->




    <div class="modal fade" id="modalTambahPenghuni-{{ $t->id }}" tabindex="-1"
        aria-labelledby="modalTambahPenghuniLabel-{{ $t->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <form action="{{ route('DetailTransaction.store') }}" method="POST"
                    id="formTambahPenghuni-{{ $t->id }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahPenghuniLabel-{{ $t->id }}">
                            Pilih Kamar & Input Penghuni — {{ $t->properties->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        {{-- kirim info transaksi --}}
                        <input type="hidden" name="transaction_id" value="{{ $t->id }}">
                        <input type="hidden" name="start" value="{{ $t->start }}">
                        <input type="hidden" name="end" value="{{ $t->end }}">

                        {{-- Tabs lantai (hanya yang punya lantai) --}}
                        @if ($t->floors->isEmpty())
                            <div class="alert alert-warning small mb-3">
                                Tidak ada kamar dengan data lantai. Lengkapi kolom <em>lantai</em> pada data kamar.
                            </div>
                        @else
                            <ul class="nav nav-tabs lantai-tabs mb-3" id="lantaiTab-{{ $t->id }}"
                                role="tablist">
                                @foreach ($t->floors as $lantai => $roomsOnFloor)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="lantai-{{ $t->id }}-tab-{{ $lantai }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#lantai-{{ $t->id }}-pane-{{ $lantai }}"
                                            type="button" role="tab"
                                            aria-controls="lantai-{{ $t->id }}-pane-{{ $lantai }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            Lantai {{ $lantai }}
                                            <span class="badge bg-secondary ms-1">{{ $roomsOnFloor->count() }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content" id="lantaiTabContent-{{ $t->id }}">
                                @foreach ($t->floors as $lantai => $roomsOnFloor)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="lantai-{{ $t->id }}-pane-{{ $lantai }}" role="tabpanel"
                                        aria-labelledby="lantai-{{ $t->id }}-tab-{{ $lantai }}"
                                        tabindex="0">

                                        <div class="row g-3">
                                            @foreach ($roomsOnFloor as $k)
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                    <div class="border rounded-3 p-3 h-100">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <div class="fw-bold">{{ $k->nama_kamar }}</div>
                                                                <div class="text-muted small">
                                                                    Kapasitas: {{ $k->kapasitas }} · Lantai
                                                                    {{ $k->lantai }}
                                                                </div>
                                                            </div>

                                                            <div class="form-check">
                                                                <input class="form-check-input room-check"
                                                                    type="checkbox"
                                                                    id="room-{{ $t->id }}-{{ $k->id }}"
                                                                    name="kamar_id[]" value="{{ $k->id }}"
                                                                    data-capacity="{{ (int) $k->kapasitas }}"
                                                                    data-target="#penghuni-wrap-{{ $t->id }}-{{ $k->id }}">
                                                            </div>
                                                        </div>

                                                        <div id="status-{{ $t->id }}-{{ $k->id }}"
                                                            class="small mt-2"></div>
                                                        <div id="penghuni-wrap-{{ $t->id }}-{{ $k->id }}"
                                                            class="mt-2"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif




                        <small class="text-muted d-block mt-3">
                            Centang kamar yang akan dipakai, lalu isi nama penghuni (maksimal sesuai kapasitas kamar).
                        </small>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @once
            @section('script')
                <script>
                    document.addEventListener('change', function(e) {
                        if (!e.target.classList.contains('room-check')) return;

                        const modal = e.target.closest('.modal');
                        const formEl = modal.querySelector('form'); // <-- ambil form ID yg benar
                        const wrap = modal.querySelector(e.target.dataset.target);
                        const cap = parseInt(e.target.dataset.capacity || '1', 10);
                        if (!wrap) return;

                        wrap.innerHTML = '';

                        if (!e.target.checked) {
                            toggleSubmit(modal);
                            return;
                        }

                        for (let i = 1; i <= Math.max(1, cap); i++) {
                            // bikin group
                            const group = document.createElement('div');
                            group.className = 'input-group input-group-sm mb-2';

                            // bikin span #
                            const span = document.createElement('span');
                            span.className = 'input-group-text';
                            span.textContent = `#${i}`;

                            // bikin input
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.name = `nama_penghuni[${e.target.value}][]`; // <--- penting: name bersarang per kamar
                            input.className = 'form-control';
                            input.placeholder = `Nama penghuni ${i}`;

                            // trik penting: force asosiasi ke form
                            input.setAttribute('form', formEl.id); // <--- kunci agar masuk ke request

                            group.appendChild(span);
                            group.appendChild(input);
                            wrap.appendChild(group);
                        }

                        toggleSubmit(modal);
                    });

                    function toggleSubmit(modal) {
                        if (!modal) return;
                        const anyChecked = modal.querySelectorAll('.room-check:checked').length > 0;
                        const submitBtn = modal.querySelector('button[type="submit"]');
                        if (submitBtn) submitBtn.disabled = !anyChecked;
                    }

                    // (opsional) debug: lihat apa yang akan terkirim
                    document.addEventListener('submit', function(e) {
                        const form = e.target;
                        const modal = form.closest('.modal');
                        if (!modal || !form.id?.startsWith('formTambahPenghuni-')) return;

                        // uncomment kalau mau cek di console
                        // const fd = new FormData(form);
                        // for (const [k,v] of fd.entries()) console.log(k, '=>', v);
                    });
                </script>
                <script>
                    // helper: buat badge cepat
                    function badge(html, type = 'secondary') {
                        return `<span class="badge bg-${type}">${html}</span>`;
                    }

                    document.addEventListener('change', async function(e) {
                        if (!e.target.classList.contains('room-check')) return;

                        const modal = e.target.closest('.modal');
                        const formEl = modal.querySelector('form');
                        const txId = modal.querySelector('input[name="transaction_id"]').value;
                        const start = modal.querySelector('input[name="start"]').value;
                        const end = modal.querySelector('input[name="end"]').value;
                        const kamarId = e.target.value;

                        const wrap = modal.querySelector(e.target.dataset.target); // penghuni-wrap
                        const status = modal.querySelector(`#status-${txId}-${kamarId}`);
                        const cap = parseInt(e.target.dataset.capacity || '1', 10);

                        // reset area
                        wrap.innerHTML = '';
                        status.innerHTML = '';

                        // kalau uncheck: cukup update tombol submit
                        if (!e.target.checked) {
                            toggleSubmit(modal);
                            return;
                        }

                        // panggil route check
                        const base = "{{ route('kamar.check', [':tx', ':kamar']) }}";
                        const url = base
                            .replace(':tx', encodeURIComponent(txId))
                            .replace(':kamar', encodeURIComponent(kamarId)) +
                            `?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;

                        // loading state kecil
                        status.innerHTML = badge('Cek...', 'light');

                        try {
                            const res = await fetch(url);
                            const data = await res.json();

                            if (!data.ok) {
                                status.innerHTML = badge(data.message || 'Gagal cek', 'danger');
                                e.target.checked = false;
                                toggleSubmit(modal);
                                return;
                            }

                            if (data.available) {
                                // render input nama penghuni sesuai kapasitas
                                for (let i = 1; i <= Math.max(1, cap); i++) {
                                    const group = document.createElement('div');
                                    group.className = 'input-group input-group-sm mb-2';

                                    const span = document.createElement('span');
                                    span.className = 'input-group-text';
                                    span.textContent = `#${i}`;

                                    const input = document.createElement('input');
                                    input.type = 'text';
                                    input.name = `nama_penghuni[${kamarId}][]`;
                                    input.className = 'form-control';
                                    input.placeholder = `Nama penghuni ${i}`;
                                    input.setAttribute('form', formEl.id); // force ikut submit

                                    group.appendChild(span);
                                    group.appendChild(input);
                                    wrap.appendChild(group);
                                }

                                status.innerHTML = badge('Tersedia', 'success');
                            } else {
                                // tampilkan konflik lalu uncheck
                                const bentroks = (data.conflicts || [])
                                    .map(c => `${c.start}–${c.end}`)
                                    .join(', ');
                                status.innerHTML = badge('Bentrok', 'danger') +
                                    (bentroks ? ` <div class="text-muted mt-1">(${bentroks})</div>` : '');
                                e.target.checked = false;
                            }

                        } catch (err) {
                            console.error(err);
                            status.innerHTML = badge('Error cek ruangan', 'danger');
                            e.target.checked = false;
                        }

                        toggleSubmit(modal);
                    });

                    // aktif/nonaktifkan tombol submit per modal
                    function toggleSubmit(modal) {
                        if (!modal) return;
                        const anyChecked = modal.querySelectorAll('.room-check:checked').length > 0;
                        const submitBtn = modal.querySelector('button[type="submit"]');
                        if (submitBtn) submitBtn.disabled = !anyChecked;
                    }
                </script>
            @endsection
        @endonce
    </div>


    {{-- Modal Detail As User --}}
    <div class="modal fade" id="modalDetailAsUser{{ $t->id }}" tabindex="-1">
        {{-- Fullscreen di HP, besar di desktop --}}
        <div class="modal-dialog modal-lg  modal-dialog-centered">

            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Detail Transaksi - {{ $t->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <p><strong>Instansi:</strong> {{ $t->instansi }}</p>
                            <p><strong>Affiliation:</strong> {{ $t->affiliation }}</p>
                            <p><strong>Phone:</strong> {{ $t->phone_number }}</p>
                            <p><strong>Email:</strong> {{ $t->email }}</p>
                            <p><strong>Description:</strong> {{ $t->description }}</p>
                            <p><strong>Unit:</strong> {{ $t->ordered_unit }}</p>
                            <p><strong>Total Harga:</strong> Rp. {{ number_format($t->total_harga, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-12 col-md-6">

                            {{-- Payment Receipt --}}
                            <p class="mb-1"><strong>Bukti Pembayaran:</strong>
                                @if ($t->payment_receipt)
                                    <a href="{{ asset('storage/uploads/payment_receipt/' . $t->payment_receipt) }}"
                                        target="_blank">Download</a>
                                @else
                                    <em>Tidak ada</em>
                                @endif
                            </p>
                            <form action="{{ route('transactions.payment', $t->id) }}" method="POST"
                                enctype="multipart/form-data" class="mb-3">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="payment_receipt" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </div>
                            </form>

                            {{-- Request Letter --}}
                            <p class="mt-3 mb-1"><strong>Surat Permohonan: </strong>
                                @if ($t->request_letter)
                                    <a href="{{ asset('/storage/uploads/request_letter/' . $t->request_letter) }}"
                                        target="_blank">Download</a>
                                @else
                                    <em>Tidak ada</em>
                                @endif
                            </p>
                            <form action="{{ route('transactions.request_letter', $t->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="request_letter" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </div>
                            </form>

                            {{-- Status --}}
                            <p class="mt-3 mb-1"><strong>Status :</strong>
                                @if ($t->status == 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif ($t->status == 'waiting_payment')
                                    <span class="badge bg-info">Menunggu Pembayaran</span>
                                @elseif ($t->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif ($t->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </p>
                            @if ($t->status == 'approved' && ($t->properties->type == 'kamar' || $t->properties->type == 'asrama'))

                                <p class="mt-3 mb-1"><strong>nama kamar:</strong></p>
                                <ul>
                                    @foreach ($t->detailKamars as $k)
                                        <li>{{ $k->kamar->nama_kamar }}</li>
                                    @endforeach
                                </ul>
                            @endif


                            @if ($t->status == 'rejected')
                                <p class="mt-3 mb-1"><strong>Alasan Penolakan:</strong> {{ $t->rejection_reason }}</p>
                            @endif
                            @if ($t->status == 'waiting_payment')
                                <div class="mt-3">
                                    <p class="mb-1"><strong>Instruksi Pembayaran:</strong></p>

                                    {{-- Billing Code --}}
                                    <p class="mb-1 mt-1">
                                        Kode Billing: <code>{{ $t->billing_code }}</code>
                                    </p>

                                    {{-- Billing QR Download --}}
                                    <p class="mb-1 mt-1">
                                        <a href="{{ asset('storage/uploads/billing_qr/' . $t->billing_qr) }}"
                                            target="_blank" class="btn btn-sm btn-primary">
                                            Download code
                                        </a>
                                    </p>
                                </div>
                            @endif



                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <p><strong>Mulai Acara:</strong> {{ $t->start }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <p><strong>Sampai Acara:</strong> {{ $t->end }}</p>
                        </div>
                    </div>

                    <hr class="mt-0">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <p><strong>Dipesan pada:</strong> {{ $t->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail{{ $t->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Detail Transaksi - {{ $t->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>Instansi:</strong> {{ $t->instansi }}</p>
                            <p><strong>Affiliation:</strong> {{ $t->affiliation }}</p>
                            <p><strong>Phone:</strong> {{ $t->phone_number }}</p>
                            <p><strong>Email:</strong> {{ $t->email }}</p>
                            <p><strong>Description:</strong> {{ $t->description }}</p>
                            <p><strong>Unit:</strong> {{ $t->ordered_unit }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Bukti Pembayaran:</strong>
                                @if ($t->payment_receipt)
                                    <a href="{{ asset('storage/uploads/payment_receipt/' . $t->payment_receipt) }}"
                                        target="_blank">Download</a>
                                @else
                                    <em>Tidak ada</em>
                                @endif
                            </p>
                            <p><strong>Surat Permohonan:</strong>
                                @if ($t->request_letter)
                                    <a href="{{ asset('/storage/uploads/request_letter/' . $t->request_letter) }}"
                                        target="_blank">Download</a>
                                @else
                                    <em>Tidak ada</em>
                                @endif
                            </p>
                            @if ($t->status == 'rejected')
                                <p class="mt-3 mb-1 text-wrap">
                                    <strong>Alasan Penolakan:</strong> {{ $t->rejection_reason }}
                                </p>
                            @endif
                            @if ($t->status == 'waiting_payment')
                                <div class="mt-3">


                                    {{-- Billing Code --}}
                                    <p class="mb-1 mt-1">
                                        <strong> Kode Billing: </strong><code>{{ $t->billing_code }}</code>
                                    </p>

                                    {{-- Billing QR Download --}}
                                    <p class="mb-1 mt-1">
                                        <a href="{{ asset('storage/uploads/billing_qr/' . $t->billing_qr) }}"
                                            target="_blank" class="btn btn-sm btn-primary">
                                            Download code
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <p><strong>Mulai Acara:</strong> {{ $t->start }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Sampai Acara:</strong> {{ $t->end }}</p>
                        </div>

                    </div>
                    <hr style="margin-top: 0px;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>Dipesan pada:</strong> {{ $t->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Status Dropdown (Jangan diubah tombolnya) --}}
                    <div class="dropdown">
                        @if ($t->status == 'pending')
                            <button class="btn btn-warning dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Menunggu
                            </button>
                        @elseif ($t->status == 'approved')
                            <button class="btn btn-success dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Disetujui
                            </button>
                        @elseif ($t->status == 'waiting_payment')
                            <button class="btn btn-info dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Menunggu Pembayaran
                            </button>
                        @elseif ($t->status == 'rejected')
                            <button class="btn btn-danger dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Ditolak
                            </button>
                        @endif
                        <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                            <li>
                                <form method="POST" action=" {{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="pending"
                                        class="dropdown-item text-warning">
                                        menunggu
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="approved"
                                        class="dropdown-item text-success">
                                        Disetujui
                                    </button>
                                </form>
                            </li>
                            <li>
                                <!-- Button to trigger modal -->
                                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" class="dropdown-item text-info" data-bs-toggle="modal"
                                        data-bs-target="#waitingPaymentModal-{{ $t->id }}">
                                        Menunggu Pembayaran
                                    </button>
                                </form>
                            </li>
                            <li>
                                <!-- Button to trigger modal -->
                                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal-{{ $t->id }}">
                                        Ditolak
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="waitingPaymentModal-{{ $t->id }}" tabindex="-1"
        aria-labelledby="waitingPaymentLabel-{{ $t->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="waitingPaymentLabel-{{ $t->id }}">Billing Information
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="billing_code-{{ $t->id }}" class="form-label">Billing
                                    Code</label>
                                <input type="text" class="form-control" id="billing_code-{{ $t->id }}"
                                    name="billing_code" required>
                            </div>

                            <div class="mb-3">
                                <label for="billing_qr-{{ $t->id }}" class="form-label">Billing QR /
                                    Barcode</label>
                                <input type="file" class="form-control" id="billing_qr-{{ $t->id }}"
                                    name="billing_qr" accept=".pdf,image/*">
                                <small class="text-muted">Optional — PDF or Image allowed</small>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="status" value="waiting_payment">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="rejectModal-{{ $t->id }}" tabindex="-1"
        aria-labelledby="rejectModalLabel-{{ $t->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel-{{ $t->id }}">Rejection Reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason-{{ $t->id }}" class="form-label">Please provide a
                                reason:</label>
                            <textarea class="form-control" id="rejection_reason-{{ $t->id }}" name="rejection_reason" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="status" value="rejected">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- form edit for peminjaman ruangan --}}
    <div class="modal fade" id="modalCenter{{ $t->id }}" tabindex="-1" data-bs-backdrop="static"
        role="dialog" aria-labelledby="editEventLabel" aria-hidden="true">


        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('transactions.ruangan.update', $t->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $t->user_id }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editEventLabel">Edit Transaksi - {{ $t->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <ul class="nav nav-tabs border-0" id="editTab{{ $t->id }}" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active border-0 border-bottom border-3 border-primary fw-semibold text-primary"
                                    id="pemohon-tab-{{ $t->id }}" data-bs-toggle="tab"
                                    data-bs-target="#pemohon-{{ $t->id }}" type="button" role="tab">
                                    Data Pemohon
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link border-0 fw-semibold text-secondary"
                                    id="kegiatan-tab-{{ $t->id }}" data-bs-toggle="tab"
                                    data-bs-target="#kegiatan-{{ $t->id }}" type="button" role="tab">
                                    Detail Kegiatan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link border-0 fw-semibold text-secondary"
                                    id="dokumen-tab-{{ $t->id }}" data-bs-toggle="tab"
                                    data-bs-target="#dokumen-{{ $t->id }}" type="button" role="tab">
                                    Dokumen
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link border-0 fw-semibold text-secondary"
                                    id="status-tab-{{ $t->id }}" data-bs-toggle="tab"
                                    data-bs-target="#status-{{ $t->id }}" type="button" role="tab">
                                    Status
                                </button>
                            </li>





                        </ul>




                        {{-- Tab Content --}}
                        <div class="tab-content mt-3">

                            {{-- Data Pemohon --}}
                            <div class="tab-pane fade show active" id="pemohon-{{ $t->id }}"
                                role="tabpanel">
                                <div class="mb-3">
                                    <label for="office" class="form-label">Instansi</label>
                                    <input type="text" class="form-control" id="office" name="office"
                                        required value="{{ $t->instansi }}">
                                </div>
                                {{-- select affiliation --}}
                                <div class="mb-3">
                                    <label for="affiliation" class="form-label">Affiliation</label>
                                    <select class="form-select" id="affiliation" name="affiliation" required>
                                        <option value="" selected disabled>{{ $t->affiliation }}</option>
                                        <option value="internal_pu"
                                            {{ $t->affiliation == 'internal_pu' ? 'selected' : '' }}>
                                            internal PU</option>
                                        <option value="external_pu"
                                            {{ $t->affiliation == 'external_pu' ? 'selected' : '' }}>
                                            external PU</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        value="{{ $t->phone_number }}">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $t->email }}">
                                </div>
                                {{-- total harga --}}
                                <div class="mb-3">
                                    <label for="total_harga" class="form-label">
                                        Total Harga
                                        <small class="text-muted fs-6">
                                            (sebelumnya: Rp {{ number_format($t->total_harga) }})
                                        </small>

                                    </label>
                                    <input type="number" class="form-control" id="total_harga-{{ $t->id }}"
                                        name="total_harga" value="{{ $t->total_harga }}">
                                </div>






                            </div>

                            {{-- Detail Kegiatan --}}
                            <div class="tab-pane fade" id="kegiatan-{{ $t->id }}" role="tabpanel">
                                <div class="mb-3">
                                    <label for="event" class="form-label">Kegiatan</label>
                                    <input type="text" class="form-control" id="event" name="event"
                                        required value="{{ $t->kegiatan }}">
                                </div>


                                <div class="mb-3">
                                    <label for="ordered_unit" class="form-label">Jumlah Unit</label>
                                    <input type="number" class="form-control" id="ordered_unit" name="ordered_unit"
                                        min="1" value="{{ $t->ordered_unit }}" required>

                                    <span class="mt-1 text-sm text-danger">Untuk tipe Aula dan Kelas, hanya ada 1
                                        Ruangan</span>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ $t->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ruangan</label>
                                    <select name="ruangan_id" class="form-select"
                                        id="ruanganSelect-{{ $t->id }}">
                                        @foreach ($ruangan as $r)
                                            <option value="{{ $r->id }}" data-price="{{ $r->price }}"
                                                {{ $r->id == $t->properties->id ? 'selected' : '' }}>
                                                {{ $r->name }} ({{ $r->capacity }} orang) - Rp
                                                {{ number_format($r->price) }}
                                            </option>
                                        @endforeach
                                    </select>




                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="start" class="form-label">Mulai</label>
                                        <input type="date" class="form-control" id="start" name="start"
                                            value="{{ $t->start }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end" class="form-label">Selesai</label>
                                        <input type="date" class="form-control" id="end" name="end"
                                            value="{{ $t->end }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Dokumen --}}
                            <div class="tab-pane fade" id="dokumen-{{ $t->id }}" role="tabpanel">
                                <div class="mb-3">
                                    <p><strong>Bukti Pembayaran:</strong></p>
                                    @if ($t->payment_receipt)
                                        <a href="{{ asset('storage/uploads/payment_receipt/' . $t->payment_receipt) }}"
                                            target="_blank">Download</a>
                                        <input type="hidden" name="old_payment_receipt"
                                            value="{{ $t->payment_receipt }}">
                                    @else
                                        <em>Tidak ada</em>
                                    @endif
                                    <input type="file" name="payment_receipt" class="form-control mt-2"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>

                                <div class="mb-3">
                                    <p><strong>Surat Permohonan:</strong></p>
                                    @if ($t->request_letter)
                                        <a href="{{ asset('storage/uploads/request_letter/' . $t->request_letter) }}"
                                            target="_blank">Download</a>
                                        <input type="hidden" name="old_request_letter"
                                            value="{{ $t->request_letter }}">
                                    @else
                                        <em>Tidak ada</em>
                                    @endif
                                    <input type="file" name="request_letter" class="form-control mt-2"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="tab-pane fade" id="status-{{ $t->id }}" role="tabpanel">
                                <div class="mb-3">
                                    <label for="statusSelect-{{ $t->id }}" class="form-label">Status</label>
                                    <select class="form-select" id="statusSelect-{{ $t->id }}"
                                        name="status">
                                        <option class="text-warning" value="pending"
                                            {{ $t->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                        <option class="text-success" value="approved"
                                            {{ $t->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                        <option class="text-info" value="waiting_payment"
                                            {{ $t->status == 'waiting_payment' ? 'selected' : '' }}>Menunggu
                                            Pembayaran</option>
                                        <option class="text-danger" value="rejected"
                                            {{ $t->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>

                                {{-- Rejection Reason --}}
                                <div id="rejectionForm-{{ $t->id }}" style="display: none;" class="mt-3">
                                    <label for="rejection_reason-{{ $t->id }}" class="form-label">Alasan
                                        Penolakan</label>
                                    <textarea id="rejection_reason-{{ $t->id }}" name="rejection_reason" class="form-control">{{ $t->rejection_reason ?? '' }}</textarea>
                                </div>

                                {{-- Billing Information --}}
                                <div id="waitingPaymentForm-{{ $t->id }}" style="display: none;"
                                    class="mt-3">
                                    <div class="mb-3">
                                        <label for="billing_code-{{ $t->id }}" class="form-label">Code
                                            Pembayaran</label>
                                        <input type="text" class="form-control"
                                            id="billing_code-{{ $t->id }}" name="billing_code"
                                            value="{{ $t->billing_code ?? '' }}">
                                    </div>

                                    <div class="mb-3">
                                        <label for="billing_qr-{{ $t->id }}" class="form-label">QR
                                            Code/Barcode</label>
                                        <input type="file" class="form-control"
                                            id="billing_qr-{{ $t->id }}" name="billing_qr"
                                            accept=".pdf,image/*">
                                        <small class="text-muted">Optional — PDF or Image allowed</small>
                                        @if ($t->billing_qr)
                                            <p class="mt-2">Current File:
                                                <a href="{{ asset('storage/uploads/billing_qr/' . $t->billing_qr) }}"
                                                    target="_blank">Download</a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
    @once
        <script>
            document.addEventListener('click', async function(e) {
                const btn = e.target.closest('.cek-ruangan-btn');
                if (!btn) return;

                const txId = btn.dataset.tx;
                const kamarId = btn.dataset.kamar;

                const li = btn.closest('li');
                const modal = btn.closest('.modal');
                const cb = li.querySelector('.kamar-checkbox');

                const startVal = modal?.querySelector('input[name="start"]')?.value;
                const endVal = modal?.querySelector('input[name="end"]')?.value;

                if (!startVal || !endVal) {
                    alert('Isi tanggal mulai & selesai dulu ya ✋');
                    return;
                }

                // build URL dari route
                const base = "{{ route('kamar.check', [':tx', ':kamar']) }}";
                const url = base
                    .replace(':tx', encodeURIComponent(txId))
                    .replace(':kamar', encodeURIComponent(kamarId)) +
                    `?start=${encodeURIComponent(startVal)}&end=${encodeURIComponent(endVal)}`;

                // loading state
                btn.disabled = true;
                const prev = btn.innerText;
                btn.innerText = 'Cek...';

                try {
                    const res = await fetch(url);
                    const data = await res.json();

                    if (!data.ok) {
                        alert(data.message || 'Gagal cek');
                        return;
                    }

                    const badge = li.querySelector('.badge') || document.createElement('span');
                    if (!badge.className) {
                        badge.className = 'badge ms-2'; // ms-2 = margin-left kecil
                        btn.insertAdjacentElement('afterend', badge); // ⬅ ganti appendChild jadi after btn
                    }


                    if (data.available) {
                        // ✅ buka kunci & centang
                        cb.disabled = false;
                        cb.checked = true;
                        cb.dataset.validated = '1';
                        badge.className = 'badge bg-success ms-2';
                        badge.textContent = 'Tersedia';
                        alert(`✅ Kamar tersedia (${data.start} s/d ${data.end})`);
                    } else {
                        // ❌ tetap kunci & pastikan tidak tercentang
                        cb.checked = false;
                        cb.disabled = true;
                        cb.dataset.validated = '0';
                        badge.className = 'badge bg-danger ms-2';
                        badge.textContent = 'Bentrok';
                        const bentrok = data.conflicts.map(c => `${c.start}–${c.end}`).join(', ');
                        alert(`❌ Bentrok: ${bentrok}`);
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi error saat cek ruangan');
                } finally {
                    btn.disabled = false;
                    btn.innerText = prev;
                }
            });

            // Jika tanggal diubah, reset validasi (wajib cek ulang)
            document.addEventListener('input', function(e) {
                if (!['start', 'end'].includes(e.target.name)) return;
                const modal = e.target.closest('.modal');
                modal.querySelectorAll('.kamar-checkbox').forEach(cb => {
                    const pre = cb.dataset.preselected === '1';
                    if (pre) {
                        // yang sudah ada di transaksi boleh tetap aktif (biar bisa uncheck)
                        cb.dataset.validated = '1';
                    } else {
                        cb.checked = false;
                        cb.disabled = true;
                        cb.dataset.validated = '0';
                        const li = cb.closest('li');
                        const badge = li.querySelector('.badge');
                        if (badge) {
                            badge.remove();
                        }
                    }
                });
            });
        </script>
    @endonce





    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#editTab{{ $t->id }} .nav-link');

            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    // Hapus style biru dari semua tab
                    tabs.forEach(btn => {
                        btn.classList.remove('border-bottom', 'border-3', 'border-primary',
                            'text-primary');
                        btn.classList.add('text-secondary');
                    });

                    // Tambahkan ke tab aktif
                    e.target.classList.add('border-bottom', 'border-3', 'border-primary',
                        'text-primary');
                    e.target.classList.remove('text-secondary');
                });
            });
        });
    </script>

    {{-- Script untuk toggle rejection/billing --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let statusSelect = document.getElementById("statusSelect-{{ $t->id }}");
            let rejectionForm = document.getElementById("rejectionForm-{{ $t->id }}");
            let waitingPaymentForm = document.getElementById("waitingPaymentForm-{{ $t->id }}");


            function toggleForms() {
                rejectionForm.style.display = (statusSelect.value === "rejected") ? "block" : "none";
                waitingPaymentForm.style.display = (statusSelect.value === "waiting_payment") ? "block" : "none";


            }

            statusSelect.addEventListener("change", toggleForms);
            toggleForms();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("modalCenter{{ $t->id }}");
            if (!modal) return;

            const startInput = modal.querySelector("#start");
            const endInput = modal.querySelector("#end");
            const unitInput = modal.querySelector("#ordered_unit");
            const affiliationSelect = modal.querySelector("#affiliation");
            const ruanganSelect = modal.querySelector("#ruanganSelect-{{ $t->id }}");
            const totalHargaInput = modal.querySelector("#total_harga-{{ $t->id }}");

            function calculateTotal() {
                const afiliasi = affiliationSelect.value;
                const start = new Date(startInput.value);
                const end = new Date(endInput.value);
                const unit = parseInt(unitInput.value) || 1;

                const selectedOption = ruanganSelect.options[ruanganSelect.selectedIndex];
                const pricePerDay = parseInt(selectedOption?.dataset.price) || 0;

                // internal PU gratis
                if (afiliasi === "internal_pu") {
                    totalHargaInput.value = 0;
                    return;
                }

                if (isNaN(start.getTime()) || isNaN(end.getTime()) || end < start) {
                    // jangan override total_harga lama
                    return;
                }

                // hitung total tanpa syarat ruangan
                const diffTime = end - start;
                const diffDays = diffTime / (1000 * 60 * 60 * 24) + 1;
                const total = diffDays * pricePerDay * unit;
                totalHargaInput.value = total;
            }



            // listener untuk semua input yang berpengaruh
            startInput.addEventListener("change", calculateTotal);
            endInput.addEventListener("change", calculateTotal);
            unitInput.addEventListener("input", calculateTotal);
            affiliationSelect.addEventListener("change", calculateTotal);
            ruanganSelect.addEventListener("change", calculateTotal);

            // hitung pertama kali saat modal dibuka
            calculateTotal();
        });
    </script>
@else
    {{-- form edit for penghuni wisma --}}
    <div class="modal fade" id="modalCenter{{ $wisma->id }}" tabindex="-1" data-bs-backdrop="static"
        role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('transactions.wisma.update', $wisma->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventLabel">Edit Penghuni Wisma</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="basic-icon-default-fullname">Nama</label>
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                        class="bx bx-user"></i></span>
                                <input type="text" class="form-control" id="basic-icon-default-fullname"
                                    placeholder="Tulis disini ..." aria-label="John Doe"
                                    aria-describedby="basic-icon-default-fullname2" name="name"
                                    value="{{ $wisma->name }}" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-icon-default-fullname">Asal</label>
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                        class="bx bx-location-plus"></i></span>
                                <input type="text" class="form-control" id="basic-icon-default-fullname"
                                    placeholder="Tulis disini ..." aria-label="John Doe"
                                    aria-describedby="basic-icon-default-fullname2" name="asal"
                                    value="{{ $wisma->from }}" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="basic-icon-default-kegiatan">Kegiatan</label>
                            <div class="input-group input-group-merge">
                                <span id="basic-icon-default-kegiatan2" class="input-group-text"><i
                                        class="bx bx-book-alt"></i></span>
                                <input type="text" class="form-control" id="basic-icon-default-kegiatan"
                                    placeholder="Tulis disini ..." aria-label="John Doe"
                                    aria-describedby="basic-icon-default-kegiatan2" name="kegiatan"
                                    value="{{ $wisma->kegiatan }}" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="basic-select">Wisma</label>
                            <input id="rooms" type="text" class="form-control"
                                placeholder="Cek ketersediaan..." aria-label="John Doe"
                                aria-describedby="basic-icon-default-fullname2" readonly
                                value="{{ $wisma->room }}" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
