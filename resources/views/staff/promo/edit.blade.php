@extends('templates.app')

@section('content')
    <div class="w-75 mx-auto my-5">
        @if (Session::get('error'))
            <div class="alert alert-success">{{ Session::get('failed') }}</div>
        @endif
        <form action="{{ route('staff.promos.update', $promo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="" class="form-label">Kode Promo</label>
                <input type="text" class="form-control" name="promo_code" value="{{ $promo->promo_code }}">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe Promo</label>
                <select name="type" id="type" rows="5" class="form-select
                    @error('type')
                        is-invalid
                    @enderror
                ">
                    <option value="percent" {{ $promo->type == 'percent' ? 'selected' : '' }}>%</option>
                    <option value="rupiah" {{ $promo->type == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Jumlah Potongan</label>
                <input type="number" class="form-control" name="discount" value="{{ $promo->discount }}">
            </div>

            <button type="submit" class="btn btn-primary">Kirim data</button>
        </form>
    </div>
@endsection
