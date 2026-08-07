{{-- =========================
  Modal: Tambah User
  - Konsisten pakai modal-modern, form-floating, btn-modern
  - Validasi server-side pakai @error
  ========================== --}}
<div class="modal fade" id="modalCreateUser" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"
    aria-labelledby="modalCreateUserLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-modern">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalCreateUserLabel">Tambah User</h5>
                    <small class="text-muted">Pastikan data valid dan lengkap</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <form action="{{ route('users.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Nama --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="create_name" name="name" placeholder="Nama" value="{{ old('name') }}"
                                    required>
                                <label for="create_name">Nama</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="create_email" name="email" placeholder="nama@email.com"
                                    value="{{ old('email') }}" required>
                                <label for="create_email">Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('role') is-invalid @enderror" id="create_role"
                                    name="role" required>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                    <option value="user" @selected(old('role') === 'user')>User</option>
                                    <option value="supervisor" @selected(old('role') === 'supervisor')>Supervisor</option>
                                </select>
                                <label for="create_role">Role</label>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="create_password" name="password" placeholder="********" minlength="8"
                                    autocomplete="new-password" required>
                                <label for="create_password">Password (min. 8)</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="create_password_confirmation"
                                    name="password_confirmation" placeholder="********" autocomplete="new-password"
                                    required>
                                <label for="create_password_confirmation">Konfirmasi Password</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern" id="btnCreateSubmit">
                        <i class="bx bx-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================
  Modals per user: Edit & Detail
  - Id unik per user
  - Konsisten form-floating + btn-modern
  ========================== --}}
@php $roles = ['admin','user','supervisor']; @endphp
@foreach ($users as $user)
    {{-- Edit User --}}
    <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1" aria-hidden="true"
        aria-labelledby="modalEditLabel{{ $user->id }}">
        <div class="modal-dialog modal-dialog-centered modal-modern">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel{{ $user->id }}">Edit User: {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT') {{-- gunakan PUT/PATCH untuk update --}}
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Nama --}}
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit_name_{{ $user->id }}"
                                        name="name" placeholder="Nama" value="{{ old('name', $user->name) }}"
                                        required>
                                    <label for="edit_name_{{ $user->id }}">Nama</label>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="edit_email_{{ $user->id }}"
                                        name="email" placeholder="nama@email.com"
                                        value="{{ old('email', $user->email) }}" required>
                                    <label for="edit_email_{{ $user->id }}">Email</label>
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="edit_role_{{ $user->id }}" name="role"
                                        required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected($user->role === $role)>
                                                {{ ucfirst($role) }}</option>
                                        @endforeach
                                    </select>
                                    <label for="edit_role_{{ $user->id }}">Role</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-modern"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-modern">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Detail User --}}
    <div class="modal fade" id="modalDetail{{ $user->id }}" tabindex="-1" aria-hidden="true"
        aria-labelledby="modalDetailLabel{{ $user->id }}">
        <div class="modal-dialog modal-dialog-centered modal-modern">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel{{ $user->id }}">Detail User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <strong>Nama:</strong><br>
                            <span class="text-break">{{ $user->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Email:</strong><br>
                            <span class="text-break">{{ $user->email }}</span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Role:</strong><br>
                            <span class="badge bg-brand">{{ ucfirst($user->role ?? '-') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Dibuat:</strong><br>
                            {{ optional($user->created_at)->format('d M Y') ?: '-' }}
                        </div>
                        <div class="col-sm-6">
                            <strong>Diperbarui:</strong><br>
                            {{ optional($user->updated_at)->format('d M Y') ?: '-' }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-modern"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
{{-- Modal: Informasi Peran --}}
<div class="modal fade" id="rolesInfoModal" tabindex="-1" aria-labelledby="rolesInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-3">
            <div class="modal-header">
                <h5 class="modal-title" id="rolesInfoLabel">
                    Informasi Peran Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">

                    {{-- Admin --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-dark rounded-pill">Admin</span>
                            <span class="text-muted small">Hak akses penuh</span>
                        </div>
                        <ul class="mb-0">
                            <li>Kelola master data (user, ruangan, kamar, peminjama).</li>
                            <li>Kelola akun & peran pengguna.</li>
                            <li>Lihat & ubah semua transaksi peminjaman.</li>
                            <li>Ekspor laporan Peminjaman.</li>
                        </ul>
                    </div>
                    <hr class="my-2">

                    {{-- Supervisor --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-pill">Supervisor</span>
                            <span class="text-muted small">Persetujuan & laporan</span>
                        </div>
                        <ul class="mb-0">
                            <li>Review jadwal peminjaman.</li>
                            <li>Review pengajuan peminjaman, pengguna, properties & kamar.</li>
                            <li>Cetak/ekspor laporan peminjaman.</li>
                        </ul>
                    </div>


                    <hr class="my-2">

                    {{-- User --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success rounded-pill">User</span>
                            <span class="text-muted small">Pengajuan & pelaporan</span>
                        </div>
                        <ul class="mb-0">
                            <li>Mengajukan peminjaman: pilih ruangan, isi <em>instansi/kegiatan</em>, tanggal
                                mulai–akhir.</li>
                            <li>melihat status pengajuan.</li>
                            <li>Melengkapi pembayaran (jika berlaku) & unggah bukti bayar.</li>
                            <li>Menerima surat balasan.</li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
