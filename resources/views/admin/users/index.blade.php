@extends('layout.admin_layout')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.navbar')
@endsection

@section('head')
    <link href="{{ asset('/assets/vendor/libs/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">{{ session('success') }}</x-toast>
    @endif
    @if (session('failed'))
        <x-toast bgColor="bg-danger" title="Failed">{{ session('failed') }}</x-toast>
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

                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-group fs-4"></i>
                            <h5 class="mb-0">Users</h5>

                            {{-- dorong tombol ke kanan --}}
                            <button type="button" class="btn p-0 me-1" data-bs-toggle="modal"
                                data-bs-target="#rolesInfoModal" aria-label="Informasi peran pengguna" title="Info peran"
                                style="line-height:1">
                                <i class="bx bx-info-circle" style="font-size:23px; color:#003A70;"></i>
                            </button>



                        </div>
                        @if (auth()->user()->role != 'supervisor')
                            <div class="d-flex align-items-center gap-2">

                                <button type="button" class="btn btn-outline-primary btn-modern" data-bs-toggle="modal"
                                    data-bs-target="#modalCreateUser">
                                    <i class="bx bx-plus-medical me-1"></i> Tambah User
                                </button>

                            </div>
                        @endif
                    </div>

                    <div class="table-responsive p-3">
                        <table id="datatable" class="table table-hover align-middle table-modern w-100">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Dibuat</th>
                                    @if (Auth::user()->role != 'supervisor')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $u)
                                    <tr>
                                        <td class="fw-semibold">
                                            <i class="bx bx-user text-brand me-2"></i>{{ $u->name }}
                                        </td>
                                        <td>{{ $u->email }}</td>
                                        <td><span class="badge badge-soft">{{ $u->role ?? '-' }}</span></td>
                                        <td>{{ optional($u->created_at)->format('Y M d') ?? '-' }}</td>
                                        @if (Auth::user()->role != 'supervisor')
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button class="btn btn-outline-primary btn-sm btn-modern"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalDetail{{ $u->id }}">
                                                        <i class="bx bx-show"></i> Detail
                                                    </button>
                                                    <button class="btn btn-primary btn-sm btn-modern" data-bs-toggle="modal"
                                                        data-bs-target="#modalEdit{{ $u->id }}">
                                                        <i class="bx bx-edit-alt"></i> Edit
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

            </div>
        </div>
    </div>
    @push('modals')
        @include('admin.users.modal')
        @foreach ($users as $user)
            @include('admin.users.modal', ['user' => $user])
        @endforeach
    @endpush

@endsection
@push('scripts')
    <script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dt = new DataTable('#datatable', {
                pageLength: 10,
                lengthChange: false,
                ordering: true
            });

            // quick search ke DataTables
            const qs = document.getElementById('quickSearch');
            if (qs) qs.addEventListener('input', (e) => dt.search(e.target.value).draw());

            // anti double-submit untuk create
            const createModal = document.getElementById('modalCreateUser');
            const btn = document.getElementById('btnCreateSubmit');
            const form = createModal?.querySelector('form');
            if (form && btn) {
                form.addEventListener('submit', () => {
                    btn.disabled = true;
                    btn.innerText = 'Menyimpan...';
                });
                // reset saat modal ditutup
                createModal.addEventListener('hidden.bs.modal', () => {
                    form.reset();
                    createModal.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                        'is-invalid'));
                    btn.disabled = false;
                    btn.innerText = 'Simpan';
                });
            }
        });
    </script>
@endpush
