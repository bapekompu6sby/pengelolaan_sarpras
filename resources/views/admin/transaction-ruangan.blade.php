@extends('layout.index')
@section('sidebar')
@include('layout.sidebar')
@endsection
@section('nav')
@include('layout.nav')
@endsection

@section('head')
<link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
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
  <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Peminjaman/</span> Tambah peminjaman</h4>

  @if($errors->any())
  <div>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <h5 class="text-danger">Masukkan data dengan benar</h5>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close  btn-lg" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
  @endif

  <div class="row">
    <div class="col">
      <div class="card calendar-container">
        <div class="card-body">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal add event -->
  <div class="modal fade" id="addEvent" tabindex="-1" role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form action="{{ route('transactions.ruangan.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="addEventLabel">Add Event</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="office" class="form-label">Instansi</label>
              <input type="text" class="form-control" id="office" name="office" required>
            </div>
            <div class="mb-3">
              <label for="event" class="form-label">Kegiatan</label>
              <input type="text" class="form-control" id="event" name="event" required>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="start" class="form-label">Mulai</label>
                <input type="date" class="form-control" id="start" name="start" required>
              </div>
              <div class="col mb-3">
                <label for="end" class="form-label">Selesai</label>
                <input type="date" class="form-control" id="end" name="end" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="venue" class="form-label">Ruangan</label>
              <select class="form-select" id="venue" name="venue" required>
                <option selected disabled>Pilih Ruangan</option>
                @foreach ($properties as $property)
                <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- / Modal add event -->

  <!-- Modal trigger -->
  <button id="btn-trigger" type="button" class="btn btn-primary invisible" data-bs-toggle="modal"
    data-bs-target="#addEvent">
    Add Event
  </button>


  @endsection

  @section('script')
  <script src="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.js') }}"></script>
  <script>
    const getEvents = async () => {
      const response = await fetch('/api/events');
      const data = await response.json();
      return data;
    }

    document.addEventListener('DOMContentLoaded', async function () {
      var calendarEl = document.getElementById('calendar');
      const btnTrig = document.getElementById('btn-trigger');
      const startDate = document.getElementById('start');
      const endDate = document.getElementById('end');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialDate: new Date(),
        customButtons: {
          addEventButton: {
            text: 'Tambah',
            click: function () {
              btnTrig.click();
            }
          },
          listEventButton: {
            text: 'List Kegiatan',
            click: function () {
              window.location.href = '/transactions/ruangan/list';
            }
          }
        },
        headerToolbar: {
          left: 'addEventButton listEventButton',
          center: 'title',
        },
        selectable: true,
        eventClick: function (arg) {
          console.log(arg.event.title);
        },
        businessHours: true,
        dayMaxEvents: true, // allow "more" link when too many events
        events: await getEvents(),
      });

      calendar.render();
    });
  </script>
  @endsection