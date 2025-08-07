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
  if(Auth::check()) {
    $role = Auth::user()->role;
  }
  @endphp
  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ ($route == 'dashboard') ? 'active' : '' }}" id="dashboard">
      <a href="/" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>

@auth

  @if ($role == 'admin' || $role == 'pakheru')
  <li class="menu-item {{ (in_array($route, $menus))? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle" id="data-master">
      <i class="menu-icon tf-icons bx bx-coin-stack"></i>
      <div data-i18n="Apps">Data Master</div>
    </a>

    <ul class="menu-sub">
      @if($role == 'admin')
      <li class="menu-item {{ ($route == 'properties') ? 'active' : '' }}" id="data-ruangan">
        <a href="{{ route('properties') }}" class="menu-link">
          <div data-i18n="going">Data Ruangan</div>
        </a>
      </li>
      @endif

      <li class="menu-item {{ ($route == 'ruangan.detail') ? 'active' : '' }}" id="data-peminjaman">
        <a href="{{ route('ruangan.detail') }}" class="menu-link">
          <div data-i18n="going">Peminjaman Ruangan</div>
        </a>
      </li>
      <li class="menu-item {{ ($route == 'wisma-admin' || $route == 'wisma.detail') ? 'active' :  '' }}" id="data-asrama">
        <a href="{{ ($role == 'admin') ? route('wisma-admin') : route('wisma.detail') }}" class="menu-link">
          <div data-i18n="idle">Asrama & Paviliun</div>
        </a>
      </li>
    </ul>
  </li>
  @if($role == 'admin')
  <li class="menu-item {{ (strpos($route, 'transactions') !== false) ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle" id="transaction">
      <i class="menu-icon tf-icons bx bx-grid-alt"></i>
      <div data-i18n="Apps">Peminjaman</div>
    </a>

    <ul class="menu-sub">
      <li class="menu-item {{ ($route == 'transactions.ruangan.show') ? 'active' : '' }}" id="trans-ruangan">
        <a href="{{ route('transactions.ruangan.show') }}" class="menu-link">
          <div data-i18n="going">Ruangan</div>
        </a>
      </li>
      <li class="menu-item {{ ($route == 'transactions.wisma.show') ? 'active' : '' }}" id="trans-asrama">
        <a href="{{ route('transactions.wisma.show') }}" class="menu-link">
          <div data-i18n="idle">Asrama & Paviliun</div>
        </a>
      </li>
    </ul>
  </li>
  @endif

  @endif

  @if($role == 'user' || $role == 'pakheru')
  <li class="menu-item {{ ($route == 'wisma_show_user') ? 'active' : '' }}">
    <a href="{{ route('wisma_show_user') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-building-house"></i>
      <div data-i18n="Apps">Asrama & Paviliun</div>
    </a>
  </li>
  @endif
  
@endauth
    @if(!Auth::check())
      <li class="menu-item {{ ($route == 'asrama') ? 'active' : ''}}">
        <a href="{{ route('asrama') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-building-house"></i>
          <div data-i18n="room">Asrama</div>
        </a>
      </li>
    @endif

    <li class="menu-item {{ ($route == 'calendar') ? 'active' : ''}}">
      <a href="{{ route('calendar') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calendar"></i>
        <div data-i18n="room">Kalender Kegiatan</div>
      </a>
    </li>
  </ul>
</aside>