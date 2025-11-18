@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Tiket Aktif</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Tiket Kadaluarsa</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <h5 class="my-4">Data Tiket Aktif, {{Auth::user()->name}}</h5>
                    @forelse ($ticketActive as $ticket )
                        <a href="{{route('tickets.show', $ticket->id)}}" class="text-decoration-none">
                            <div class="card mb-3 p-3 shadow-sm">
                                <strong>{{$ticket['schedule']['cinema']['name']}}</strong>
                                <div>Tanggal : {{$ticket['ticketPayment']['booked_date']}}</div>
                                <div>Jam : {{$ticket['hour']}}</div>
                                <span class="badge bg-success mt-2">Aktif</span>
                            </div>
                        </a>
                    @empty
                    <p class="text-muted">Tidak ada tiket aktif.</p>
                    @endforelse
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <h5 class="my-4">Data Tiket Kadaluarsa, {{Auth::user()->name}}</h5>
                    @forelse ($ticketNonActive as $ticket )
                            <div class="card mb-3 p-3 shadow-sm">
                                <strong>{{$ticket['schedule']['cinema']['name']}}</strong>
                                <div>Tanggal : {{$ticket['ticketPayment']['booked_date']}}</div>
                                <div>Jam : {{$ticket['hour']}}</div>
                                <span class="badge bg-danger mt-2">Kadaluarsa</span>
                            </div>
                    @empty
                    <p class="text-muted">Tidak ada tiket kadaluarsa.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
