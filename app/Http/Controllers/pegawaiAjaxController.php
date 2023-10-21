<?php

namespace App\Http\Controllers;

use App\Models\pegawai;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Yajra\DataTables\Facades\DataTables;

// use Yajra\DataTables\Contracts\DataTable;

class pegawaiAjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = pegawai::orderBy("nama", "asc");
        // memanggil data dari phpmyadmin
        return DataTables::of($data)->addIndexColumn()
            //* memberikan column tambahan secara manual, yang isinya tombol
            ->addColumn('aksi', function ($data) {
                // mengoper $data dari DataTables
                return view("pegawai.tombol")->with("data", $data);
            })->make(true);
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
        //* Validasi Versi lama
        // $data = $request->validate([
        //     'nama' => 'required|max:255',
        //     'email' => 'required|email|unique:pegawai',
        // ]);

        $validasi = FacadesValidator::make($request->all(), [
            "nama" => "required",
            "email" => "required|email|unique:pegawai",
        ], [
            "nama.required" => "Nama harus di isi",
            "email.required" => "Email harus di isi",
            "email.email" => "Format Email harus benar",
            "email.unique:pegawai" => "Email telah terdaftar",
        ]);

        if ($validasi->fails()) {
            // return "test";
            return response()->json(["errors" => $validasi->errors()])->setStatusCode(400);
        } else {
            $data = [
                "nama" => $request->nama,
                "email" => $request->email,
            ];
            pegawai::create($data);
            return response()->json(["success" => "berhasil menyimpan data"]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pegawai $pegawaiAjax)
    {
        return $pegawaiAjax;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pegawai $pegawaiAjax)
    {
        $validasi = $request->validate([
            "nama" => "required",
            "email" => "required|email"
        ]);
        // melakukan pengecekan
        if ($request->email != $pegawaiAjax->email) {
            $validasi['email'] = ['required', 'unique:pegawai', "email"];
        };

        $data = [
            "nama" => $request->nama,
            "email" => $request->email,
        ];
        pegawai::where("id", $pegawaiAjax->id)->update($data);
        return response()->json(["success" => "berhasil update data"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pegawai $pegawaiAjax)
    {
        pegawai::destroy($pegawaiAjax->id);
    }
}
