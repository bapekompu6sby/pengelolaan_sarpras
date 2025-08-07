<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('/assets') }}"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Topang</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>

    <!-- Page CSS -->

    <!-- Helpers -->
    @section('head')
    @show
    <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></h >

    <script src="{{ asset('/assets/js/config.js') }}" ></script>
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
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  , by <span class="fw-bolder">Bapekom 6 Surabaya</span> colaborate with <span class="fw-bolder">MBKM 2024 UTM</span> 
                </div>
              </div>
            </footer>
          </div>
        </div>
      </div>
    </div>
  </body>


  <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

  <script src="{{ asset('assets/vendor/js/menu.js/') }}"></script>

  @section('script')
  @show

  <!-- Main JS -->
  <script src="{{ asset('/assets/js/main.js') }}"></script>
</html>