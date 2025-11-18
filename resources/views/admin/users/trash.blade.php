@extends('templates.app')
@section('content')

<div class="container my-5">
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <h3 class="my-3">Data Sampah : Jadwal Tayangan</h3>
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        @foreach ($user as $key => $u)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role }}</td>
            <td class="d-flex align-items-center">
                <form action="{{ route('admin.users.restore', ['id' => $u->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Kembalikan</button>
                </form>
                <form action="{{ route('admin.users.deletepermanent', ['id' => $u->id]) }}" method="POST" class="ms-2">
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
