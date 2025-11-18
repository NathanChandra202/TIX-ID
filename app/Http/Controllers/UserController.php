<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{


    public function store(Request $request) //class yang digunakna untuk mengambil value req, form(post/get)
    {
        $request->validate(
            [
                'first_name' => 'required|min:3',
                'last_name' => 'required|min:3',
                'email' => 'required|email:dns',
                'password' => 'required|min:8',
            ],
            [
                'first_name.required' => 'Nama depan harus diisi',
                'first_name.min' => 'Nama depan harus berisi minimal 3 karakter',
                'last_name.required' => 'Nama belakang harus diisi',
                'last_name.min' => 'Nama belakang harus berisi 3 karakter',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Email harus diisi dengan data valid',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password harus berisi 8 karakter unik',
            ],
        );

        $createUser = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);
        if ($createUser) {
            // redirect() = move page, route() = rute, with() = mengirin data session, biasanya notif
            return redirect()->route('login')->with('ok', 'Berhasil membuat akun');
        } else {
            // back() = kembali
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        };
    }
    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required',
                'password' => 'required'
            ],
            [
                'email.required' => 'Email wajib diisi',
                'password.required' => 'Password wajib diisi'
            ]
        );

        $data = $request->only(['email', 'password']);


        if (Auth::attempt($data)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Login Berhasil dilakukan!');
            }elseif(Auth::user()->role == 'staff'){
                return redirect()->route('staff.promos.index')->with('login', 'Login Berhasil dilakukan!');
            } else {
                return redirect()->route('home')->with('success', 'Login berhasil dilakukan!');
            }
        } else {
            return redirect()->back()->with('error', 'Gagal login! Coba lagi');
        };
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda berhasil logout! Silahkan login kembali untuk akses lengkap');
    }

    public function index()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns|unique:users,email',
            'password' => 'required|min:8'
        ], [
            'name.required' => 'Nama wajib diisi!',
            'name.min' => 'Jumlah nama minimal 8 karakter!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus diisi dengan data valid',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password harus berisi 8 karakter unik',
        ]);

        $createUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);

        if ($createUser) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil Menambahkan Staff!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        }
    }

    public function edit($id)
    {
        $user = User::Find($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'nullable|min:8'
        ], [
            'name.required' => 'Nama wajib diisi!',
            'name.min' => 'Jumlah nama minimal 3 karakter!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus diisi dengan data valid',
            'password.min' => 'Password harus berisi minimal 8 karakter unik',
        ]);

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // $user->role = 'staff';

        if ($user->save()) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil mengubah data');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        }
    }

    // public function storeStaff(Request $request)
    // {
    //     $request->validate(['name' => 'required|min:3', 'email' => 'required|email:dns', 'password' => 'required|min:8'], ['name.required' => 'Nama wajib diisi!', 'name.min' => 'Jumlah nama minimal 8 karakter!', 'email.required' => 'Email wajib diisi!', 'email.email' => 'Email harus diisi dengan data valid', 'password.required' => 'Password harus diisi', 'password.min' => 'Password harus berisi 8 karakter unik',]);
    //     $createUser = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'role' => 'staff']);
    //     if ($createUser) {
    //         return redirect()->route('admin.users.index')->with('success', 'Berhasil Menambahkan Staff!');
    //     } else {
    //         return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
    //     }
    // }

    public function destroy($id)
    {
        $user = User::where('id', $id)->delete();

        if ($user) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil menghapus data');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        }
    }

    public function exportExcel()
    {
        $file_name = 'data-user.xlsx';
        return \Excel::download(new \App\Exports\UserExport, 'user.xlsx');
    }

            public function trash()
        {
            // Ambil semua data yang sudah dihapus (soft delete)
            $user = User::onlyTrashed()->get();
            return view('admin.users.trash', compact('user'));
        }

        public function restore($id)
        {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            return redirect()->route('admin.users.index')->with('success', 'Berhasil dikembalikan!');
        }

        public function deletepermanent($id)
        {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->forceDelete();
            return redirect()->route('admin.users.index')->with('success', 'Berhasil dihapus permanen!');
        }

         public function dataForDatatables()
    {
        //siapkan query eloquent dari model movie
        $user = User::whereIn('role' , ['admin', 'staff'])->get();
        //datatables :: of($movies) : menyiapkan data untuk Datatables, data diambil dari $movies
        return DataTables::of($user)
        ->addIndexColumn() //memberikan nomor 1,2,dst di column table
        //addColumn(): menambahkan data selain dari table movies, digunakan untuk button aksi dan data yang perlu di manipulasi
        ->addColumn('role', function($data){
              if ($data->role == 'admin'){
                return  '<div class="badge text-bg-info">' . $data->role . '</div>';
              }
             else{
                  return  '<div class="badge text-bg-success">' . $data->role . '</div>';
             }

        })
    ->addColumn('buttons', function($data){
        $btnEdit = ' <a href="'. route('admin.users.edit', $data->id) . '" class="btn btn-primary">Edit</a>';
        $btnDelete = ' <form class = "me-2" action="' .  route('admin.users.delete', $data->id) .'" method="POST">' .
                            csrf_field() .
                            method_field('DELETE') .
                            '<button type="submit" class="btn btn-danger">Hapus</button>
                        </form>';

 return '<div class="d-flex justify-content-center gap-2">'  . $btnEdit . $btnDelete .  '</div>';
    })
    ->rawColumns(['buttons', 'role'])
    ->make(true);
}
};
