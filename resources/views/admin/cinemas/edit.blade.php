@extends('templates.app')

@section('content')
    <div class="w-75 d-block mx-auto mt-3 p-4">
        @if (Session::get('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger">{{ Session::get('failed') }}</div>
        @endif
        <h5 class="text-center mb-3">Edit Data Bioskop</h5>
        <form action="{{ route('admin.cinemas.update', $cinema->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Bioskop</label>
                <input type="text" name="name" id="name" class="form-control @error('name')
                    is-invalid
                @enderror" value="{{ $cinema->name }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Lokasi</label>
                <textarea name="location" id="location" rows="5" class="form-control @error('location')
                    is-invalid
                @enderror">{{ $cinema->location }}</textarea>
                @error('location')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Kirim Data</button>
            {{-- <button type="button" class="btn btn-danger"><a href="" class="">Batal</a></button> --}}
            <a href="{{ route('admin.cinemas.index') }}" class="btn btn-danger">Batal</a>
        </form>
    </div>
@endsection
