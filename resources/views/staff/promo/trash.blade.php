@extends('templates.app')
@section('content')

<div class="container my-5">
    <div class="d-flex justify-content-end">
        <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <h3 class="my-3">Data Sampah : Jadwal Tayangan</h3>
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Promo</th>
            <th>Discount</th>
            <th>Type</th>
        </tr>
        @foreach ($promo as $key => $p)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $p->promo_code }}</td>
            <td>{{ $p->discount }}</td>
            <td>{{ $p->type }}</td>
            <td class="d-flex align-items-center">
                <form action="{{ route('staff.promos.restore', ['id' => $p->id]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Kembalikan</button>
                </form>
                <form action="{{ route('staff.promos.deletepermanent', ['id' => $p->id]) }}" method="POST" class="ms-2">
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
