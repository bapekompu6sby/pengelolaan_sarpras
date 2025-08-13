@if (isset($property))
    <div class="modal fade" id="modalCenter{{ $property->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
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
                            <label for="area" class="form-label">Luas Area</label>
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
    </div>
@elseif(isset($t))
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
                            <p><strong>Payment Receipt:</strong>
                                @if ($t->payment_receipt)
                                    <a href="{{ asset('storage/uploads/payment_receipt/' . $t->payment_receipt) }}"
                                        target="_blank">Download</a>
                                @else
                                    <em>Tidak ada</em>
                                @endif
                            </p>
                            <p><strong>Request Letter:</strong>
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
                                menunggu
                            </button>
                        @elseif ($t->status == 'approved')
                            <button class="btn btn-success dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                di terima
                            </button>
                        @elseif ($t->status == 'rejected')
                            <button class="btn btn-danger dropdown-toggle" type="button" id="statusDropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                di tolak
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
                                        di terima
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('transactions.updateStatus', $t->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="rejected"
                                        class="dropdown-item text-danger">
                                        di tolak
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
        role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('transactions.ruangan.update', $t->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventLabel">Edit Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="office" class="form-label">Instansi</label>
                            <input type="text" class="form-control" id="office" name="office" required
                                value="{{ $t->instansi }}">
                        </div>
                        <div class="mb-3">
                            <label for="event" class="form-label">Kegiatan</label>
                            <input type="text" class="form-control" id="event" name="event" required
                                value="{{ $t->kegiatan }}">
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="start" class="form-label">Mulai</label>
                                <input type="date" class="form-control" id="start" name="start" disabled
                                    value="{{ $t->start }}">
                            </div>
                            <div class="col mb-3">
                                <label for="end" class="form-label">Selesai</label>
                                <input type="date" class="form-control" id="end" name="end" disabled
                                    value="{{ $t->end }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="venue" class="form-label">Ruangan</label>
                            <select class="form-select" id="venue" name="venue" required>
                                <option selected disabled>Pilih Ruangan</option>
                                @foreach ($ruangan as $property)
                                    <option value="{{ $property->id }}"
                                        {{ $property->id == $t->property_id ? 'selected' : '' }}>
                                        {{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $t->description }}</textarea>
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
                                aria-describedby="basic-icon-default-fullname2" disabled
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
