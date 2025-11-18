@extends('templates.app')

@section('content')

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        {{-- header Lokasi Bioskop --}}
                        @if (count($schedules) > 0)
                            <h4 class="card-title mb-4 border-bottom pb-3">
                                <i class="fa-solid fa-location-dot me-2 text-danger"></i>
                                {{ $schedules[0]['cinema']['location'] }}
                            </h4>
                        @endif

                        {{-- Loop buat setiap jadwal film --}}
                        @foreach ($schedules as $schedule)
                            <div class="schedule-item mb-4">
                                <div class="row g-3">
                                    {{-- Kolom Poster Film --}}
                                    <div class="col-md-2">
                                        <div style="width: 150px;">
                                            <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}"
                                                alt="{{ $schedule['movie']['title'] }}" class="w-100 rounded shadow-sm">
                                        </div>
                                    </div>

                                    {{-- Kolom Detail Film --}}
                                    <div class="col-md-10">
                                        <h5 class="fw-bold ms-4 mt-5">{{ $schedule['movie']['title'] }}</h5>
                                        <div class="movie-details ms-4 mb-5" style="max-width: 450px;">
                                            <div class="d-flex">
                                                <b class="text-secondary" style="width: 120px;">Genre</b>
                                                <span>{{ $schedule['movie']['genre'] }}</span>
                                            </div>
                                            <div class="d-flex">
                                                <b class="text-secondary" style="width: 120px;">Duration</b>
                                                <span>{{ $schedule['movie']['duration'] }}</span>
                                            </div>
                                            <div class="d-flex">
                                                <b class="text-secondary" style="width: 120px;">Sutradara</b>
                                                <span>{{ $schedule['movie']['director'] }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <b class="text-secondary" style="width: 120px;">Rating Usia</b>
                                                {{-- Badge Bootstrap 5 --}}
                                                <span class="badge bg-success">{{ $schedule['movie']['age_rating'] }}+</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bagian Harga dan Jam Tayang --}}
                                <div class="w-100 my-3">
                                    {{-- Harga di kanan --}}
                                    <div class="text-end">
                                        <h5 class="mb-2 text-dark fw-bold">
                                            Rp. {{ number_format($schedule['price'], 0, ',', '.') }}
                                        </h5>
                                    </div>

                                    {{-- Jam Tayang --}}
                                    <div class="d-flex flex-wrap gap-2 my-2"> {{-- flex-wrap agar responsif --}}
                                        @foreach ($schedule['hours'] as $index => $hours)
                                            <div class="btn btn-outline-secondary" style="cursor: pointer"
                                                onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                                {{ $hours }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            @if (!$loop->last)
                                <hr class="my-4">
                            @endif
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Beli Tiket di Bawah --}}
    <div class="w-100 p-3 bg-light text-center fixed-bottom shadow-lg" id="wrapBtn" style="border-top: 1px solid #ddd;">
        {{-- Styling awal untuk tombol --}}
        <a href="javascript:void(0)" id="btnTicket" class="text-decoration-none text-secondary text-uppercase fw-bold" style="font-size: 1.1rem;">
            <i class="fa-solid fa-ticket me-2"></i> BELI TIKET
        </a>
    </div>
@endsection

@push('script')
    <script>
        let elementBefore = null;

        function selectedHour(scheduleId, hourId, el) {
            //jika element sebelumnya ada, dan skrg pindah ke element lain kliknya jd putih lg
            if (elementBefore) {
                //ubah styling css : style.property
                elementBefore.style.background = "";
                elementBefore.style.color = "";
                //property css kebab (boreder-color) di js jadi camel (BorderColor)
                elementBefore.style.borderColor = "";
            }

            //kasi warna ke element baru
            el.style.background = "#112646",
                el.style.color = "white";
            el.style.borderColor = "#112646";
            //update element sebelumnya pake yg baru
            elementBefore = el;

            let wrapBtn = document.querySelector('#wrapBtn');
            let btnTicket = document.querySelector('#btnTicket');
            //kasi warna biru ke div wrap dan hilangkan warna abu
            //warnna abu dari 'bg-light' class bootsrtrap
            wrapBtn.classList.remove('bg-light');
            wrapBtn.style.background = "#112646";

            let url = "{{ route('schedules.show_seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}"
                .replace(':scheduleId', scheduleId)
                .replace(':hourId', hourId);
            // isi url ke href btnTicket
            btnTicket.href = url;
            btnTicket.style.color = 'white';

        }
    </script>
@endpush
