<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Promo;
use App\Models\Ticket;
use App\Models\TicketPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticketActive = Ticket::whereHas('ticketPayment', function($q){
            $q->whereDate('booked_date', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        // <> operator sql untuk yang tidak sebanding
        $ticketNonActive = Ticket::whereHas('ticketPayment', function($q){
            $q->whereDate('booked_date',  '<' ,now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        // dd($ticketActive);
        // dd($ticketNonActive);

        return view('ticket.index', compact(['ticketActive', 'ticketNonActive']));
    }

    public function chartData() {
        $month = now()->format('m');
        $tickets = Ticket::whereHas('ticketPayment', function($q) use($month){
            $q->whereMonth('booked_date', $month)->where('paid_date', '<>', NULL);
        })->get()->groupBy(function($ticket){
            return \Carbon\Carbon::parse($ticket['ticketPayment']['booked_date'])->format('Y-m-d');
        })->toArray();
        // $movies = Movie::where('actived', 1)->get()->toArray();
        // dd($tickets);
        // ambil data key/index
        $labels = array_keys($tickets);
        // array yang meinyimpan data jumlah pembelian tiap tanggal
        $data = [];
        foreach($tickets as $ticket){
            // simpan hasil perhitungan count() dari $ticket. $ticket berbentuk array data-data yg dibeli ditgl tertentu
            array_push($data, count($ticket));
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'rows_of_seats' => 'required',
            'quantity' => 'required',
            'total_price' => 'required',
            'hour' => 'required'
        ]);

        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'quantity' => $request->quantity,
            'rows_of_seats' => $request->rows_of_seats,
            'total_price' => $request->total_price,
            'actived' => 1,
            'service_fee' => 4000 * $request->quantity,
            'hour' => $request->hour
        ]);

        return response()->json([
            'message' => 'berhasil',
            'data' => $createData
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])->first();
        return view('schedule.receipt', compact(['ticket']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }

    public function showSeats($scheduleId, $hourId)
    {
        $schedule = Schedule::where('id', $scheduleId)->with(['cinema', 'movie'])->first();
        // dd($schedule['movie']);
        $hour = $schedule['hours'][$hourId] ?? '';
        // dd($hour);
        $seats = Ticket::whereHas('ticketPayment', function($q){
            $q->whereDate('paid_date', now()->format('Y-m-d'));
        })->whereTime('hour', $hour)->pluck('rows_of_seats');
        // dd($seats);
        $seatsFormat = array_merge(...$seats);
        return view('schedule.show-seats', compact('schedule', 'hour', 'seatsFormat'));
    }

    public function ticketOrder($ticketId)
    {
        // with disesuaikan dengan relasi
        $ticket = Ticket::where('id', $ticketId)->with(['schedule.cinema', 'schedule.movie'])->first();
        $promos = Promo::where('actived', 1)->get();
        // dd($ticket);
        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function createBarcode(Request $request, $ticketId)
    {
        $barcodeKode = 'TICKET' . $ticketId . rand(1, 10);

        $qrImage = QrCode::format('svg')->size(300)->margin(2)->generate($barcodeKode);

        $fileName = $barcodeKode . '.svg';

        $path = 'barcodes/' . $fileName;

        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $ticketId,
            'barcode' => $path,
            'status' => 'process',
            'booked_date' => now()
        ]);

        if ($request->promo_id != NULL) {
            $ticket = Ticket::find($ticketId);
            $promo = Promo::find($request->promo_id);
            if ($promo && $promo['type'] == 'percent') {
                $totalPrice = $ticket['total_price'] - ($ticket['total_price'] * $promo['discount'] / 100);
            } else {
                $totalPrice = $ticket['total_price'] - $promo['discount'];
            };
            $ticket->update(['promo_id' => $request->promo_id, 'total_price' => $totalPrice]);
        }
        return response()->json(['message' => 'Berhasil!', 'data' => $createData]);
    }

    public function paymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['promo', 'ticketPayment'])->first();
        return view('schedule.payment', compact('ticket'));
    }

    public function proofPayment($ticketId)
    {
        $updateData = TicketPayment::where('ticket_id', $ticketId)->update([
            'paid_date' => now()
        ]);
        return redirect()->route('tickets.fajar', $ticketId);
    }

    public function exportPdf($ticketId)
    {
        $ticket =Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])->first()->toArray();

        view()->share('ticket', $ticket);

        $pdf = Pdf::loadView('schedule.pdf', $ticket);

        $fileName = 'TICKET' . $ticket['id'] . '.pdf';
        return $pdf->download($fileName);
    }


}
