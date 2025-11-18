@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('login'))
            <div class="alert alert-success w-100">{{ Session::get('login') }} <b>Selamat Datang,
                    {{ Auth::user()->name }}</b>
            </div>
        @endif
        @if (Session::get('success'))
            <div class="alert alert-success w-100">{{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger w-100">{{ Session::get('error') }}
            </div>
        @endif
        <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
              <a class="btn btn-success" href="{{route('staff.promos.trash')}}">Data Sampah</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5>Data Promo</h5>
        <table class="table table-bordered" id="promotables">
          <thead>
              <tr>
                <th>#</th>
                <th>Kode Promo</th>
                <th>Total Potongan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
          </thead>
        </table>
    </div>
@endsection

@push('script')
<script>
    $(function(){
       $('#promotables').DataTable({
        processing : true,
        serverSide : true,
        ajax : "{{route('staff.promos.datatables')}}",
        columns : [
            {data : 'DT_RowIndex', name: 'DT_Rowindex', orderable : false, searchable : false},
            {data : 'promo_code', name: 'promo_code', orderable : true, searchable : true},
            {data : 'discount', name: 'discount', orderable : true, searchable : true},
            {data : 'activedBadge', name: 'activedBadge', orderable : true, searchable : true},
            {data : 'buttons', name: 'buttons', orderable : false, searchable : false},
        ]
       })
    });
</script>
@endpush
