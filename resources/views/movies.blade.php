@extends('templates.app')

@section('content')
    <div class="container my-3">
        <div class="mb3">Seluruh Film Sedang Tayang</div>
        {{-- form untuk search : metod="GET" karna akan menampilkan data bukan menambah data, action="" karna
         di prosess ke fungsi dan halaman yang sama --}}
        <form action="" method="GET">
            <div class="row">
                <div class="col-10">
                    <input type="text" class="form-control" placeholder="Cari judul film" name="search_movie">
                </div>
                <div class="col-2"> <button type="submit" class="btn btn-primary">Cari</button></div>
            </div>
        </form>
        <div class="d-flex justify-content-center gap-4 my-3 flex-wrap">
            @foreach ($movies as $item)
                <div class="card" style="width: 13rem; margin : 10px;">
                    <img style="object-fit: cover; height: 300px;" src="{{ asset('storage/' . $item->poster) }}" alt="Sunset"
                        class="card-img-top">
                    <div class="card-body" style="padding: 0 !important; height: 50px">
                        <div class="justify-content-center d-flex text-truncate">
                            {{ $item->title }}
                        </div>
                        <p class="card-text text-center shadow py-2">
                           <a href="{{ route('schedules.detail', $item->id) }}" class="text-warning btn btn-primary">Beli Tiket</a>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
