@extends('templates.app')
@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <h3 class="text-center mb-4">Ringkasan order</h3>
            <div class="d-flex mb-4">
                <div class="me-2">
                    <img src="{{ asset('storage/' . $ticket['schedule']['movie']['poster']) }}" width="140" alt="Foto">
                </div>
                <div>
                    <h4 class="text-warnin">{{ $ticket['schedule']['cinema']['name'] }}</h4>
                    <h4 class="text-warning">{{ $ticket['schedule']['movie']['title'] }}</h4>
                    <table>
                        <tr>
                            <td class="text-secondary"><b>Genre</b></td>
                            <td>{{ $ticket['schedule']['movie']['genre'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary"><b>Durasi</b></td>
                            <td>{{ $ticket['schedule']['movie']['duration'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary"><b>Director</b></td>
                            <td>{{ $ticket['schedule']['movie']['director'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary"><b>Rating Usia</b></td>
                            <td class="badge badge-danger ms-2"> {{ $ticket['schedule']['movie']['age_rating'] }}+</td>
                        </tr>
                    </table>
                </div>
            </div>
            <h5 class="text-secondary">Nomor Pesanan : {{ $ticket['id'] }}</h5>
            <hr>
            <b>Detail Transaksi</b>
            <table>
                <tr>
                    <td>2 Ticket</td>
                    <td style="padding: 0 30px;"></td>
                    <td><b>{{ implode(',', $ticket['row_of_seats']) }}</b></td>
                </tr>
                <tr>
                    <td>Kursi Regular</td>
                    <td style="padding: 0 30px;"></td>
                    <td><b>{{ $ticket['schedule']['price'] }}</b> <span
                            class="text-secondary">x{{ $ticket['quantity'] }}</span></td>
                </tr>
                <tr>
                    <td>Biaya Layanan</td>
                    <td style="padding: 0 30px;"></td>
                    <td><b>Rp. 4.000</b> <span class="text-secondary">x{{ $ticket['quantity'] }}</span></td>
                </tr>
            </table>
            <hr>
            <p>Pilih Promo :</p>
            <select name="promo_id" id="promo_id" class="form-select">
                <option selected hidden disabled>Pilih</option>
                @foreach ($promos as $promo)
                    <option value="{{ $promo['id'] }}">{{ $promo['promo_code'] }} -
                        {{ $promo['type'] == 'percent' ? $promo['discount'] . '%' : 'Rp. ' . number_format($promo['discount'], 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="fixed-bottom w-100 text-white p-3 text-center"
        style="font-weight: bold; cursor: pointer; background: #112646;" onclick="createBarcode('{{ $ticket['id'] }}')">
        Bayar Sekarang</div>
@endsection

@push('script')
    <script>
        function createBarcode(ticketId) {
            let promo = $('#promo_id').val();

            $.ajax({
                url: "{{ route('tickets.barcode', ['ticketId' => ':ticketId']) }}".replace(':ticketId', ticketId),
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    ticket_id: ticketId,
                    promo_id: promo
                },

                success: function(response) {
                    window.location.href = `/tickets/${ticketId}/payment`;
                },
                error: function(message) {
                    console.log(message);
                    alert('Gagal membuat barcode pembayaran!');
                }
            })
        }
    </script>
@endpush
