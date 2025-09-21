@extends('layout.auth_layout')
@section('sidebar')
    @include('layout.sidebar')
@endsection
@section('nav')
    @include('layout.navbar')
@endsection
@section('content')
    <!-- Content -->

@section('head')
    <link href="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
@endsection

<div class="container-fluid flex-grow-1 p-3">
    <div class="row">
        <div class="col">
            <div class="card calendar-container">
                <div class="card-body">
                  <h1 class="h3 fw-bold text-dark mb-4">Kalender</h1>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('/assets/vendor/libs/fullcalendar/lib/main.min.js') }}"></script>
<script>
    const venue = document.getElementById('venue');



    const getEvents = async () => {
        const response = await fetch('/api/events');
        const data = await response.json();
        return data;
    }

    document.addEventListener('DOMContentLoaded', async function() {
        var calendarEl = document.getElementById('calendar');
        const btnTrig = document.getElementById('btn-trigger');
        const startDate = document.getElementById('start');
        const endDate = document.getElementById('end');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialDate: new Date(),

            events: await getEvents(),
        });

        calendar.render();
    });
</script>
@endsection
