<div class="modal fade" id="modalTambahPenghuni-{{ $t->id }}" tabindex="-1"
    aria-labelledby="modalTambahPenghuniLabel-{{ $t->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-modern">
        <div class="modal-content">
            <form action="{{ route('transactions.DetailTransaction.store') }}" method="POST"
                id="formTambahPenghuni-{{ $t->id }}">
                @csrf

                {{-- Header --}}
                <div class="modal-header">
                    <div class="d-flex flex-column">
                        <h5 class="modal-title d-flex align-items-center gap-2 m-0"
                            id="modalTambahPenghuniLabel-{{ $t->id }}">
                            Pilih Kamar & Input Penghuni — {{ $t->properties->name }} ({{ $t->name }})
                        </h5>
                        <small class="text-muted mt-1">Ordered unit: {{ $t->ordered_unit }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    {{-- kirim info transaksi --}}
                    <input type="hidden" name="transaction_id" value="{{ $t->id }}">
                    <input type="hidden" name="start" value="{{ $t->start }}">
                    <input type="hidden" name="end" value="{{ $t->end }}">

                    {{-- Tabs lantai --}}
                    @if ($t->floors->isEmpty())
                        <div class="alert alert-warning small mb-3">
                            Tidak ada kamar dengan data lantai. Lengkapi kolom <em>lantai</em> pada data kamar.
                        </div>
                    @else
                        <ul class="nav nav-tabs mb-3 border-0" id="lantaiTab-{{ $t->id }}" role="tablist">
                            @foreach ($t->floors as $lantai => $roomsOnFloor)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }} fw-semibold"
                                        id="lantai-{{ $t->id }}-tab-{{ $lantai }}" data-bs-toggle="tab"
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
                                                <div class="card card-modern h-100">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="min-w-0">
                                                                <div class="fw-bold text-truncate">{{ $k->nama_kamar }}
                                                                </div>
                                                                <div class="text-muted small">
                                                                    Kapasitas: {{ $k->kapasitas }} · Lantai
                                                                    {{ $k->lantai }}
                                                                </div>
                                                            </div>

                                                            <div class="form-check ms-2">
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

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-modern" disabled>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts (tetap pakai logicmu; UI only) --}}
    @once
        @section('script')
            <script>
                // Saat modal dibuka, pastikan tombol submit disabled jika belum ada kamar dicentang
                document.addEventListener('shown.bs.modal', function(e) {
                    const m = e.target;
                    if (!m.id || !m.id.startsWith('modalTambahPenghuni-')) return;
                    const submitBtn = m.querySelector('button[type="submit"]');
                    const anyChecked = m.querySelectorAll('.room-check:checked').length > 0;
                    if (submitBtn) submitBtn.disabled = !anyChecked;
                });
            </script>

            {{-- ====== skripmu tetap jalan seperti semula; tidak diubah alurnya ====== --}}
            <script>
                document.addEventListener('change', function(e) {
                    if (!e.target.classList.contains('room-check')) return;

                    const modal = e.target.closest('.modal');
                    const formEl = modal.querySelector('form');
                    const wrap = modal.querySelector(e.target.dataset.target);
                    const cap = parseInt(e.target.dataset.capacity || '1', 10);
                    if (!wrap) return;

                    wrap.innerHTML = '';

                    if (!e.target.checked) {
                        toggleSubmit(modal);
                        return;
                    }

                    for (let i = 1; i <= Math.max(1, cap); i++) {
                        const group = document.createElement('div');
                        group.className = 'input-group input-group-sm mb-2';

                        const span = document.createElement('span');
                        span.className = 'input-group-text';
                        span.textContent = `#${i}`;

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = `nama_penghuni[${e.target.value}][]`;
                        input.className = 'form-control';
                        input.placeholder = `Nama penghuni ${i}`;
                        input.setAttribute('form', formEl.id);

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

                // (opsional) debug
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    const modal = form.closest('.modal');
                    if (!modal || !form.id?.startsWith('formTambahPenghuni-')) return;
                    // const fd = new FormData(form);
                    // for (const [k,v] of fd.entries()) console.log(k, '=>', v);
                });
            </script>

            <script>
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

                    const wrap = modal.querySelector(e.target.dataset.target);
                    const status = modal.querySelector(`#status-${txId}-${kamarId}`);
                    const cap = parseInt(e.target.dataset.capacity || '1', 10);

                    wrap.innerHTML = '';
                    status.innerHTML = '';

                    if (!e.target.checked) {
                        toggleSubmit(modal);
                        return;
                    }

                    const base = "{{ route('kamar.check', [':tx', ':kamar']) }}";
                    const url = base
                        .replace(':tx', encodeURIComponent(txId))
                        .replace(':kamar', encodeURIComponent(kamarId)) +
                        `?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;

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
                                input.setAttribute('form', formEl.id);

                                group.appendChild(span);
                                group.appendChild(input);
                                wrap.appendChild(group);
                            }

                            status.innerHTML = badge('Tersedia', 'success');
                        } else {
                            const bentroks = (data.conflicts || []).map(c => `${c.start}–${c.end}`).join(', ');
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
            </script>
        @endsection
    @endonce
</div>


{{-- form edit for peminjaman ruangan --}}
<div class="modal fade" id="modalDetailEdit{{ $t->id }}" tabindex="-1" data-bs-backdrop="static" role="dialog"
    aria-labelledby="editEventLabel-{{ $t->id }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg modal-modern" role="document">
        <div class="modal-content">
            <form action="{{ route('transactions.update', $t->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ $t->user_id }}">

                {{-- Header --}}
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editEventLabel-{{ $t->id }}">Edit Transaksi —
                            {{ $t->name }}</h5>
                        <small class="text-muted">Sesuaikan data pemohon, kegiatan, dokumen, atau status</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Tabs --}}
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
                        <div class="tab-pane fade show active" id="pemohon-{{ $t->id }}" role="tabpanel">
                            <div class="mb-3">
                                <label for="office-{{ $t->id }}" class="form-label">Instansi</label>
                                <input type="text" class="form-control" id="office-{{ $t->id }}"
                                    name="office" required value="{{ $t->instansi }}">
                            </div>

                            <div class="mb-3">
                                <label for="affiliation-{{ $t->id }}" class="form-label">Affiliation</label>
                                <select class="form-select" id="affiliation-{{ $t->id }}" name="affiliation"
                                    required>
                                    <option value="" disabled>{{ $t->affiliation }}</option>
                                    <option value="internal_pu"
                                        {{ $t->affiliation == 'internal_pu' ? 'selected' : '' }}>internal PU</option>
                                    <option value="external_pu"
                                        {{ $t->affiliation == 'external_pu' ? 'selected' : '' }}>external PU</option>
                                </select>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="phone_number-{{ $t->id }}" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone_number-{{ $t->id }}"
                                        name="phone_number" value="{{ $t->phone_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="email-{{ $t->id }}" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email-{{ $t->id }}"
                                        name="email" value="{{ $t->email }}">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="total_harga-{{ $t->id }}" class="form-label">
                                    Total Harga
                                    <small class="text-muted">(sebelumnya: Rp
                                        {{ number_format($t->total_harga) }})</small>
                                </label>
                                <input type="number" class="form-control" id="total_harga-{{ $t->id }}"
                                    name="total_harga" value="{{ $t->total_harga }}">
                            </div>
                        </div>

                        {{-- Detail Kegiatan --}}
                        <div class="tab-pane fade" id="kegiatan-{{ $t->id }}" role="tabpanel">
                            <div class="mb-3">
                                <label for="event-{{ $t->id }}" class="form-label">Kegiatan</label>
                                <input type="text" class="form-control" id="event-{{ $t->id }}"
                                    name="event" required value="{{ $t->kegiatan }}">
                            </div>

                            <div class="mb-3">
                                <label for="ordered_unit-{{ $t->id }}" class="form-label">Jumlah Unit</label>
                                <input type="number" class="form-control" id="ordered_unit-{{ $t->id }}"
                                    name="ordered_unit" min="1" value="{{ $t->ordered_unit }}" required>
                                <div class="form-text">Untuk tipe Aula dan Kelas, hanya tersedia 1 ruangan.</div>
                            </div>

                            <div class="mb-3">
                                <label for="description-{{ $t->id }}" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description-{{ $t->id }}" name="description" rows="3">{{ $t->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ruangan</label>
                                <select name="ruangan_id" class="form-select"
                                    id="ruanganSelect-{{ $t->id }}">
                                    @foreach ($ruangan as $r)
                                        <option value="{{ $r->id }}" data-price="{{ $r->price }}"
                                            {{ $r->id == $t->properties->id ? 'selected' : '' }}>
                                            {{ $r->name }} ({{ $r->capacity }} orang) — Rp
                                            {{ number_format($r->price) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start-{{ $t->id }}" class="form-label">Mulai</label>
                                    <input type="date" class="form-control" id="start-{{ $t->id }}"
                                        name="start" value="{{ $t->start }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="end-{{ $t->id }}" class="form-label">Selesai</label>
                                    <input type="date" class="form-control" id="end-{{ $t->id }}"
                                        name="end" value="{{ $t->end }}">
                                </div>
                            </div>
                        </div>

                        {{-- Dokumen --}}
                        <div class="tab-pane fade" id="dokumen-{{ $t->id }}" role="tabpanel">
                            {{-- Bukti Pembayaran --}}
                            <div class="mb-3 min-w-0">
                                <p class="mb-1 fw-semibold">Bukti Pembayaran</p>
                                @if ($t->payment_receipt)
                                    <a href="{{ asset('storage/uploads/payment_receipt/' . $t->payment_receipt) }}"
                                        target="_blank" class="text-break text-wrap d-inline-block">Download</a>
                                    <input type="hidden" name="old_payment_receipt"
                                        value="{{ $t->payment_receipt }}">
                                @else
                                    <em class="text-muted">Tidak ada</em>
                                @endif
                                <input type="file" name="payment_receipt" class="form-control mt-2"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            {{-- Surat Permohonan --}}
                            <div class="mb-3 min-w-0">
                                <p class="mb-1 fw-semibold">Surat Permohonan</p>
                                @if ($t->request_letter)
                                    <a href="{{ asset('storage/uploads/request_letter/' . $t->request_letter) }}"
                                        target="_blank" class="text-break text-wrap d-inline-block">Download</a>
                                    <input type="hidden" name="old_request_letter"
                                        value="{{ $t->request_letter }}">
                                @else
                                    <em class="text-muted">Tidak ada</em>
                                @endif
                                <input type="file" name="request_letter" class="form-control mt-2"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            {{-- Kode Pembayaran (Billing QR/File) --}}
                            <div class="mb-3 min-w-0">
                                <p class="mb-1 fw-semibold">Kode Pembayaran</p>
                                @if ($t->billing_qr)
                                    <a href="{{ asset('storage/uploads/billing_qr/' . $t->billing_qr) }}"
                                        target="_blank" class="text-break text-wrap d-inline-block">Download</a>
                                    <input type="hidden" name="old_billing_qr" value="{{ $t->billing_qr }}">
                                @else
                                    <em class="text-muted">Tidak ada</em>
                                @endif
                                <input type="file" id="billing_qr_doc-{{ $t->id }}" name="billing_qr"
                                    class="form-control mt-2" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                @error('billing_qr')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Surat Balasan --}}
                            <div class="mb-3 min-w-0">
                                <p class="mb-1 fw-semibold">Surat Balasan Permohonan</p>
                                @if ($t->response_letter)
                                    <a href="{{ asset('storage/uploads/response_letter/' . $t->response_letter) }}"
                                        target="_blank" class="text-break text-wrap d-inline-block">Download</a>
                                    <input type="hidden" name="old_response_letter"
                                        value="{{ $t->response_letter }}">
                                @else
                                    <em class="text-muted">Tidak ada</em>
                                @endif
                                <input type="file" id="response_letter_doc-{{ $t->id }}"
                                    name="response_letter" class="form-control mt-2"
                                    accept=".pdf,.jpg,.jpeg,.png,.webp">
                                @error('response_letter')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="tab-pane fade" id="status-{{ $t->id }}" role="tabpanel">
                            <div class="mb-3">
                                <label for="statusSelect-{{ $t->id }}" class="form-label">Status</label>
                                <select class="form-select" id="statusSelect-{{ $t->id }}" name="status">
                                    <option class="text-warning" value="pending"
                                        {{ $t->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option class="text-success" value="approved"
                                        {{ $t->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option class="text-info" value="waiting_payment"
                                        {{ $t->status == 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran
                                    </option>
                                    <option class="text-danger" value="rejected"
                                        {{ $t->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            {{-- Alasan Penolakan --}}
                            <div id="rejectionForm-{{ $t->id }}" style="display:none" class="mt-3">
                                <label for="rejection_reason-{{ $t->id }}" class="form-label">Alasan
                                    Penolakan</label>
                                <textarea id="rejection_reason-{{ $t->id }}" name="rejection_reason" class="form-control">{{ $t->rejection_reason ?? '' }}</textarea>
                            </div>

                            {{-- Upload billing saat Waiting Payment --}}
                            <div id="waitingPaymentForm-{{ $t->id }}" style="display:none" class="mt-3">
                                <div class="mb-3">
                                    <label for="billing_qr_status-{{ $t->id }}" class="form-label">File kode
                                        pembayaran</label>
                                    <input type="file" class="form-control"
                                        id="billing_qr_status-{{ $t->id }}" name="billing_qr"
                                        accept=".pdf,image/*">
                                    @if ($t->billing_qr)
                                        <p class="mt-2 mb-0">File saat ini:
                                            <a href="{{ asset('storage/uploads/billing_qr/' . $t->billing_qr) }}"
                                                target="_blank">Download</a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('toggle-remove')) return;
            const targetSel = e.target.getAttribute('data-target');
            const input = document.querySelector(targetSel);
            if (!input) return;
            input.disabled = e.target.checked; // centang -> nonaktifkan upload
        });
    </script>



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
        const statusSelect = document.getElementById("statusSelect-{{ $t->id }}");
        const rejectionForm = document.getElementById("rejectionForm-{{ $t->id }}");
        const waitingPaymentForm = document.getElementById("waitingPaymentForm-{{ $t->id }}");

        const docInput = document.getElementById("billing_qr_doc-{{ $t->id }}"); // di Dokumen
        const statusInput = document.getElementById("billing_qr_status-{{ $t->id }}"); // di Status

        const form = statusSelect?.closest("form");

        function setDefaultByStatus() {
            const isRejected = statusSelect.value === "rejected";
            const isWaiting = statusSelect.value === "waiting_payment";

            // show/hide section
            if (rejectionForm) rejectionForm.style.display = isRejected ? "block" : "none";
            if (waitingPaymentForm) waitingPaymentForm.style.display = isWaiting ? "block" : "none";

            // default aktif: waiting_payment -> statusInput, selain itu -> docInput
            if (docInput) {
                docInput.disabled = !!isWaiting;
                if (isWaiting) {
                    try {
                        docInput.value = "";
                    } catch (e) {}
                }
            }
            if (statusInput) {
                statusInput.disabled = !isWaiting;
                if (!isWaiting) {
                    try {
                        statusInput.value = "";
                    } catch (e) {}
                }
            }
        }

        function preferDocInput() {
            if (!docInput || !statusInput) return;
            // user pilih file di Dokumen -> aktifkan Dokumen, matikan Status
            if (docInput.files && docInput.files.length > 0) {
                statusInput.disabled = true;
                try {
                    statusInput.value = "";
                } catch (e) {}
                docInput.disabled = false;
            }
        }

        function preferStatusInput() {
            if (!docInput || !statusInput) return;
            // user pilih file di Status -> aktifkan Status, matikan Dokumen
            if (statusInput.files && statusInput.files.length > 0) {
                docInput.disabled = true;
                try {
                    docInput.value = "";
                } catch (e) {}
                statusInput.disabled = false;
            }
        }

        function beforeSubmitEnsureSingle() {
            if (!form) return;
            form.addEventListener('submit', function() {
                // kalau dua-duanya kosong, biarkan apa adanya (tidak mengubah file)
                const hasDoc = docInput && docInput.files && docInput.files.length > 0;
                const hasStatus = statusInput && statusInput.files && statusInput.files.length > 0;

                if (hasDoc && hasStatus) {
                    // kalau (jarang) keduanya terisi, prioritaskan input yang terakhir diubah:
                    // heuristik: pilih statusInput (skenario umum saat waiting_payment)
                    docInput.disabled = true;
                } else if (hasDoc) {
                    // kirim yang dokumen, disable status
                    if (statusInput) statusInput.disabled = true;
                } else if (hasStatus) {
                    // kirim yang status, disable dokumen
                    if (docInput) docInput.disabled = true;
                } else {
                    // tidak upload baru -> disable keduanya agar tidak kirim field kosong dobel
                    if (docInput) docInput.disabled = true;
                    if (statusInput) statusInput.disabled = true;
                }
            }, {
                once: true
            });
        }

        if (statusSelect) {
            statusSelect.addEventListener("change", setDefaultByStatus);
            setDefaultByStatus(); // set kondisi awal
        }

        if (docInput) docInput.addEventListener('change', preferDocInput);
        if (statusInput) statusInput.addEventListener('change', preferStatusInput);

        beforeSubmitEnsureSingle();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("modalDetailEdit{{ $t->id }}");
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
