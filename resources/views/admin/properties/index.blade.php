@extends('layout.admin_layout')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/datatables/datatables.min.css') }}" rel="stylesheet">
    <style>
        .toggle-active {
            gap: .25rem;
        }

        .toggle-active .track {
            width: 52px;
            height: 28px;
            border-radius: 999px;
            background: #e9ecef;
            display: inline-block;
            position: relative;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, .06);
            cursor: pointer;
            transition: background .2s ease;
        }

        .toggle-active .thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
            transition: left .2s ease;
        }

        /* icon default: X merah (nonaktif) */
        .toggle-active .thumb .bi {
            font-size: 14px;
            color: #dc3545;
        }

        .toggle-active .thumb .bi::before {
            content: "\f623";
            /* bootstrap-icons: x-lg */
            font-family: "bootstrap-icons";
        }

        /* checked: hijau & centang */
        .toggle-active input:checked+.track {
            background: #d1e7dd;
        }

        .toggle-active input:checked+.track .thumb {
            left: 27px;
        }

        .toggle-active input:checked+.track .thumb .bi {
            color: #198754;
        }

        .toggle-active input:checked+.track .thumb .bi::before {
            content: "\f26e";
            /* bootstrap-icons: check-lg */
        }

        /* teks ikut berubah tanpa JS */
        .toggle-active .status-text .on {
            display: none;
        }

        .toggle-active input:checked~.status-text .on {
            display: inline;
        }

        .toggle-active input:checked~.status-text .off {
            display: none;
        }
    </style>
@endsection


@section('content')

    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">
            {{ session('success') }}
        </x-toast>
    @endif

    @if (session('failed'))
        <x-toast bgColor="bg-danger" title="Failed">
            {{ session('failed') }}
        </x-toast>
    @endif

    <div class="container-fluid flex-grow-1 p-0">
        <div class="row g-0">
            <div class="col-12 px-3 py-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h5 class="text-danger">Masukkan data dengan benar</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-modern my-0">
                    {{-- Header: sama seperti Users (clean, border accent) --}}
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-buildings fs-4"></i>
                            <h5 class="m-0">Sarana &amp; Prasarana</h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">

                            <button type="button" class="btn btn-outline-primary btn-modern" data-bs-toggle="modal"
                                data-bs-target="#modalCreate">
                                <i class="bx bx-plus-medical me-1"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive p-3">
                        <table id="datatable" class="table table-hover align-middle table-modern w-100">
                            <thead>
                                <tr>
                                    <th>Properti</th>
                                    <th>Img</th>
                                    <th>Jenis</th>
                                    <th>Kapasitas</th>
                                    <th>aktif</th>
                                    @if (Auth::user()->role != 'supervisor')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($properties as $property)
                                    <tr>
                                        <td class="fw-semibold">
                                            <i class="bx bx-home-heart text-brand me-2"></i>{{ $property->name }}
                                        </td>

                                        <td>
                                            @if (!empty($property->image_path))
                                                <img src="{{ asset('uploads/' . $property->image_path) }}"
                                                    alt="{{ $property->name }}" width="48" height="48"
                                                    style="object-fit:cover;border-radius:8px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>

                                        <td><span class="badge badge-soft">{{ ucfirst($property->type) }}</span></td>
                                        <td>{{ $property->capacity }}</td>
                                        <td>
                                            <div class="toggle-active d-inline-flex align-items-center">
                                                {{-- state --}}
                                                <input type="checkbox" id="active-{{ $property->id }}"
                                                    class="js-toggle-active" data-id="{{ $property->id }}"
                                                    data-url="{{ route('properties.status', $property->id) }}"
                                                    {{-- ← pakai named route --}}
                                                    {{ $property->status === 'ative' ? 'checked' : '' }} hidden>


                                                {{-- switch --}}
                                                <label for="active-{{ $property->id }}" class="track mb-0"
                                                    aria-label="Ubah status aktif">
                                                    <span class="thumb"><i class="bi"></i></span>
                                                </label>

                                                {{-- teks --}}
                                                <span class="status-text ms-2 fw-semibold">
                                                    <span class="on text-success">Aktif</span>
                                                    <span class="off text-muted">Nonaktif</span>
                                                </span>
                                            </div>
                                        </td>



                                        @if (Auth::user()->role != 'supervisor')
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-primary btn-sm btn-modern"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalDetail{{ $property->id }}">
                                                        <i class="bx bx-show"></i> Detail
                                                    </button>
                                                    <button class="btn btn-primary btn-sm btn-modern" data-bs-toggle="modal"
                                                        data-bs-target="#modalEdit{{ $property->id }}">
                                                        <i class="bx bx-edit-alt"></i> Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-modern" data-bs-toggle="modal"
                                                        data-bs-target="#modalDelete{{ $property->id }}">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Stack modals di akhir body --}}
                @push('modals')
                    {{-- Create (1x) --}}
                    @include('admin.properties.modal')

                    {{-- Per item --}}
                    @foreach ($properties as $property)
                        @include('admin.properties.modal', ['property' => $property])
                    @endforeach
                @endpush
            </div>
        </div>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            document.querySelectorAll('.js-toggle-active').forEach(cb => {
                cb.addEventListener('change', async () => {
                    const url = cb.dataset.url; // ← ambil dari data-url
                    const payload = {
                        is_active: cb.checked ? 1 : 0
                    };

                    try {
                        const res = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload),
                            credentials: 'same-origin' // ikutkan cookie sesi
                        });

                        if (!res.ok) {
                            const text = await res.text(); // log isi errornya
                            console.error('Toggle failed:', res.status, res.statusText, text);
                            throw new Error(`HTTP ${res.status}`);
                        }

                        const json = await res.json();
                        alert('Status berhasil diubah: ' + json.status);
                        // console.log('Sukses:', json);
                    } catch (e) {
                        cb.checked = !cb.checked; // revert UI
                        alert('Gagal mengubah status. Cek console untuk detail.');
                    }
                });
            });
        });
    </script>



    <!-- / Content -->




@endsection
