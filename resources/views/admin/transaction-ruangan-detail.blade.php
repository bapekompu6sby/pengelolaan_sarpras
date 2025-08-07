@extends('layout.index')
@section('sidebar')
@include('layout.sidebar')
@endsection
@section('nav')
@include('layout.nav')
@endsection

@section('head')
<link href="{{ asset('/assets/vendor/libs/datatables/datatables.min.css') }}" rel="stylesheet">
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
  <div class="row">
    <div class="col-lg-12 mb-4 order-0">
      <div class="card">
        <div class="d-flex justify-content-between">
          <div class="head">
            <h5 class="card-header">Peminjaman Ruangan</h5>
          </div>
          <div class="my-auto pe-4">
            @if(Auth::user()->role == 'admin')
              <a href="{{ route('transactions.ruangan.show') }}" class="btn btn-primary me-3">
                <span class="tf-icons bx bx-plus-medical bx-sm"></span>
              </a>
            @endif
            <a href="{{ route('transactions.ruangan.export') }}" class="btn btn-success" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" title="" data-bs-original-title="<i class='bx bx-spreadsheet bx-xs' ></i> <span>Export to excel</span>">
              <span class="tf-icons bx bx-cloud-download bx-sm"></span>
            </a>
          </div>
        </div>
        <div class="table-responsive text-nowrap p-4">
          <table id="datatable2" class="table">
            <thead>
              <tr>
                @if(Auth::user()->role == 'admin')
                  <th style="width: 10px">✔</th>
                @endif
                <th>Instansi</th>
                <th>Kegiatan</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                @if(Auth::user()->role == 'admin')
                  <th>Actions</th>
                @endif
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @foreach ($transactions as $t )
              <tr>
                @if(Auth::user()->role == 'admin')
                  <th>
                    <input type="checkbox" class="form-check-input" value="{{ $t->id }}">
                  </th>
                @endif
                <td><strong>{{ ucfirst($t->instansi) }}</strong></td>
                <td>{{ $t->kegiatan }}</td>
                <td>
                  {{ $t->properties->name }}
                </td>
                <td><span class="me-1">{{ date("d-m-Y", strtotime($t->start)) .' | '. date("d-m-Y", strtotime($t->end)) }}</span></td>
                @if(Auth::user()->role == 'admin')
                  <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                      data-bs-target="#modalCenter{{ $t->id }}">
                      <i class="bx bx-edit-alt me-2"></i>
                      Edit
                    </button>
                  </td>
                @endif
              </tr>

              @include('components.edit-modal')

              @endforeach
            </tbody>
          </table>
          <form action="{{ route('transactions.ruangan.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="text" class="d-none" id="selected" name="selected">
            <input type="button" class="btn btn-danger d-none" id="delete" value="Delete" data-bs-toggle="modal"
              data-bs-target="#modalDeleteRooms">

            <!-- Confirm modal -->
            <div class="modal fade" id="modalDeleteRooms" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Hapus ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus data ini?</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                      Close
                    </button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                  </div>
                </div>
              </div>
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

<script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/datatables.js') }}"></script>
<script>
  const check = document.querySelectorAll('.form-check-input');
  const deleteBtn = document.getElementById('delete');
  let formSelected = document.getElementById('selected');
  let selected = [];

  check.forEach((c) => {
    c.addEventListener('change', (e) => {
      let value = parseInt(e.target.value)
      if (e.target.checked) {
        selected.push(value);
      } else {
        selected = selected.filter((s) => s !== value);
      }
      formSelected.value = selected;
      console.log(formSelected);

      if (selected.length > 0) {
        deleteBtn.classList.remove('d-none');
      } else {
        deleteBtn.classList.add('d-none');
      }
    });
  });

</script>

@endsection