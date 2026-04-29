<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('/assets') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Topang</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/assets/img/favicon/favicon.ico') }}" />

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
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />


    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WE8MH7S3B2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-WE8MH7S3B2');
    </script>

    <!-- Theme override PUPR -->
    <style>
        :root {
            /* Brand PUPR */
            --pupr-blue: #003A70;
            --pupr-blue-2: #145EA8;
            --pupr-yellow: #F5C518;
            --pupr-ink: #1F2937;
            /* Override Bootstrap */
            --bs-body-color: var(--pupr-ink);
            --bs-primary: var(--pupr-blue);
            --bs-warning: var(--pupr-yellow);
            --bs-link-color: var(--pupr-blue);
            --bs-link-hover-color: #0b62b3;
        }

        .page-section {
            margin-block: 1.25rem;
        }

        /* Card bersih */
        .card-modern {
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .04);
        }

        /* Header putih, aksen garis bawah */
        .card-modern .card-header {
            background: #fff;
            color: #1f2937;
            border: 0;
            padding: 14px 18px;
            border-bottom: 3px solid var(--pupr-blue);
            /* aksen warna di border saja */
        }

        .card-modern .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .card-header .gap-2 {
            gap: .5rem !important;
        }

        .bx.fs-4 {
            line-height: 1;
        }



        .btn-modern {
            border-radius: 12px;
        }


        .btn-primary {
            background-color: var(--pupr-blue);
            border-color: var(--pupr-blue);
        }

        .btn-primary:hover {
            filter: brightness(1.05);
        }

        .btn-outline-primary {
            color: var(--pupr-blue);
            border-color: var(--pupr-blue);
            background: #fff;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background: var(--pupr-blue);
            /* ← solid PUPR blue */
            color: #fff;
            border-color: var(--pupr-blue);
            box-shadow: 0 6px 14px rgba(0, 58, 112, .18);
        }



        .badge-soft {
            background: #F2F6FC;
            color: #334155;
            border: 1px solid #E6ECF5;
            border-radius: 999px;
            padding: .35rem .7rem;
            font-weight: 600;
            font-size: .75rem;
        }

        .table-modern thead th {
            background: #F8FAFC;
            color: #4B5563;
            font-weight: 600;
            border-bottom: 1px solid #EEF2F7;
        }

        .table-modern tbody tr:hover {
            background: #FAFCFF;
        }

        .form-floating>.form-control,
        .form-floating>.form-select {
            border-radius: 12px;
        }

        .modal-modern .modal-content {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .12);
        }

        .modal-modern .modal-header {
            border: 0;
            padding-bottom: 0;
        }

        .modal-modern .modal-footer {
            border: 0;
            padding-top: 0;
        }

        .text-brand {
            color: var(--pupr-blue) !important;
        }

        .bg-brand {
            background: var(--pupr-blue) !important;
            color: #fff !important;
        }

        .ring-1 {
            box-shadow: 0 0 0 1px rgba(0, 0, 0, .06) inset;
            border-radius: 12px;
        }
    </style>

    <!-- Helpers -->
    @section('head')
    @show
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}">
        < /h >

        <
        script src = "{{ asset('/assets/js/config.js') }}" >
    </script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Sidebar -->
            @yield('sidebar')
            <!-- /Sidebar -->
            <div class="layout-page">
                @yield('nav')
                <div class="content-wrapper">
                    @section('content')
                    @show
                    <div class="customer-service" style="display: none;">
                        @include('components.customer_service')
                    </div>

                    <style>
                        @keyframes showElement {
                            from {
                                opacity: 0;
                            }

                            to {
                                opacity: 1;
                            }
                        }

                        .customer-service {
                            display: block !important;
                            opacity: 0;
                            animation: showElement 1s ease-in forwards;
                            animation-delay: 3s;
                            /* muncul di detik ke-5 */
                        }
                    </style>



                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                {{-- | v2 MBKM ITATS 2025 --}}
                                , by <span class="fw-bolder">Bapekom 6 Surabaya</span> <span
                                    style="font-size: 12px; color: #666;">
                                    Collab with MBKM UTM 2024
                                </span>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
    @stack('modals')
</body>


<script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('assets/vendor/js/menu.js/') }}"></script>
<script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/datatables.js') }}"></script>
{{-- sebelum </body> --}}

@stack('scripts')


@section('script')
@show

<!-- Main JS -->
<script src="{{ asset('/assets/js/main.js') }}"></script>



</html>
