@extends('templates.app')
@section('content')

<div class="container my-5">
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <h3 class="my-3">Data Sampah : Jadwal Tayangan</h3>
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Poster</th>
            <th>Title</th>
            <th>Genre</th>
            <th>Aksi</th>
        </tr>
        @foreach ($movies as $key => $m)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td><img src="{{ asset('storage/' . $m->poster)  }}" alt="" width="80" ></td>
            <td>{{ $m->title }}</td>
            <td>{{ $m->genre }}</td>
            <td class="d-flex align-items-center">
                <form action="{{ route('admin.movies.restore', ['id' => $m->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Kembalikan</button>
                </form>
                <form action="{{ route('admin.movies.deletepermanent', ['id' => $m->id]) }}" method="POST" class="ms-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus selamanya</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>

@endsection
