@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('failed'))
            <div class="alert alert-success">{{ Session::get('failed') }}</div>
        @endif
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end mt-3 gap-2">
              <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
              <a class="btn btn-success" href="{{route('admin.users.trash')}}">Data Sampah</a>
             <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Data</a>

        </div>
        <table class="table table-responsive table-bordered mt-3" id="usertables">
            <h4>Data Pengguna (Admin & Staff)</h4>
            <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            </thead>

        </table>
    </div>
@endsection

@push('script')
<script>
    $(function(){
       $('#usertables').DataTable({
        processing : true,
        serverSide : true,
        ajax : "{{route('admin.users.datatables')}}",
        columns : [
            {data : 'DT_RowIndex', name: 'DT_Rowindex', orderable : false, searchable : false},
            {data : 'name', name: 'name', orderable : true, searchable : true},
            {data : 'email', name: 'email', orderable : true, searchable : true},
            {data : 'role', name: 'role', orderable : true, searchable : true},
            {data : 'buttons', name: 'buttons', orderable : false, searchable : false},
        ]
       })
    });
</script>
@endpush
