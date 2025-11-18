@extends('templates.app')

@section('content')
    <div class="w-75 mx-auto my-5">
                @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('staff.promos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="" class="form-label">Kode Promo</label>
                <input type="text" class="form-control" name="promo_code" value="{{ old('promo_code') }}">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Promo</label>
                <select name="type" id="type" rows="5" class="form-select
                    @error('type')
                        is-invalid
                    @enderror
                " value="{{ old('type') }}">
                    <option value="percent">%</option>
                    <option value="rupiah">Rupiah</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Jumlah Potongan</label>
                <input type="number" class="form-control" name="discount" value="{{ old('discount') }}">
                @error('discount')
                  <small class="text-danger">{{$message}}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Kirim data</button>
        </form>
    </div>
@endsection
