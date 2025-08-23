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
                            <p class="mt-3 mb-1"><strong>Surat Permohonan:</strong>
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
                        </div>
                        <div class="col-md-6">
                            <p><strong>Description:</strong> {{ $t->description }}</p>
                            <p><strong>Unit:</strong> {{ $t->ordered_unit }}</p>
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
                            <button class="btn btn-success dropdown-toggle" type="button" id="statusDropdown"
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
                                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="waiting_payment"
                                        class="dropdown-item text-success">
                                        Menunggu Pembayaran
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="rejected"
                                        class="dropdown-item text-danger">
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

    {{-- form edit for peminjaman ruangan --}}
    <div class="modal fade" id="modalCenter{{ $t->id }}" tabindex="-1" data-bs-backdrop="static"
        role="dialog" aria-labelledby="editEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('transactions.ruangan.update', $t->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEventLabel">Edit Transaksi - {{ $t->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="office" class="form-label">Instansi</label>
                                    <input type="text" class="form-control" id="office" name="office"
                                        required value="{{ $t->instansi }}">
                                </div>
                                <div class="mb-3">
                                    <label for="affiliation" class="form-label">Affiliation</label>
                                    <input type="text" class="form-control" id="affiliation" name="affiliation"
                                        value="{{ $t->affiliation }} " readonly>
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
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event" class="form-label">Kegiatan</label>
                                    <input type="text" class="form-control" id="event" name="event"
                                        required value="{{ $t->kegiatan }}">
                                </div>
                                <div class="mb-3">
                                    <label for="ordered_unit" class="form-label">Unit</label>
                                    <input type="number" class="form-control" id="ordered_unit" name="ordered_unit"
                                        value="{{ $t->ordered_unit }}" readonly>

                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ $t->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ruangan</label>
                                    <input type="text" class="form-control" id="property_id"
                                        value="{{ $t->properties->name }}" readonly>
                                </div>

                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start" class="form-label">Mulai</label>
                                <input type="date" class="form-control" id="start" name="start" readonly
                                    value="{{ $t->start }}">
                            </div>
                            <div class="col-md-6">
                                <label for="end" class="form-label">Selesai</label>
                                <input type="date" class="form-control" id="end" name="end" readonly
                                    value="{{ $t->end }}">
                            </div>
                        </div>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
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

                            <div class="col-md-6">
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

                        <hr>

                        {{-- Status Dropdown --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option class="text-warning" value="pending"
                                    {{ $t->status == 'pending' ? 'selected' : '' }}>
                                    Menunggu
                                </option>
                                <option class="text-success" value="approved"
                                    {{ $t->status == 'approved' ? 'selected' : '' }}>
                                    Disetujui
                                </option>
                                <option class="text-success" value="waiting_payment"
                                    {{ $t->status == 'waiting_payment' ? 'selected' : '' }}>
                                    Menunggu Pembayaran
                                </option>
                                <option class="text-danger" value="rejected"
                                    {{ $t->status == 'rejected' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
