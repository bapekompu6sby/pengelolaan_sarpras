<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('/assets/img/favicon/logo.png') }}" width="50px" alt="">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Topang</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    @php
        $menus = ['properties', 'wisma-admin', 'ruangan.detail', 'wisma.detail'];
        $route = Route::currentRouteName();
        if (Auth::check()) {
            $role = Auth::user()->role;
        }
    @endphp
    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ $route == 'dashboard' ? 'active' : '' }}" id="dashboard">
            <a href="/" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        {{-- <li class="menu-item {{ $route == 'transactions.ruangan.show' ? 'active open' : '' }}">
            <a href="{{ route('transactions.ruangan.show') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                <div data-i18n="room">Peminjaman</div>
            </a>
        </li> --}}
        {{-- tabel kegiatan --}}
        <li class="menu-item {{ $route == 'tabelKegiatan' ? 'active' : '' }}">
            <a href="{{ route('tabelKegiatan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                <div data-i18n="room">Kegiatan Hari Ini</div>
            </a>
        </li>
        <li class="menu-item {{ $route == 'calendar' ? 'active' : '' }}">
            <a href="{{ route('calendar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="room">Kalender Kegiatan</div>
            </a>
        </li>
        <li class="menu-item {{ strpos($route, 'PropertiesAsUser') !== false ? 'active open' : '' }}">
            <a href="{{ route('PropertiesAsUser') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                <div data-i18n="room">Peminjaman Ruangan</div>
            </a>
        </li>
        @auth
            @if ($role == 'user')
                <li class="menu-item {{ $route == 'transactions.historyTransaction' ? 'active open' : '' }}">
                    <a href="{{ route('transactions.historyTransaction') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-history"></i>
                        <div data-i18n="room">Riwayat Peminjaman</div>
                    </a>
                </li>
            @endif



            @if ($role == 'admin' || $role == 'pakheru' || $role == 'supervisor')
                {{-- kamar terpakai --}}
                <li class="menu-item {{ $route == 'kamarTerpakai' ? 'active' : '' }}">
                    <a href="{{ route('kamarTerpakai') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                        <div data-i18n="room">Kamar Terpakai</div>
                    </a>
                </li>
                <li class="menu-item {{ in_array($route, $menus) ? 'active open' : '' }}">
                    <a href="#" class="menu-link menu-toggle" id="data-master">
                        <i class="menu-icon tf-icons bx bx-coin-stack"></i>
                        <div data-i18n="Apps">Data Master</div>
                    </a>

                    <ul class="menu-sub">
                        @if ($role == 'admin' || $role == 'supervisor')
                            <li class="menu-item {{ $route == 'properties' ? 'active' : '' }}" id="data-ruangan">
                                <a href="{{ route('properties') }}" class="menu-link">
                                    <div data-i18n="going">Data Ruangan</div>
                                </a>
                            </li>
                            {{-- kamar --}}
                            <li class="menu-item {{ $route == 'kamar' ? 'active' : '' }}" id="data-kamar">
                                <a href="{{ route('kamar') }}" class="menu-link">
                                    <div data-i18n="going">Data Kamar</div>
                                </a>
                            </li>

                            <li class="menu-item {{ $route == 'ruangan.detail' ? 'active' : '' }}" id="data-peminjaman">
                                <a href="{{ route('ruangan.detail') }}" class="menu-link">
                                    <div data-i18n="going">Peminjaman Ruangan</div>
                                </a>
                            </li>
                        @endif
                    </ul>


                </li>
            @endif


        @endauth
        {{-- buku panduan --}}
        <li class="menu-item {{ strpos($route, 'bukuPanduan') !== false ? 'active open' : '' }}">
            <a href="{{ route('bukuPanduan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="room">Buku Panduan</div>
            </a>
        </li>
    </ul>
</aside>
