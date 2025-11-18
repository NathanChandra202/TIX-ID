@extends('templates.app')
@section('content')
    <div class="container card my-5 p-4" style="margin-bottom: 10% !important">
        <div class="card-body">
            <b>{{ $schedule['cinema']['name'] }}</b>
            <br>
            {{-- mengambil tgl hari ini : carbon::now() --}}
            <b>{{ \Carbon\Carbon::now()->format('d M, Y') }} || {{ $hour }}</b>
            <div class="d-flex justify-content-center">
                <div class="row w-50">
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #112646;"></div>
                        <p class="ms-2">Kursi Kosong</p>
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #eaeaea;"></div>
                        <p class="ms-2">Kursi Terjual</p>
                    </div>
                    <div class="col-4 d-flex">
                        <div style="width: 20px; height: 20px; background: #3e85ef;"></div>
                        <p class="ms-2">Kursi Dipilih</p>
                    </div>
                </div>
            </div>

            @php
                //membuat array isi A-H untuk baris, 1-18 untuk no kursi
                $row = range('A', 'H');
                $col = range(1, 18);
            @endphp
            {{-- Looping untuk membuat barus A-H --}}
            @foreach ($row as $baris)
            <div class="d-flex justify-content-center my-1 text-center">
                    {{-- looping untuk membuat di satu baru --}}
                    @foreach ($col as $kursi)
                        {{-- Jika kursi no 7, tambahkan space kosong untuk jalan --}}
                        @if ($kursi == 4)
                            <div style="width: 35px;"></div>
                        @elseif($kursi == 16)
                            <div style="width: 35px;"></div>
                        @endif
                        @php
                            $seat = $baris . "-" . $kursi;
                        @endphp
                       @if (in_array($seat, $seatsFormat))
                            <div style="background:#e4e4e4; border-radius: 10px; width: 45px; height: 45px; cursor:pointer" class="p-2 mx-1 text-black text-center">
                                <span style="font-size: 12px" >{{ $baris }}-{{ $kursi }}</span>
                            </div>
                        @else
                        <div style="background:#112646; border-radius: 10px; width: 45px; height: 45px; cursor:pointer" class="p-2 mx-1 text-white text-center " onclick="selectedSeat('{{ $schedule->price }}', '{{ $baris }}', '{{ $kursi }}', this)">
                            <span style="font-size: 12px" >{{ $baris }}-{{ $kursi }}</span>
                        </div>
                        @endif
                    @endforeach
            </div>
            @endforeach
        </div>
    </div>

    <div class="w-100 p-2 bg-light text-center fixed-bottom" id="wrapBtn">
        <b class="text-center p-3">LAYAR BIOSKOP</b>
        <div class="row" style="border: 1px solid #d1d1d1">
            <div class="col-6 text-center" style="border: 1px solid #d1d1d1;">
                <p>Total Harga</p>
                <h5 id="totalPrice">Rp. -</h5>
            </div>
            <div class="col-6 text-center" style="border: 1px solid #d1d1d1;">
                <p>Kursi Dipilih</p>
                <h5 id="selectedSeats">-</h5>
            </div>
        </div>
        {{-- Menyimpan value yang diperlukan untuk aksi ringkasan pemesanan --}}
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" id="user_id">
        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}" id="schedule_id">
        <input type="hidden" name="hour" value="{{ $hour}}" id="hour">
        <div style="color: black; font-weight: bold; cursor: not-allowed;" class="w-100 text-center" id="btnOrder">RINGKASAN PEMESANAN</div>
    </div>
@endsection

@push('script')
<script>
    //array data kursi yang sudah Dipilih
    let seats = [];
    let totalPriceData = null;
    function selectedSeat(price, row, col, el){
        //membuat A-1 sesuai row dan col yang Dipilih
        let seatItem = row + "-" + col;
        // cek apakag kursi ini sudah ada di array seats
        let indexSeat = seats.indexOf(seatItem);
        // jika ada akan muncul index nnua jika nga ada -1
        if (indexSeat == -1){
            //kalau gaada simpen kursi yang dipilih ke array
            seats.push(seatItem);
            //kasi wana biru muda ke elemen yang dipilih
            el.style.background = "#3e85ef";
        }else {
            //kalau ada di array artinya klik kali ini untuk membatalkan pilihan (klikan ke 2 pada kursi )
            seats.splice(indexSeat, 1); //hapus item dari array
            //kemvbalikan warna biru ke biru tua
            el.style.background = "#112646";
        }
        //menghitung total harga sesuai dengan kursi yang Dipilih
        let totalPrice = price * (seats.length); //seats.lenght : jumlah item array
        totalPriceData = totalPrice;
        let totalPriceEl = document.querySelector('#totalPrice');
        totalPriceEl.innerText = "Rp " + totalPrice.toLocaleString('id-ID');
        //memunculkan daftar kursi yang dipilih
        let selectedSeatsEl = document.querySelector('#selectedSeats');
        //seats.join(", ") mengubah array jadi string, dipisahkan dengan tanda tertentu
        selectedSeatsEl.innerText = seats.join(", ");

        // Jika seats nya lebih dari 1 atau = 1 aktifkan order dan tambahkan function onclick
        // Jika seats yang dipilih kurang dari 1 maka akan balik ke awal lagi asek😎😎
        if(seats.length > 0){
            let btnOrder = document.querySelector('#btnOrder');
            btnOrder.style.background = '#112646';
            btnOrder.style.color = 'white';
            btnOrder.style.cursor = 'pointer';
            btnOrder.onclick = createTicketData;
        } else{
            btnOrder.style.background = '';
            btnOrder.style.color = '';
            btnOrder.style.cursor = '';
            btnOrder.onclick = null;
        }

       function createTicketData(){
        // AJAX : jika mau akses data ke server melalui js gunakan method ajax({}), bisa digunakan melalui jQuery($)
        $.ajax ({
            //routing untuk akses data
            url: "{{ route('tickets.store') }}",
            //http method
            method:"POST",
            //Data yang akan dikirim diambil dari( request, $request)
            data: {
                // csrf token
                _token: "{{ csrf_token() }}",
                user_id: $('#user_id').val(),
                schedule_id: $('#schedule_id').val(),
                row_of_seats: seats,
                quantity: seats.length,
                total_price: totalPriceData,
                hour: $('#hour').val(),


            },
            success: function(response){
                // console.log(response);
                // Jika beerhasil maka arahkan ke /tickets/order
                let ticketId = response.data.id;
                window.location.href = `/tickets/${ticketId}/order`;
            },
            error: function(message){
                console.log(message);
                alert("Terjadi kesalahan ketika membuat data ticket");
            }
          })
       }
    }
</script>
@endpush
