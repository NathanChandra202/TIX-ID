@extends('templates.app')
{{-- @ -> engine --}}
@section('content')
    <form class="container w-75 mx-auto my-5 d-block" method="POST" action="{{ route('signup.store') }}">
        {{-- @csrf : sebagai key agar data form bisa diakses server/controler --}}
        @csrf
        <div class="row mb-4">
            <div class="col">
                <div data-mdb-input-init class="form-outline">
                    <input type="text" name="first_name" id="form3Example1"
                    class="form-control
                    @error('first_name')
                        is-invalid
                    @enderror" value="{{ old('first_name') }}"/>
                    <label class="form-label" for="form3Example1">Nama depan</label>
                </div>
                {{-- mengambil error validate input --}}
                @error('first_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col">
                <div data-mdb-input-init class="form-outline">
                    <input type="text" name="last_name" id="form3Example2" class="form-control @error('last_name')
                        is-invalid
                    @enderror" value="{{ old('last_name') }}"/>
                    <label class="form-label" for="form3Example2">Nama belakang</label>
                </div>
                @error('last_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Email input -->
        @error('email')
                <small class="text-danger">{{ $message }}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" name="email" id="form3Example3" class="form-control @error('email')
                is-invalid
            @enderror" value="{{ old('email') }}"/>
            <label class="form-label" for="form3Example3">Email</label>
        </div>

        <!-- Password input -->
        @error('password')
                <small class="text-danger">{{ $message }}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="password" name="password" id="form3Example4" class="form-control @error('password')
                is-invalid
            @enderror" value="{{ old('password') }}"/>
            <label class="form-label" for="form3Example4">Password</label>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4">Sign up</button>

        <!-- Register buttons -->
        <div class="text-center">
            <p>atau sign up dengan:</p>
            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                <i class="fab fa-facebook-f"></i>
            </button>

            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                <i class="fab fa-google"></i>
            </button>

            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                <i class="fab fa-twitter"></i>
            </button>

            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1">
                <i class="fab fa-github"></i>
            </button>
        </div>
    </form>
@endsection
