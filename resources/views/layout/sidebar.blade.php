<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme border-end border-1 border-secondary-subtle">

    {{-- Brand --}}
    <div class="app-brand demo px-3 py-2 mb-3">
        <a href="{{ route('dashboard') }}" class="app-brand-link d-flex align-items-center text-decoration-none ps-3">
            <span class="app-brand-logo demo d-inline-flex align-items-center justify-content-center">
                <img src="{{ asset('/assets/img/favicon/logo.png') }}" width="44" height="44" alt="Logo PUPR"
                    class="img-fluid">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2 text-truncate"
                style="max-width:120px;">Topang</span>
        </a>


        {{-- Collapse toggle (mobile) --}}
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"
            aria-label="Toggle menu">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $route = Route::currentRouteName();
        $role = Auth::check() ? Auth::user()->role : null;
        // Helper: function active untuk 1/lebih pola
        function isActive($patterns)
        {
            foreach ((array) $patterns as $p) {
                if (request()->routeIs($p)) {
                    return true;
                }
            }
            return false;
        }
    @endphp

    <ul class="menu-inner py-1">

        {{-- Dashboard --}}
        @php $isDash = isActive('dashboard'); @endphp
        <li class="menu-item {{ $isDash ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link" {{ $isDash ? 'aria-current=page' : '' }}>
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        {{-- Kegiatan Hari Ini --}}
        @php $isTbl = isActive('tabelKegiatan'); @endphp
        <li class="menu-item {{ $isTbl ? 'active' : '' }}">
            <a href="{{ route('tabelKegiatan') }}" class="menu-link" {{ $isTbl ? 'aria-current=page' : '' }}>
                <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                <div>Kegiatan Hari Ini</div>
            </a>
        </li>

        {{-- Kalender Kegiatan --}}
        @php $isCal = isActive('calendar'); @endphp
        <li class="menu-item {{ $isCal ? 'active' : '' }}">
            <a href="{{ route('calendar') }}" class="menu-link" {{ $isCal ? 'aria-current=page' : '' }}>
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div>Kalender Kegiatan</div>
            </a>
        </li>

        {{-- Peminjaman Ruangan (User view) --}}
        @php $isBookings = isActive('bookings*'); @endphp
        <li class="menu-item {{ $isBookings ? 'active' : '' }}">
            <a href="{{ route('bookings') }}" class="menu-link" {{ $isBookings ? 'aria-current=page' : '' }}>
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                <div>Peminjaman Ruangan</div>
            </a>
        </li>

        @auth
            {{-- Riwayat (khusus user) --}}
            @if ($role === 'user')
                @php $isHistory = isActive('transactions.historyTransaction'); @endphp
                <li class="menu-item {{ $isHistory ? 'active' : '' }}">
                    <a href="{{ route('transactions.historyTransaction') }}" class="menu-link"
                        {{ $isHistory ? 'aria-current=page' : '' }}>
                        <i class="menu-icon tf-icons bx bx-history"></i>
                        <div>Riwayat Peminjaman</div>
                    </a>
                </li>
            @endif

            {{-- Admin & Supervisor --}}
            @if (in_array($role, ['admin', 'supervisor']))
                {{-- Kamar Terpakai --}}
                @php $isPenghunis = isActive('penghunis'); @endphp
                <li class="menu-item {{ $isPenghunis ? 'active' : '' }}">
                    <a href="{{ route('penghunis') }}" class="menu-link" {{ $isPenghunis ? 'aria-current=page' : '' }}>
                        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                        <div>Kamar Terpakai</div>
                    </a>
                </li>

                {{-- Data Master (submenu) --}}
                @php
                    $isMasterOpen = isActive(['users', 'properties', 'kamar', 'transactions']);
                @endphp
                <li class="menu-item {{ $isMasterOpen ? 'active open' : '' }}">
                    <a href="#" class="menu-link menu-toggle" id="data-master"
                        aria-expanded="{{ $isMasterOpen ? 'true' : 'false' }}">
                        <i class="menu-icon tf-icons bx bx-coin-stack"></i>
                        <div>Data Master</div>
                    </a>

                    <ul class="menu-sub">
                        {{-- Data User --}}
                        @php $isUsers = isActive('users'); @endphp
                        <li class="menu-item {{ $isUsers ? 'active' : '' }}">
                            <a href="{{ route('users') }}" class="menu-link" {{ $isUsers ? 'aria-current=page' : '' }}>
                                <div>Data User</div>
                            </a>
                        </li>

                        {{-- Data Ruangan --}}
                        @php $isProps = isActive('properties'); @endphp
                        <li class="menu-item {{ $isProps ? 'active' : '' }}">
                            <a href="{{ route('properties') }}" class="menu-link"
                                {{ $isProps ? 'aria-current=page' : '' }}>
                                <div>Data Ruangan</div>
                            </a>
                        </li>

                        {{-- Data Kamar --}}
                        @php $isKamar = isActive('kamar'); @endphp
                        <li class="menu-item {{ $isKamar ? 'active' : '' }}">
                            <a href="{{ route('kamar') }}" class="menu-link" {{ $isKamar ? 'aria-current=page' : '' }}>
                                <div>Data Kamar</div>
                            </a>
                        </li>

                        {{-- Peminjaman Ruangan (Admin table) --}}
                        @php $isTx = isActive('transactions'); @endphp
                        <li class="menu-item {{ $isTx ? 'active' : '' }}">
                            <a href="{{ route('transactions') }}" class="menu-link"
                                {{ $isTx ? 'aria-current=page' : '' }}>
                                <div>Peminjaman Ruangan</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endauth

        {{-- Buku Panduan --}}
        @php $isGuide = isActive('bukuPanduan'); @endphp
        <li class="menu-item {{ $isGuide ? 'active' : '' }}">
            <a href="{{ route('bukuPanduan') }}" class="menu-link" {{ $isGuide ? 'aria-current=page' : '' }}>
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div>Buku Panduan</div>
            </a>
        </li>

    </ul>
</aside>
