@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end gap-3">
            {{-- karena modal isi tidak akan berubah, munculkan dengan bootstrap --}}
            <a href="{{ route('staff.schedule.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a class="btn btn-success" href="{{ route('staff.schedule.trash') }}">Data Sampah</a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Data</button>
        </div>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif

        <h3 class="my-3">Data Jadwal Tayangan</h3>
        <table class="table table-bordered" id="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Bioskop</th>
                    <th>Judul Film</th>
                    <th>Harga</th>
                    <th>Jam Tayang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        {{-- modal --}}
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('staff.schedule.store') }}">
                        @csrf
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="cinema_id" class="col-form-label">Bioskop :</label>
                                <select name="cinema_id" id="cinema_id"
                                    class="form-select @error('cinema_id') is-invalid @enderror">
                                    <option disabled hidden selected>Pilih Bioskop</option>
                                    @foreach ($cinemas as $cinema)
                                        <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('cinema_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="movie_id" class="col-form-label">Film :</label>
                                <select name="movie_id" id="movie_id"
                                    class="form-select @error('cinema_id') is-invalid @enderror">
                                    <option disabled hidden selected>Pilih Film</option>
                                    @foreach ($movie as $movie)
                                        <option value="{{ $movie['id'] }}">{{ $movie['title'] }}</option>
                                    @endforeach
                                </select>
                                @error('movie_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="price" class="col-form-label">Harga :</label>
                                <input type="number" name="price" id="price"
                                    class="form-control @error('price') is-invalid @enderror"></input>
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for=hours class="form-label">Jam Tayang :</label>
                                @if ($errors->has('hours . *'))
                                    <small class="text-danger">{{ $errors->first('hours. *') }}</small>
                                @endif
                                <input type="time" name="hours[]" id="hours" class="form-control"
                                    @if ($errors->has('hours.*')) is-invalid @endif>
                                <div id="additionalInput"></div>
                                <span class="text-primary my-3" style="cursor: pointer" onclick="addInput()">+ Tambah Input
                                    Jam</span>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function(){
            $("#table").DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("staff.schedule.datatables") }}',
                columns: [
                {data : 'DT_RowIndex', name: 'DT_Rowindex', orderable : false, searchable : false},
                 {data : 'cinemaTitle', name: 'cinemaTitle', orderable : true, searchable : true},
                {data : 'movieTitle', name: 'movieTitle', orderable : true, searchable : true},

                {data : 'price', name: 'price', orderable : false, searchable : false},
                {data : 'hours', name: 'hours', orderable : false, searchable : false},
                {data : 'buttons', name: 'buttons', orderable : false, searchable : false},]
            })
        })
    </script>


    <script>
        function addInput() {
            let content = `<input type="time" name="hours[]" class="form-control my-2">`;
            let wrap = document.querySelector("#additionalInput");
            wrap.innerHTML += content;
        }
    </script>
    @if ($errors->any())
        <script>
            let modalAdd = document.querySelector('#modalAdd');
            new bootstrap.Modal(modalAdd).show();
        </script>
    @endif
@endpush
