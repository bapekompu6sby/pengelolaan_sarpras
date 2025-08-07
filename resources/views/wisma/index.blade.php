@extends('layout.index')
@section('sidebar')
@include('layout.sidebar')
@endsection
@section('nav')
@include('layout.nav')
@endsection
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Ketersediaan/</span> Asrama & Paviliun</h4>
  <div class="row">
    <div class="col-md-6 col-lg-4 mb-3">
      <div class="card h-100 border border-info border-5 border-top-0 border-bottom-0 border-end-0">
        <div class="card-body">
          <h5 class="card-title">Wisma Suhodo</h5>
          <p class="card-text">
          Wisma Suhodo, terletak pada sebelah barat ruang makan 1 yang memiliki 3 lantai dimana untuk penomorannya nomor ganjil pada sebelah timur dan nomor genap pada sebelah barat
          </p>
          <a href="javascript:void(0)" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#roomsSuhodo">Cek Ketersedian</a>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-3">
      <div class="card h-100 border border-warning border-5 border-top-0 border-bottom-0 border-end-0">
        <div class="card-body">
          <h5 class="card-title">Wisma Surjono</h5>
          <p class="card-text">
          Wisma Surjono, terlatak pada sebelah timur ruang makan 1. Wisma Surjono memiliki 3 lantai dimana untuk penomorannya nomor ganjil pada sebelah timur dan nomor genap pada sebelah barat
          </p>
          <a href="javascript:void(0)" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#roomsSurjono">Cek Ketersedian</a>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-3">
      <div class="card h-100 border border-danger border-5 border-top-0 border-bottom-0 border-end-0">
        <div class="card-body">
          <h5 class="card-title">Paviliun</h5>
          <p class="card-text">
          Paviliun terletak pada bagian paling barat. Paviliun diperuntukan untuk orang - orang penting atau para instruktur yang datang untuk memberikan pelatihan. Paviliun memiliki 1 lantai.
          </p>
          <a href="javascript:void(0)" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#roomsPaviliun">Cek Ketersedian</a>
        </div>
      </div>
    </div>
  </div>

  @include('components.suhodo-modal')
  @include('components.surjono-modal')
  @include('components.paviliun-modal')
</div>
<script>
    // Manage rooms 
    // get all the button element
    const button = document.querySelectorAll('td');
    // convert php collection to js array
    const seats = @json($wisma);
    const nama = @json($nama);
    const kegiatan = @json($kegiatan);
    // check if seat is available from the server
    for (let i = 0; i < button.length; i++) {
      if (seats.includes((button[i].getAttribute('data-name')))) {
        button[i].classList.add('notAvailable');
        let pos = seats.indexOf(button[i].getAttribute('data-name'));

        button[i].classList.add('notAvailable');
        button[i].setAttribute("data-bs-toggle", "tooltip")
        button[i].setAttribute("title", `${nama[pos]} <br> ${kegiatan[pos]}`);
        button[i].setAttribute("data-bs-offset", "0,4");
        button[i].setAttribute("data-bs-html", "true");
        button[i].setAttribute("data-bs-placement", "top");
      }
    }
</script>
@endsection