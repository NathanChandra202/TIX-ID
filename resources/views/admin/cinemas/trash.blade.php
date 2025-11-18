@extends('templates.app')
@section('content')

<div class="container my-5">
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <h3 class="my-3">Data Sampah : Jadwal Tayangan</h3>
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Nama Bioskop</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>
        @foreach ($cinema as $key => $c)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $c->name }}</td>
            <td>{{ $c->location }}</td>
            <td class="d-flex align-items-center">
                <form action="{{ route('admin.cinemas.restore', ['id' => $c->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Kembalikan</button>
                </form>
                <form action="{{ route('admin.cinemas.deletepermanent', ['id' => $c->id]) }}" method="POST" class="ms-2">
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
