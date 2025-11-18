@extends('templates.app')

@section('content')
    <div class="w-75 d-block mx-auto mt-3 p-4">
        @if (Session::get('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        @if (Session::get('failed'))
            <div class="alert alert-danger">{{ Session::get('failed') }}</div>
        @endif
        <div class="shadow-sm p-3 mb-2 rounded text-body-secondary">Pengguna / Data / Edit</div>
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="shadow-sm p-3 mb-3 rounded">
            <h5 class="text-center mb-3">Ubah Data Staff</h5>
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control @error('name')
                    is-invalid
                @enderror" value="{{ $user->name }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Email</label>
                <input name="email" id="email" type="email" class="form-control @error('email')
                    is-invalid
                @enderror" value="{{ $user->email }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Password</label>
                <input name="password" id="password" type="password" class="form-control @error('password')
                    is-invalid
                @enderror">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <button type="submit" class="btn btn-primary w-100 mt-2">Buat</button>
                {{-- <a href="{{ route('admin.users.index') }}" class="btn btn-danger w-100 mt-2">Batal</a> --}}
            </div>
            {{-- <button type="button" class="btn btn-danger"><a href="" class="">Batal</a></button> --}}
        </form>
    </div>
@endsection
