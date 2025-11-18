@extends('templates.app')

@section('content')
    <div class="container mt-5">
        {{-- <h5>Grafik Pembelian Tiket</h5> --}}
        <div class="row">
            <div class="col-6">
                <h5>Data Pembelan Tiket Bulan {{ now()->format('F') }}</h5>
                <canvas id="chartBar"></canvas>
            </div>
            <div style="width: 300px; height: 300px; margin: auto;">
                <h5>Data Film Sesuai Status</h5>
                <canvas id="chartPie"></canvas>
            </div>

        </div>
    </div>
@endsection
@push('script')
    {{-- <script>
        const ctx = document.getElementById('chartBar');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script> --}}
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: "GET",
                success: function(res) {
                    let labels = res.labels;
                    let data = res.data;
                    new Chart($('#chartBar')[0], {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Tiket Terjual',
                                data: data,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                error: function(err) {
                    alert('Gagal Memuat Data!');
                }
            });
        });
    </script>
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('admin.movies.chart') }}",
                method: "GET",
                success: function(res) {
                    let labels = res.labels;
                    let data = res.data;
                    new Chart($('#chartPie')[0], {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: labels,
                                data: data,
                                backgroundColor: [
                                    'rgb(80, 200 ,120)',
                                    'rgb(255, 0, 0)',
                                ],
                                hoverOffset: 1

                            }]
                        }
                    });

                },
                error: function(err) {
                    alert('Gagal Memuat Data!');
                }
            });
        });
    </script>
@endpush
dashboard
