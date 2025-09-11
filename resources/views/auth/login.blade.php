<!DOCTYPE html>

<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login</title>

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

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('/assets/img/favicon/logo.png') }}" width="50px" alt="">
                            </span>
                            <span class="app-brand-text demo menu-text fw-bolder ms-2">Topang</span>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <form id="formAuthentication" class="mb-3" action="{{ route('login-store') }}"
                            method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="login" class="form-label">Email atau Username</label>
                                <input type="text" class="form-control" id="login" name="login"
                                    value="{{ old('login') }}" placeholder="Email atau username" autofocus />
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
                                        class="small">Lupa Password?</a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="••••••••••••" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer" id="eyeBtn"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>


                        <div class="mb-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary d-grid w-100"
                                type="submit">Lanjutkan sebagai tamu</a>
                        </div>

                        <p class="text-center">
                            <span>Belum punya akun topang?</span>
                            <a href="{{ route('register') }}">
                                <span>Klik disini</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordLabel"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordLabel">Lupa Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            Silakan masukkan email yang terdaftar.<br>
                            Kami akan mengirimkan tautan untuk mengatur ulang password Anda.
                        </p>

                        <div class="mb-3">
                            <label for="forgot-email" class="form-label">Email</label>
                            <input type="email" id="forgot-email" name="email" class="form-control"
                                placeholder="nama@email.com" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const eyeBtn = document.getElementById('eyeBtn');
        const passField = document.getElementById('password');
        if (eyeBtn && passField) {
            eyeBtn.addEventListener('click', () => {
                passField.type = (passField.type === 'text') ? 'password' : 'text';
                // Opsional: toggle icon
                eyeBtn.querySelector('i')?.classList.toggle('bx-hide');
                eyeBtn.querySelector('i')?.classList.toggle('bx-show');
            });
        }
    </script>
    <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
</body>

</html>
