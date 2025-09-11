<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Reset Password</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
</head>

<body>
    @if (session('success'))
        <x-toast bgColor="bg-success" title="Success">
            {{ session('success') }}
        </x-toast>
    @endif

    @if (session('error'))
        <x-toast bgColor="bg-danger" title="Error">
            {{ session('error') }}
        </x-toast>
    @endif

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body m-">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('/assets/img/favicon/logo.png') }}" width="50" alt="Topang">
                            </span>
                            <span class="app-brand-text demo menu-text fw-bolder ms-2">Topang</span>
                        </div>


                        <p class="mb-2 text-left">Silakan masukkan password baru anda</p>

                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <!-- Form Reset Password -->
                        <form method="POST" action="{{ route('reset.password') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <div class="mb-3">
                                <label class="form-label" for="password">Password Baru</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password" id="password" class="form-control" required
                                        placeholder="Masukkan password baru" />
                                    <span class="input-group-text cursor-pointer" id="togglePass"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control" required placeholder="Konfirmasi password baru" />
                                    <span class="input-group-text cursor-pointer" id="togglePass2"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                                @error('password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            

                            <div class="mb-3 mt-5">
                                <button type="submit" class="btn btn-primary d-grid w-100">Reset Password</button>
                            </div>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('login') }}"> <button type="submit"
                                    class="btn btn-primary d-grid w-100 ">Kembali ke halaman login</button></a>
                        </div>

                        <p class="text-center mt-3 mb-6">
                            <span>Belum punya akun topang?</span>
                            <a href="{{ route('register') }}">
                                <span>Klik disini</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /card -->
            </div>
        </div>
    </div>

    <script>
        // Toggle show/hide password
        const t1 = document.getElementById('togglePass');
        const p1 = document.getElementById('password');
        const t2 = document.getElementById('togglePass2');
        const p2 = document.getElementById('password_confirmation');

        const toggle = (btn, field) => {
            if (!btn || !field) return;
            btn.addEventListener('click', () => {
                field.type = field.type === 'password' ? 'text' : 'password';
                btn.querySelector('i')?.classList.toggle('bx-hide');
                btn.querySelector('i')?.classList.toggle('bx-show');
            });
        };
        toggle(t1, p1);
        toggle(t2, p2);
    </script>

    <!-- Pakai bundle agar semua komponen (modal/tooltip) jalan -->
    <script src="{{ asset('/assets/vendor/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
</body>

</html>
