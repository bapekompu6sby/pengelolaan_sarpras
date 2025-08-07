<!DOCTYPE html>

<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Register</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/boxicons.css') }}" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
  <link rel="stylesheet" href="{{ asset('/assets/vendor/css/theme-default.css') }}"
    class="template-customizer-theme-css" />
  <link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}" />

  <!-- Vendors CSS -->
  <!-- <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" /> -->

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-auth.css') }}" />
  <!-- Helpers -->
  <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>

</head>

<body>
  <!-- Content -->

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register Card -->
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <span class="app-brand-logo demo">
                  <img src="{{ asset('/assets/img/favicon/logo.png') }}" width="50px" alt="">
              </span>
              <span class="app-brand-text demo menu-text fw-bolder ms-2">Topang</span>
            </div>
            <!-- /Logo -->
            <h4 class="mb-4">Register</h4>

            <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <!-- error message -->
                @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <input type="text" class="form-control" id="username" name="name" placeholder="Enter your username"
                  autofocus />
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <!-- error message -->
                @error('email')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" />
              </div>
              <div class="mb-3 form-password-toggle">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer" id="eyeBtn"><i class="bx bx-hide"></i></span>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                  <label class="form-check-label" for="terms-conditions">
                    I agree to
                    <a href="javascript:void(0);">privacy policy & terms</a>
                  </label>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">Sign up</button>
            </form>

            <p class="text-center">
              <span>Already have an account?</span>
              <a href="{{ route('login') }}">
                <span>Sign in instead</span>
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const eyeBtn = document.getElementById('eyeBtn');
    const passField = document.getElementById('password');

    eyeBtn.addEventListener('click', () => {

      if (passField.type === 'text') {
        passField.type = 'password';
        return
      }

      passField.type = 'text';
    });

  </script>
  <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
</body>

</html>