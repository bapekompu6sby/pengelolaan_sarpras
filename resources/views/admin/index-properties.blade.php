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
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Data master/</span> Ruangan</h4>
  <div class="row">
    <div class="col-lg-12 mb-4 order-0">
      <div>
        @if ($errors->any())
        <div class="alert alert-danger">
          <h5 class="text-danger">Masukkan data dengan benar</h5>
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
      </div>
      <div class="card">
        <div class="d-flex justify-content-between">
          <div class="head">
            <h5 class="card-header">Sarana & Prasarana</h5>
          </div>
          <div class="my-auto pe-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCenter">
              <span class="tf-icons bx bx-plus-medical bx-sm"></span>
            </button>
          </div>
        </div>
        <div class="table-responsive text-nowrap p-4">
          <table id="datatable" class="display table table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Properti</th>
                <th>Jenis</th>
                <th>Kapasistas</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @foreach ($properties as $property)
              <tr>
                <td><i class="fab fa-react fa-lg text-info me-3"></i> <strong>{{ $property->name }}</strong></td>
                <td>{{ ucfirst($property->type) }}</td>
                <td>
                  {{ $property->capacity }}
                </td>
                <td><span class="badge bg-label-success me-1">Kosong</span></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalCenter{{ $property->id }}"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                      <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $property->id }}"><i class="bx bx-trash me-2"></i> Delete</a>
                    </div>
                  </div>
                </td>
              </tr>

              @include('components.edit-modal', ['property' => $property])
              @include('components.confirm-modal', ['property' => $property])

              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="modalCenter" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Tambah ruangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('properties.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col mb-3">
              <label for="nameWithTitle" class="form-label">Nama Ruangan</label>
              <input type="text" id="nameWithTitle" class="form-control" placeholder="Masukkan nama ruangan" name="name"
                value="{{ old('name') }}" />
            </div>
          </div>
          <div class="row g-2">
            <div class="col mb-0">
              <label for="exampleFormControlSelect1" class="form-label">Jenis Sarana</label>
              <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example"
                name="type">
                <option selected disabled>Open this select menu</option>
                <option value="kelas">Kelas</option>
                <option value="aula">Aula</option>
              </select>
            </div>
            <div class="col mb-0">
              <label for="dobWithTitle" class="form-label">Kapasistas</label>
              <input type="number" id="dobWithTitle" class="form-control" placeholder="Kapasistas ruangan" min="1"
                name="capacity" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('script')

<script src="{{ asset('/assets/vendor/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/datatables.js') }}"></script>

@endsection