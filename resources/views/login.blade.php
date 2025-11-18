@extends('templates.app')

@section('content')
    <form class="container w-50 mx-auto d-block my-5" method="POSt" action="{{ route('login.auth') }}">
        @csrf
        <!-- Email input -->
        @if (Session::get('ok'))
            <div class="alert alert-success">{{ Session::get('ok') }}</div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger w-100">{{ Session::get('error') }}</div>
        @endif
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" id="form2Example1"
                class="form-control @error('email')
                    is-invalid
                @enderror" name="email" value="{{ old('email') }}">
            <label class="form-label" for="form2Example1">Email</label>
        </div>
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        <!-- Password input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="password" id="form2Example2"
                class="form-control @error('password')
                    is-invalid
                 @enderror"
                name="password" value="{{ old('password') }}"/>
            <label class="form-label" for="form2Example2">Password</label>
        </div>

        <!-- 2 column grid layout for inline styling -->
        <div class="row mb-4">
            <div class="col-6 d-flex justify-content-between">
                <!-- Checkbox -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="form2Example34" checked />
                    <label class="form-check-label" for="form2Example34"> Ingatkan saya </label>
                </div>
            </div>

            <div class="col-6 justify-content-end">
                <!-- Simple link -->
                <a href="#!">Lupa password?</a>
            </div>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4">Sign in</button>

        <!-- Register buttons -->
        <div class="text-center">
            <p>Tidak punya akun? <a href="{{ route('signup') }}">Register</a></p>
            <p>Atau sign up dengan:</p>
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
