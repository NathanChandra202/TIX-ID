<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Psy\CodeCleaner\ReturnTypePass;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::where('actived', 1)->get();

        return view('staff.promo.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|min:5',
            'type' => 'required',
            'discount' => 'required'
        ], [
            'promo_code.required' => 'Kode Promo Wajib Di-isi!',
            'promo_code.min' => 'Kode Promo Wajib berisi minimal 5 karakter!',
            'type.required' => 'Tipe Diskon Wajib Di-isi!',
            'discount.required' => 'Jumlah Potongan Wajib Di-isi!'
        ]);

        if($request->type == 'percent' && $request->discount > 100){
            return redirect()->route('staff.promos.create')->with('error', 'Gagal! , Diskon tidak boleh lebih dari 100%');
        }

        if($request->type == 'rupiah' && $request->discount < 1000){
            return redirect()->route('staff.promos.create')->with('error', 'Gagal! , Diskon minimal 1000');
        }



        $createPromo = Promo::create([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'actived' => 1
        ]);

        if($createPromo){
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil Membuat kode Promo!');
        }else{
            return redirect()->back()->with('error', 'Gagal! Silahkan Coba Lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promo = Promo::find($id);
        return view('staff.promo.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'promo_code' => 'required|min:5',
            'type' => 'required',
            'discount' => 'required'
        ], [
            'promo_code.required' => 'Kode Promo Wajib Di-isi!',
            'promo_code.min' => 'Kode Promo Wajib berisi minimal 5 karakter!',
            'type.required' => 'Tipe Diskon Wajib Di-isi!',
            'discount.required' => 'Jumlah Potongan Wajib Di-isi!'
        ]);

        $updatePromo = Promo::where('id', $id)->update([
            'promo_code' => $request->promo_code,
            'type' => $request->type,
            'discount' => $request->discount,
            'actived' => 1
        ]);

        if($updatePromo){
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil Membuat kode Promo!');
        }else{
            return redirect()->back()->with('error', 'Gagal! Silahkan Coba Lagi');
        }
    }

    public function patch($id){
        $promos = Promo::findOrFail($id);
        if($promos->actived == 1){
            $promos->actived = 0;
            $movie = $promos->save();

            if($movie){
                return redirect()->route('staff.promos.index')->with('success', 'Film berhasil dinonaktifkan');
            }else{
                return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
            }
        }else{
            $promos->actived = 1;
            $movie = $promos->save();

            if($movie){
                return redirect()->route('staff.promos.index')->with('success', 'Film berhasil diaktifkan');
            }else{
                return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $promo = Promo::where('id', $id)->delete();

        if ($promo) {
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil menghapus data');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        }


    }

    public function exportExcel()
    {
        $file_name = 'data-promo.xlsx';
        return \Excel::download(new \App\Exports\PromoExport, 'promo.xlsx');
    }

        public function trash(){

        //onlyTrashed mengambil data yg sdh di hps
        $promo = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promo'));

    }

    public function restore($id){
         $promo = Promo::onlyTrashed()->find($id);
         $promo->restore();
         return redirect()->route('staff.promos.index')->with('success', 'Berhasil!!');
    }

    public function deletepermanent($id){
       $promo = Promo::onlyTrashed()->find($id);
       $promo->forceDelete();
       return redirect()->route('staff.promos.index')->with('success', 'Berhasil!!');
    }


      public function dataForDatatables()
    {
        //siapkan query eloquent dari model movie
        $promo = Promo::query()->get();
        //datatables :: of($movies) : menyiapkan data untuk Datatables, data diambil dari $movies
        return DataTables::of($promo)
        ->addIndexColumn() //memberikan nomor 1,2,dst di column table
        //addColumn(): menambahkan data selain dari table movies, digunakan untuk button aksi dan data yang perlu di manipulasi
        ->addColumn('discount', function($data){
       if ($data->type == 'rupiah'){
        return '<span>Rp. '.number_format($data->discount, 0, ',', '.').'</span>';
       }else{
         return '<span> ' .  $data->discount . '%</span>';
       }
        })
    ->addColumn('activedBadge', function($data) {
        //membuat data activedBadge yang akan mengembalikan badge warna sesuai status
        if ($data->actived == 1) {
            return '<span class="badge badge-success">Aktif</span>';
        } else {
            return '<span class="badge badge-secondary">Non-Aktif</span>';
        }
    })

    ->addColumn('buttons', function($data){
        $btnEdit = ' <a href="'. route('staff.promos.edit', $data->id) . '" class="btn btn-primary">Edit</a>';
        $btnDelete = ' <form class = "me-2" action="' .  route('staff.promos.delete', $data->id) .'" method="POST">' .
                            csrf_field() .
                            method_field('DELETE') .
                            '<button type="submit" class="btn btn-danger">Hapus</button>
                        </form>';

 return '<div class="d-flex justify-content-center gap-2">'  . $btnEdit . $btnDelete .  '</div>';
    })
    ->rawColumns(['buttons', 'discount', 'activedBadge'])
    ->make(true);
}
};

