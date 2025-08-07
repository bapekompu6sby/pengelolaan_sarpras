@extends('layout.index')
@section('sidebar')
@include('layout.sidebar')
@endsection
@section('nav')
@include('layout.nav')
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

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">Profile settings</h4>
  <div class="row">
    <div class="col-lg-12 mb-3 order-0">
      <div class="card mb-4">
        <!-- Account -->
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-4">
            <img src="{{ asset('assets/img/avatars/avatar.jpg') }}" alt="user-avatar" class="d-block rounded"
              height="100" width="100" id="uploadedAvatar" />
            <div class="button-wrapper">
              <h4 class="text-muted-mb-3">{{ ucfirst(Auth::user()->name) }}</h4>
              <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
            </div>
          </div>
        </div>
        <hr class="my-0" />
        <div class="card-body">
          <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
            @method('PATCH')
            @csrf
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="username" class="form-label">Username</label>
                <input class="form-control" type="text" id="username" name="name" value="{{ Auth::user()->name }}"
                  autofocus required />
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">Email</label>
                <input class="form-control" type="text" id="email" name="email" value="{{ Auth::user()->email }}"
                  placeholder="ur.email@example.com" required />
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Save</button>
            </div>
          </form>
        </div>
      </div>
      <div class="card mb-4">
        <!-- Account -->
        <div class="card-body">
          <h4 class="mb-0">Change Password</h4>
        </div>
        <hr class="my-0" />
        <div class="card-body">
          <form id="formAccountSettings" method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="oldPassword" class="form-label">Old Password</label>
                <div class="d-flex">
                  <input class="form-control me-2" type="password" id="oldPassword" name="current_password" value=""
                    autofocus />
                  <span class="input-group-text cursor-pointer eyeBtn"><i class="bx bx-hide"></i></span>
                </div>
                <!-- getting errors from updatedPassword group error message -->
                @error('current_password', 'updatePassword')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="newPassword" class="form-label">New Password</label>
                <div class="d-flex">
                  <input class="form-control me-2" type="password" id="newPassword" name="password" value="" />
                  <span class="input-group-text cursor-pointer eyeBtn"><i class="bx bx-hide"></i></span>
                </div>
                @error('password', 'updatePassword')
                <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Save</button>
            </div>
          </form>
        </div>
      </div>
      <div class="card mb-4">
        <!-- Account -->
        <div class="card-body">
          <h4 class="mb-0">Reset Password</h4>
        </div>
        <hr class="my-0" />
        <div class="card-body">
          <form id="formAccountSettings" method="POST" action="{{ route('password.store') }}">
            @csrf
            @method('POST')
            <div class="row">
              <div class="col-md-5">
                <p>Ketika anda melakukan reset password maka password akan diset pada password default
                  yaitu <strong>user</strong>. Mohon untuk segera melakukan perubahan password setelah melakukan reset
                  password untuk menjaga keamanan data anda.</p>
                </p>
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-danger me-2">Reset Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- / Content -->

@endsection

@section('script')
<script>

  // hide and show password by clicking eye icon
  const eyeBtn = document.querySelectorAll('.eyeBtn');
  const passField = document.querySelectorAll('.form-control[type="password"]');

  eyeBtn.forEach((btn, index) => {
    btn.addEventListener('click', () => {
      if (passField[index].type === 'text') {
        passField[index].type = 'password';
        return
      }
      passField[index].type = 'text';
    });
  });

</script>
@endsection