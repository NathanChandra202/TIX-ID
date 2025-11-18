<?php

namespace App\Http\Controllers;

use App\Exports\MovieExport;
use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
// use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{

    public function home()
    {
        //format pencarian where(column, operator, value)
        //jila operator ==/= operator bisa TIDAK DIGUNAKAN
        //operator yang digunakan : < | > | <>
        // where('actived', <> , 1) tidak sama dengan satu contoh
        // format mengurutkan data orderBy('column', 'DESC/ASC')
        // get() mengambil seluruh data filterisasi
        // first() mengambil satu data saja
        // limit() mengambil data dengan jumlah tertentu
        $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->limit(4)->get();

        return view('home', compact('movies'));
    }

    public function homeAllMovie(Request $request)
    {
        // $search = $request->input('search');

        // $movies = Movie::where('actived', 1)->when($search, function ($query, $search) {
        //     $query->where('title', 'like', "%{$search}%")
        //         ->orWhere('genre', 'like', "%{$search}%")
        //         ->orWhere('director', 'like', "%{$search}%");
        // })->orderBy('created_at', 'DESC')->get();

        // Mengambil data input dari name="search_movie"


        $search = $request->search_movies;

        if ($search != "") {
            // operator LIKE = mencari data yang mirip/mengndung kata tertentu
            // % digunakan untuk mengaktifkan LIKE
            // %kata = kata depan, kata% = kata belakang, %kata% = mencari kata depan, tengah, belakang

            $movies = Movie::where('title', 'LIKE', '%' . $search . '%')->where('actived', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('movies', compact('movies', 'search'));
    }



    public function movieSchedule($movie_id, Request $request)
    {
        // request megambil href=?
        $sortPrice = $request['sort-price'];

        $searchCinema = $request['search-cinema'];

        if ($sortPrice) {
            // karena mau mengurutkan berdasarkan prive yang ada di schedules, maka sorting orderBy() disimpan di relasi with schedules
            // use() mengirimkan variabel dari luar
            $movie = Movie::where('id', $movie_id)->with(['schedules' => function ($q) use ($sortPrice) {
                // $q mewakilkan model Schedule
                // 'schedules' => function($e) (...) : melakukan filter/menjalankan elequent didalam relasi
                $q->orderBy('price', $sortPrice);
            }, 'schedules.cinema'])->first();
        } else {
            // mengambil realsi didalam relasi
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
        }


        // if ($searchCinema) {
        //     whereHas('namarelasi') = mengambil data utama hanya jika memiliki relasi ini
        //     whereHas('namarelasi', function($q) {....}) = mengambil data utma hanua jika memiliki relasi ini dan data pada relasinua memiliki kriteria tertentu
        //     $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->whereHas('schedules', function($q) use ($searchCinema){
        //         ambil daata movie yang memiliki data schedules dan data schedules bagian 'cinema_id' nua sesiao demham $searchCinema
        //         $q->where('cinema_id', $searchCinema);
        //     })->first();
        // }

        if ($searchCinema) {
            $movie->schedules = $movie->schedules->where('cinema_id', $searchCinema)->values();
        }
        // sort alphabet
        // elequent =
        // collection =
        $sortAlphabet = $request['sort-alphabet'];
        if ($sortAlphabet == 'ASC') {
            // karena mau mengurutkan berdasarkan prive yang ada di schedules, maka sorting orderBy() disimpan di relasi with schedules
            // use() mengirimkan variabel dari luar
            $movie->schedules = $movie->schedules->sortBy(function ($schedule) {
                return $schedule->cinema->name;
            })->values();
        } elseif ($sortAlphabet == 'DESC') {
            // mengambil realsi didalam relasi
            $movie->schedules = $movie->schedules->sortByDesc(function ($schedule) {
                return $schedule->cinema->name;
            })->values();
        }
        // sort location
        $listCinema = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();



        return view('schedule.detail-film', compact('movie', 'listCinema'));
    }

    public function index()
    {
        $movies = Movie::all();
        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        return view('admin.movies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            'poster' => 'required|mimes:jpeg,jpg,png,svg,webp',
            'description' => 'required|min:15'
        ], [
            'title.required' => 'Judul film wajib diisi!',
            'genre.required' => 'Genre film wajib diisi!',
            'director.required' => 'Director film wajib diisi!',
            'age_rating.required' => 'Minimal usia wajib diisi!',
            'age_rating.numeric' => 'Minimal usia wajib diisi dengan angka',
            'poster.required' => 'Poster film wajib diisi!',
            'poster.mimes' => 'File hanya boleh bertipe JPG/JPEG/PNG/SVG/WEBP',
            'description.required' => 'Sinopsis film wajib diisi'
        ]);

        $poster = $request->file('poster');
        $namaPoster = Str::random(5) . "-poster." . $poster->getClientOriginalExtension();
        $path = $poster->storeAs('movies', $namaPoster, 'public');

        $createMovies = Movie::create([
            'title' => $request->title,
            'genre' => $request->genre,
            'duration' => $request->duration,
            'description' => $request->description,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path,
            'actived' => 1,
        ]);

        if ($createMovies) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    public function show(Movie $movie)
    {
        //
    }

    public function edit($id)
    {
        $movie = Movie::findOrFail($id);
        return view('admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            'poster' => 'mimes:jpeg,jpg,png,svg,webp',
            'description' => 'required|min:15'
        ], [
            'title.required' => 'Judul film wajib diisi!',
            'genre.required' => 'Genre film wajib diisi!',
            'director.required' => 'Director film wajib diisi!',
            'age_rating.required' => 'Minimal usia wajib diisi!',
            'age_rating.numeric' => 'Minimal usia wajib diisi dengan angka',
            'poster.mimes' => 'File hanya boleh bertipe JPG/JPEG/PNG/SVG/WEBP',
            'description.required' => 'Sinopsis film wajib diisi'
        ]);

        $movie = Movie::findOrFail($id);

        if ($request->hasFile('poster')) {
            if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
                Storage::disk('public')->delete($movie->poster);
            }
            $poster = $request->file('poster');
            $namaPoster = Str::random(5) . "-poster." . $poster->getClientOriginalExtension();
            $path = $poster->storeAs('movies', $namaPoster, 'public');
            $movie->poster = $path;
        }

        $movie->title = $request->title;
        $movie->genre = $request->genre;
        $movie->duration = $request->duration;
        $movie->description = $request->description;
        $movie->director = $request->director;
        $movie->age_rating = $request->age_rating;
        $movie->actived = 1;

        if ($movie->save()) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil diubah');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    public function patch($id)
    {
        $movies = Movie::findOrFail($id);
        if ($movies->actived == 1) {
            $movies->actived = 0;
            $movie = $movies->save();

            if ($movie) {
                return redirect()->route('admin.movies.index')->with('success', 'Film berhasil dinonaktifkan');
            } else {
                return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
            }
        } else {
            $movies->actived = 1;
            $movie = $movies->save();

            if ($movie) {
                return redirect()->route('admin.movies.index')->with('success', 'Film berhasil diaktifkan');
            } else {
                return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
            }
        }
    }


    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $schedules = Schedule::where('movie_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.movies.index')->with('error', 'Tidak dapat menghapus data bioskop! Data tertaut fengan jadwal tayang');
        }

        if ($movie->delete()) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }
    public function exportExcel()
    {
        $file_name = 'Movie_list' . '.xlsx';
        return Excel::download(new MovieExport, $file_name);
    }

    public function deletePermanent($id)
    {
        $deleteMovie = Movie::where('id', $id)->forceDelete();
        if ($deleteMovie) {
            return redirect()->route('admin.movies.trash')->with('success', 'Data berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    public function restore($id)
    {
        $movie = Movie::withTrashed()->where('id', $id)->restore();
        if ($movie) {
            return redirect()->route('admin.movies.index')->with('success', 'Data berhasil dipulihkan!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }
    public function trash()
    {
        // onlyTrashed() mengambil data yang sudah dihapus dengan fungsi soft delete/ destroy
        $movies = Movie::onlyTrashed()->get();
        return view('admin.movies.trash', compact('movies'));
    }
    public function dataForDatatables()
    {
        // siapkan query elequent dari model movies
        $movies = Movie::query();
        return DataTables::of($movies)
            ->addIndexColumn()
            ->addColumn('imgPoster', function ($data) {
                $urlImage = asset('storage') . '/' . $data->poster;
                return '<img src="' . $urlImage . '" width="90px">';
            })
            ->addColumn('activeBadge', function ($data) {
                if ($data->actived == 1) {
                    return '<div class="badge bg-success">Aktif</div>';
                } else {
                    return '<div class="badge bg-danger">Non-Aktif</div>';
                }
            })
            ->addColumn('buttons', function ($data) {
                $jsonData = e(json_encode($data));
                $btnDetail = '<button onclick="showModal(' . $jsonData . ')" class="btn btn-secondary">Detail</button>';
                $btnEdit = '<a href="' . route('admin.movies.edit', $data->id) . '" class="btn btn-primary">Edit</a>';
                $btnDelete = '<form action="' . route('admin.movies.delete', $data->id) . '" method="POST">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger">Hapus</button></form>';
                $btnNonAktif = '';
                $btnAktif = '';
                if ($data->actived == 1) {
                    $btnNonAktif = '<form action="' . route('admin.movies.patch', $data->id) . '" method="POST">' .
                        csrf_field() .
                        method_field('PATCH') .
                        '<button type="submit" class="btn btn-warning">Non-Aktifkan Film</button>' .
                        '</form>';
                } else {
                    $btnAktif = '<form action="' . route('admin.movies.patch', $data->id) . '" method="POST">' .
                        csrf_field() .
                        method_field('PATCH') .
                        '<button type="submit" class="btn btn-warning">Aktifkan Film</button>' .
                        '</form>';
                }
                return '<div class="d-flex justify-content-center gap-3">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . $btnAktif . '</div>';
            })
            ->rawColumns(['imgPoster', 'activeBadge', 'buttons'])
            ->make(true);
    }

    public function chartData() {
        $moviesActive = Movie::where('actived', 1)->count();
        $moviesNonActive = Movie::where('actived', 0)->count();
        $labels = ['Film Aktif', 'Film Tidak Aktif'];
        $data = [$moviesActive, $moviesNonActive];
        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
