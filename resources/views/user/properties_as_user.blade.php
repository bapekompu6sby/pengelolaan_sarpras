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
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Ruangan</h4>
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
                    <div class="table-responsive text-nowrap p-4">
                        @foreach ($properties as $property)
                            <div
                                style="display: flex; width: 100%; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-family: Arial, sans-serif; gap: 20px; margin-bottom: 20px;">
                                <div
                                    style="flex-shrink: 0; width: 250px; height: 250px; overflow: hidden; border-radius: 8px;">
                                    <img src="{{ asset('uploads/' . $property->image_path) }}" alt="{{ $property->name }}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" />
                                </div>


                                <div style="flex-grow: 1; color: #333;">
                                    <h3 style="margin-top: 0; margin-bottom: 12px;">{{ $property->name }}</h3>

                                    <p style="margin: 4px 0;">
                                        <strong>Tipe:</strong> {{ strtoupper($property->room_type) }}
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Kapasitas:</strong> {{ $property->capacity }} orang
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Luas:</strong> {{ $property->area }} m<sup>2</sup>
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Fasilitas:</strong> {{ $property->facilities }}
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Harga:</strong> Rp {{ number_format($property->price, 0, ',', '.') }} / Per
                                        hari Per Ruangan
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Unit:</strong> {{ $property->unit }}
                                    </p>
                                    <p style="margin: 4px 0;">
                                        <strong>Status:</strong> <span
                                            style="color: #028800; font-weight: bold;">tersedia</span>
                                    </p>

                                    <a href="{{ route('transactions.pinjam', $property->id) }}"
                                        style="text-decoration: none; color: white; background-color: #0d6efd; padding: 10px 15px; border-radius: 5px; font-weight: bold;">
                                        Pesan Sekarang
                                    </a>
                                    
                                </div>
                            </div>
                        @endforeach
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
@endsection
