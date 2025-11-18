@extends('templates.app')
@section('content')

<div class="container my-5">
    <div class="d-flex justify-content-end">
        <a href="{{route('staff.schedule.index')}}" class="btn btn-secondary">kembali</a>
    </div>
    <h3 class="my-3">Data Sampah : Jadwal Tayangan</h3>
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Nama Bioskop</th>
            <th>Judul Film</th>
            <th>Aksi</th>
        </tr>
        @foreach ( $schedule as $key => $schedule )
         <tr>
            <td>{{$key+1}}</td>
            <td>{{$schedule['cinema']['name']}}</td>
            <td>{{$schedule->movie?->title ?? '-'}}</td>
            <td class="d-flex align-item-center">
                <form action="{{ route('staff.schedule.restore', $schedule['id']) }}" method="POST">
                     @csrf
                     @method('PATCH')
                     <button type="submit" class="btn btn-success">Kembalikan</button>
                </form>
                <form action="{{ route('staff.schedule.deletepermanent', $schedule['id']) }}" method="POST"class="ms-2">
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
