{{-- delete --}}
<div class="modal fade" id="modalDeleteRooms" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Hapus ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah anda yakin ingin menghapus data ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>


{{-- Modal Detail As User --}}
<div class="modal fade" id="modalDetailAsUser{{ $t->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Detail Transaksi - {{ $t->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <p class="text-break"><strong>Instansi:</strong> {{ $t->instansi }}</p>
                        <p><strong>Affiliation:</strong> {{ $t->affiliation }}</p>
                        <p><strong>Phone:</strong> {{ $t->phone_number }}</p>
                        <p><strong>Email:</strong> {{ $t->email }}</p>
                        <p><strong>Unit:</strong> {{ $t->ordered_unit }}</p>
                        <p><strong>Jumlah Peserta:</strong> {{ $t->jumlah_peserta ?? '-' }}</p>
                        <p><strong>Total Harga:</strong> Rp {{ number_format($t->total_harga, 0, ',', '.') }}</p>

                        <p class="mt-3 mb-1"><strong>Status :</strong>
                            @if ($t->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif ($t->status === 'waiting_payment')
                                <span class="badge bg-info">Menunggu Pembayaran</span>
                            @elseif ($t->status === 'cancelled')
                                <span class="badge bg-danger">Dibatalkan</span>
                            @elseif ($t->status === 'pending')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif ($t->status === 'approved')
                                @php
                                    $isInternal = ($t->affiliation ?? '') === 'internal_pu';
                                    $hasBilling = !empty($t->billing_qr);
                                @endphp
                                @if (!$isInternal && !$hasBilling)
                                    <span class="badge bg-warning">Disetujui tapi belum bayar</span>
                                @else
                                    <span class="badge bg-success">Disetujui</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </p>
                    </div>

                    <div class="col-12 col-md-6">
                        {{-- Payment Receipt --}}
                        <p class="mb-1"><strong>Bukti Pembayaran:</strong>
                            @if ($t->payment_receipt)
                                <a href="{{ route('secure.file', ['type' => 'payment_receipt', 'filename' => $t->payment_receipt]) }}"
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
                        <p class="mt-3 mb-1"><strong>Surat Permohonan:</strong>
                            @if ($t->request_letter)
                                <a href="{{ route('secure.file', ['type' => 'request_letter', 'filename' => $t->request_letter]) }}"
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

                        {{-- File code billing --}}
                        <p class="mt-3 mb-1"><strong>File code billing:</strong>
                            @if ($t->billing_qr)
                                <a href="{{ route('secure.file', ['type' => 'billing_qr', 'filename' => $t->billing_qr]) }}"
                                    target="_blank">Download</a>
                            @else
                                <em>Belum di upload admin</em>
                            @endif
                        </p>

                        {{-- Surat Balasan --}}
                        @if ($t->response_letter)
                            <p class="mt-3 mb-1"><strong>Surat Balasan Permohonan:</strong>
                                <a href="{{ route('secure.file', ['type' => 'response_letter', 'filename' => $t->response_letter]) }}"
                                    target="_blank">Download</a>
                            </p>
                        @endif

                        {{-- Kamar & Penghuni --}}
                        @if ($t->status == 'approved' && in_array($t->properties->type, ['paviliun', 'asrama']))
                            <p class="mt-3 mb-1"><strong>Kamar & Penghuni:</strong></p>
                            @foreach ($t->detailKamars as $dk)
                                <div class="mb-2">
                                    <div class="fw-semibold">{{ $dk->kamar->nama_kamar }}</div>
                                    @if ($dk->penghunis->isNotEmpty())
                                        @foreach ($dk->penghunis as $p)
                                            <div>- {{ $p->nama_penghuni }}</div>
                                        @endforeach
                                    @else
                                        <div class="text-muted">- (belum ada penghuni)</div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        @if ($t->status == 'rejected')
                            <p class="mt-3 mb-1"><strong>Alasan Penolakan:</strong> {{ $t->rejection_reason }}</p>
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
                    <div class="col-12 col-md-6">
                        <p><strong>Jam Mulai Acara:</strong> {{ $t->jam_start }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <p><strong>Jam Sampai Acara:</strong> {{ $t->jam_end }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <p><strong>Dipesan pada:</strong> {{ $t->created_at->format('Y-m-d') }}</p>
                    </div>


                </div>

                <hr class="mt-0">

                <div class="row g-3">
                    <div class="col-12">
                        <strong>Permintaan tambahan sesuai fasilitas:</strong>

                        <form action="{{ route('transactions.updateDescription', $t->id) }}" method="POST"
                            class="mt-2">
                            @csrf

                            <textarea name="description" class="form-control mb-3" rows="3" placeholder="Update deskripsi..."
                                style="resize: vertical;">{{ $t->description }}</textarea>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                                <!-- Tombol Update -->
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    Update
                                </button>

                                <!-- Tombol Batalkan -->
                                <button type="button" class="btn btn-danger btn-sm px-4 btn-cancel-booking"
                                    data-id="{{ $t->id }}">
                                    Batalkan Pesanan
                                </button>

                            </div>
                        </form>
                    </div>
                </div>

                <form id="cancelForm-{{ $t->id }}" action="{{ route('transactions.cancel', $t->id) }}"
                    method="POST" class="d-none">
                    @csrf
                    @method('POST')
                </form>

                @once
                    @push('scripts')
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.body.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.btn-cancel-booking');
                                    if (btn) {
                                        e.preventDefault();
                                        const id = btn.getAttribute('data-id');
                                        Swal.fire({
                                            title: 'Konfirmasi Pembatalan',
                                            text: 'Yakin ingin membatalkan pesanan ini?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonText: 'Ya, Batalkan',
                                            cancelButtonText: 'Tidak',
                                            reverseButtons: true,
                                            focusCancel: true
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                const form = document.getElementById('cancelForm-' + id);
                                                if (form) {
                                                    form.submit();
                                                }
                                            }
                                        });
                                    }
                                });
                            });
                        </script>
                    @endpush
                @endonce

                @if ($t->status == 'approved')
                    <div class="row g-3 mt-4">
                        <div class="col-12 col-md-6">
                            <p><strong>QR Survey Kepuasan:</strong></p>
                            <p class="text-muted small mb-0">atau klik barcode berikut:</p>
                            <a href="https://qr.me-qr.com/SY98Pagj" target="_blank">
                                <img src="{{ asset('storage/barcode_kepuasan/qr_kepuasan.jpg') }}"
                                    alt="QR Survey Kepuasan" class="img-fluid rounded shadow-sm"
                                    style="max-width: 150px;">
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
