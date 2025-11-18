<?php

namespace App\Http\Controllers;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Exports\ScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movie = Movie::all();
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedule.index', compact('cinemas', 'movie', 'schedules'));
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
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i',
        ], [
            'cinema_id.required' => 'Bioskop harus di isi',
            'movie_id.required' => 'File harus dipilih',
            'price.required' => 'Harga harus di isi',
            'price.numeric' => "Harga harus diisi dengan angka",
            'hours. * .required ' => 'Jam tayang harus diisi minimal satu data',
            'hours. * .date_format ' => 'Jam tayang harus diisi dengan jam:menit',
        ]);

        $hours = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->value('hours');
        $hoursBefore = $hours ?? [];
        $mergeHours = array_merge($hoursBefore, $request->hours);
        $newHours = array_unique($mergeHours);

        $createData = Schedule::updateOrCreate([
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request -> movie_id,
        ], [

            'price' => $request -> price,
            'hours' => $newHours,
        ]);
        if($createData){
         return redirect()->route('staff.schedule.index')->with('success', 'Berhasil manambahkan data!');
        }else{
         return redirect()->back()->with('error', 'Gagal coba lagi!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i',
        ],[
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang harus diisi minimal satu data',
            'hours.*.date_format' => 'Jam tayang harus diisi dengan jam:menit'
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => array_unique($request->hours)
        ]);
        if ($updateData){
            return redirect()->route('staff.schedule.index')->with('success', 'Data berhasil di perbarui');
        }else {
            return redirect()->back()->with('error', 'Data gagal diperbarui');
        }

    }

    /**
     * Remove the specified resource from storage.
     */

    public function exportExcel()
{
    $fileName = 'data-jadwal.csv';
    return Excel::download(new ScheduleExport, $fileName);
}



  public function destroy($id)
    {
        $schedule = Schedule::where('id', $id)->delete();

        if ($schedule) {
            return redirect()->route('staff.schedule.index')->with('success', 'Berhasil menghapus data');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        }


    }

    public function trash(){

        //onlyTrashed mengambil data yg sdh di hps
        $schedule = Schedule::onlyTrashed()->with(['cinema', 'movie'])->get();
        return view('staff.schedule.trash', compact('schedule'));

    }

    public function restore($id){
         $schedule = Schedule::onlyTrashed()->find($id);
         $schedule->restore();
         return redirect()->route('staff.schedule.index')->with('success', 'Berhasil!!');
    }

    public function deletepermanent($id){
       $schedule = Schedule::onlyTrashed()->find($id);
       $schedule->forceDelete();
       return redirect()->route('staff.schedule.index')->with('success', 'Berhasil!!');
    }

     public function dataForDatatables()
    {
        //siapkan query eloquent dari model movie
        $schedule = Schedule::with(['cinema', 'movie'])->get();
        //datatables :: of($movies) : menyiapkan data untuk Datatables, data diambil dari $movies
        return DataTables::of($schedule)
        ->addIndexColumn() //memberikan nomor 1,2,dst di column table
        //addColumn(): menambahkan data selain dari table movies, digunakan untuk button aksi dan data yang perlu di manipulasi

        ->addColumn('movieTitle', function($data){
          return $data->movie->title;
        })
        ->addColumn('cinemaTitle', function($data){
          return $data->cinema->name;
        })
        ->addColumn('price', function ($data) {
                return '<span>Rp. ' . number_format($data->price, 0, ',', '.') . '</span>';
            })
        ->addColumn('hours', function ($data) {
                $list = '';

                foreach ($data->hours as $hour) {
                    $list .= '<li class="ms-4">' . $hour . '</li>';
                }

                return $list;
        })

        ->addColumn('buttons', function($data){
            $btnEdit = ' <a href="'. route('staff.schedule.edit', $data->id) . '" class="btn btn-primary">Edit</a>';
            $btnDelete = ' <form class = "me-2" action="' .  route('staff.schedule.delete', $data->id) .'" method="POST">' .
                                csrf_field() .
                                method_field('DELETE') .
                                '<button type="submit" class="btn btn-danger">Hapus</button>
                            </form>';

            return '<div class="d-flex justify-content-center gap-2">'  . $btnEdit . $btnDelete .  '</div>';
        })
        ->rawColumns(['cinemaTitle', 'movieTitle', 'price' , 'hours', 'buttons',])
        ->make(true);
}
}
