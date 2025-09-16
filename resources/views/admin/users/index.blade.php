@extends('layout.index')

@section('sidebar')
    @include('layout.sidebar')
@endsection

@section('nav')
    @include('layout.nav')
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

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Data Master /</span> Users</h4> --}}

        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
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

                <div class="card">
                    <div class="d-flex justify-content-between">
                        <div class="head">
                            <h5 class="card-header">Users</h5>
                        </div>
                        <div class="my-auto pe-4">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalCreateUser">
                                <span class="tf-icons bx bx-plus-medical bx-sm"></span>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive text-nowrap p-4">
                        <table id="datatable" class="display table table-hover" style="width:100%">
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

                            <tbody class="table-border-bottom-0">
                                @foreach ($users as $u)
                                    <tr>
                                        <td><i class="bx bx-user text-info me-2"></i><strong>{{ $u->name }}</strong>
                                        </td>
                                        <td>{{ $u->email }}</td>
                                        <td><span class="badge bg-label-primary">{{ $u->role ?? '-' }}</span></td>

                                        <td>{{ $u->created_at ? $u->created_at->format('Y M d ') : '-' }}</td>

                                        @if (Auth::user()->role != 'supervisor')
                                            <td>
                                                <button type="button" class="btn btn-warning btn-sm me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEdit{{ $u->id }}">Edit</button>
                                                <button type="button" class="btn btn-info btn-sm me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDetail{{ $u->id }}">Detail</button>
                                                {{-- <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalDelete{{ $u->id }}">Delete</button> --}}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- Modals per user --}}
                            @foreach ($users as $u)
                                {{-- Edit Modal --}}
                                <div class="modal fade" id="modalEdit{{ $u->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit User: {{ $u->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('users.update', $u->id) }}" method="POST">
                                                @csrf @method('POST')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama</label>
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ old('name', $u->name) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control" name="email"
                                                            value="{{ old('email', $u->email) }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Role</label>
                                                        <select class="form-select" name="role" required>
                                                            @php $roles = ['admin','user','supervisor']; @endphp
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role }}"
                                                                    {{ $u->role === $role ? 'selected' : '' }}>
                                                                    {{ ucfirst($role) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Detail Modal --}}
                                <div class="modal fade" id="modalDetail{{ $u->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-6"><strong>Nama:</strong><br>{{ $u->name }}
                                                    </div>
                                                    <div class="col-6"><strong>Email:</strong><br>{{ $u->email }}
                                                    </div>
                                                    <div class="col-6"><strong>Role:</strong><br><span
                                                            class="badge bg-primary">{{ $u->role ?? '-' }}</span></div>

                                                    <div class="col-6">
                                                        <strong>Dibuat:</strong><br>{{ $u->created_at ? $u->created_at->format('d M Y ') : '-' }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Diperbarui:</strong><br>{{ $u->updated_at ? $u->updated_at->format('d M Y ') : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete Modal --}}
                                {{-- <div class="modal fade" id="modalDelete{{ $u->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Yakin ingin menghapus user <strong>{{ $u->name }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('users.destroy', $u->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            @endforeach

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    <div class="modal fade" id="modalCreateUser" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                                <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter.</small>
                        </div>

                        {{-- WAJIB untuk rule "confirmed" --}}
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required
                                autocomplete="new-password">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dt = new DataTable('#datatable');
        });
    </script>
@endpush
