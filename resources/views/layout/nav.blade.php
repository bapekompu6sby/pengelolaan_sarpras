<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User -->
      @if (Auth::check())
        <li class="nav-item navbar-dropdown dropdown-user dropdown" id="user-profile">
          <a href="{{ route('profile') }}">
            <div class="avatar avatar-online">
              <img src="{{ asset('assets/img/avatars/avatar.jpg') }}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
          </a>
        </li>
        <li class="nav-item navbar-dropdown dropdown-user dropdown" id="btn-logout">
          <a class="nav-link" href="#logout" data-bs-toggle="modal" data-bs-target="#logout">
            <i class="bx bx-sm bx-power-off me-2"></i>
          </a>
        </li>
      @else
        <li>
          <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        </li>
      @endif
      <!--/ User -->
    </ul>
  </div>
</nav>

<!-- Confirm modal -->
<div class="modal fade" id="logout" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah anda yakin ingin keluar dari akun ini?</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-danger">Confirm</button>
        </form>
      </div>
    </div>
  </div>
</div>