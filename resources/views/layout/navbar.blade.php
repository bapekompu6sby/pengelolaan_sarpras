<nav id="layout-navbar"
    class="navbar navbar-expand-xl align-items-center bg-white bg-menu-theme border-bottom border-1 border-secondary-subtle sticky-top p-0">
    <div class="container-fluid px-2 py-3">

        {{-- Toggle menu (mobile) --}}
        <button class="btn ms-2 p-0 border-0 d-xl-none me-2 layout-menu-toggle" type="button" aria-label="Menu">
            <i class="bx bx-menu bx-sm"></i>
        </button>

        {{-- Halo user di kiri (sembunyikan di layar kecil) --}}
        @if (Auth::check())
            <div class="navbar-text ms-2 fw-semibold text-truncate d-none d-lg-block">
                {{ Auth::user()->name }}
            </div>
        @endif

        <div class="ms-auto d-flex align-items-center pe-2 pe-sm-3">
            @if (Auth::check())
                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 px-3 py-0" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu pengguna">
                        {{-- Avatar LEBIH BESAR --}}
                        <img src="{{ asset('assets/img/avatars/avatar.jpg') }}" alt="avatar" class="rounded-circle"
                            width="40" height="40">
                        {{-- Nama tetap truncate biar rapi di mobile --}}
                        <span class="fw-medium text-truncate" style="max-width:140px"
                            title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                        <i class="bx bx-chevron-down fs-5"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow p-2">
                        <li class="px-2 py-2">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('assets/img/avatars/avatar.jpg') }}" alt="avatar"
                                    class="rounded-circle" width="40" height="40">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate" style="max-width:220px">
                                        {{ Auth::user()->name }}</div>
                                    <div class="small text-muted text-truncate" style="max-width:220px">
                                        {{ Auth::user()->email ?? '' }}</div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center px-2 py-2"
                                href="{{ route('dashboard') }}">
                                <i class="bx bx-grid-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center px-2 py-2" href="{{ route('profile') }}">
                                <i class="bx bx-user me-2"></i> Profil
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger px-2 py-2" href="#logout"
                                data-bs-toggle="modal" data-bs-target="#logout">
                                <i class="bx bx-log-out me-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            @else
                
                <a href="{{ route('login') }}" class="btn btn-primary px-3 py-2  fw-semibold">Login</a>
            @endif
        </div>


    </div>
</nav>

{{-- Modal Konfirmasi Logout (punyamu) --}}
<div class="modal fade" id="logout" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah anda yakin ingin keluar dari akun ini?</p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>
